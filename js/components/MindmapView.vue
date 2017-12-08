<!--
@copyright Copyright (c) 2017 Kai Schröer <git@schroeer.co>

@author Kai Schröer <git@schroeer.co>

@license GNU AGPL version 3 or any later version

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
-->

<template>
	<div>
		<div id="app-content-wrapper">
			<div class="popovermenu bubble">
				<ul>
					<li>
						<a href="#" @click="showRename">
							<span class="icon-rename"></span>
							<span>{{ t('Edit') }}</span>
						</a>
					</li>
					<li>
						<a href="#" @click="remove">
							<span class="icon-delete"></span>
							<span>{{ t('Delete') }}</span>
						</a>
					</li>
				</ul>
			</div>
			<div id="mindmap" class="loading"></div>
		</div>
		<app-sidebar :mindmap="mindmap"></app-sidebar>
	</div>
</template>

<script lang="ts">
	import {Component, Vue} from 'vue-property-decorator';
	import * as _ from 'lodash';
	import * as vis from 'vis';
	import AppSidebar from './AppSidebar.vue';
	import MindmapNodeService from '../services/MindmapNodeService';
	import MindmapNode from '../models/MindmapNode';
	import MindmapService from '../services/MindmapService';
	import Mindmap from '../models/Mindmap';

	@Component({
		components: {
			'app-sidebar': AppSidebar
		}
	})
	export default class MindmapView extends Vue {
		private mindmapNodeService: MindmapNodeService;
		// @ts-ignore
		private mindmap: Mindmap = new Mindmap();

		created(): void {
			const id = parseInt(this.$route.params.id);
			this.mindmapNodeService = new MindmapNodeService();

			const mindmapService = new MindmapService();
			mindmapService.load().then(() => {
				$('#mindmap').removeClass('loading');
				const mindmap = mindmapService.find(id);
				if (!_.isNull(mindmap)) {
					this.mindmap = mindmap;
				}
			}).catch(error => {
				$('#mindmap').removeClass('loading');
				console.error('Error: ' + error.message);
			});

			this.mindmapNodeService.load(id).then(response => {
				const content = document.getElementById('app-content');
				const wrapper = document.getElementById('app-content-wrapper');
				const container = document.getElementById('mindmap');
				const options = {
					physics: {enabled: false},
					interaction: {dragNodes: false},
					locale: OC.getLocale()
				};
				const nodes: Array<MindmapNode> = response.data;
				const edges: Array<{from: number, to: number}> = [];

				if (!_.isNull(content) && !_.isNull(wrapper) && !_.isNull(container)) {
					// The mindmap should use all of the wrappers place.
					wrapper.style.height = content.clientHeight + 'px';
					container.style.height = wrapper.clientHeight + 'px';
				}

				nodes.forEach((val: MindmapNode) => {
					if (val.parentId !== null) {
						edges.push({from: val.parentId, to: val.id});
					}
				});

				if (!_.isNull(vis) && !_.isNull(container)) {
					const network = new vis.Network(container,
						{nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges)},
						options);
					network.fit();

					network.on('click', this.showPopover);
					network.on('doubleClick', this.showNew);
				} else {
					OC.dialogs.alert(t('mindmaps', 'The vis.js Framework is not available!'), t('mindmaps', 'Error'));
				}
			}).catch(error => {
				console.error('Error: ' + error.message);
			});
		}

		showPopover(params: any): void {
			const $popover = $('.popovermenu');
			if (params.nodes.length === 1) {
				$('#mindmap').data('selected', params.nodes[0]);

				$popover.addClass('open');
				$popover.css('top', params.pointer.DOM.y + 30);
				$popover.css('left', params.pointer.DOM.x - 60);
			} else {
				$popover.removeClass('open');
			}
		}

		showNew(params: any): void {
			const parentId: number = parseInt($('#mindmap').data('selected') as string);
			if (_.isNaN(parentId)) {
				OC.dialogs.alert(t('mindmaps', 'Please select a parent node first!'), t('mindmaps', 'Error'));
			} else {
				console.log('New node at: ' + params.pointer.DOM.y + ' / ' + params.pointer.DOM.x + ' / Parent: ' + parentId);
			}
		}

		showRename(): void {
			const mindmapNodeId: number = parseInt($('#mindmap').data('selected') as string);
			if (_.isNaN(mindmapNodeId)) {
				console.log('Edit node: ' + mindmapNodeId);
			}
		}

		remove(): void {
			const mindmapNodeId: number = parseInt($('#mindmap').data('selected') as string);
			this.mindmapNodeService.remove(mindmapNodeId).then(() => {
				console.log('Node deleted!');
			}).catch(error => {
				console.error('Error: ' + error.message);
			});
		}

	}
</script>

<style lang="scss">
	#app-content-wrapper {
		.popovermenu {
			width: 84px;
		}
	}
</style>
