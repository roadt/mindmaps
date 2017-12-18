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
						<form @submit.prevent="create">
							<input type="text" :placeholder="t('Edit mindmap node')" maxlength="255" v-model="title">
							<input type="button" value="" class="icon-close">
							<input type="submit" value="" class="icon-checkmark">
						</form>
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
	import {Component, Vue, Watch} from 'vue-property-decorator';
	import * as _ from 'lodash';
	import * as vis from 'vis';
	import AppSidebar from './AppSidebar.vue';
	import {MindmapService, MindmapNodeService} from '../services';
	import {Mindmap, MindmapNode} from '../models';

	@Component({
		components: {
			'app-sidebar': AppSidebar
		}
	})
	export default class MindmapView extends Vue {
		private mindmapService: MindmapService;
		private mindmapNodeService: MindmapNodeService;
		// @ts-ignore
		mindmap: Mindmap = new Mindmap();

		@Watch('$route.params.id', {deep: true})
		onMindmapIdChanged(id: number): void {
			this.loadMindmap(id);
		}

		created(): void {
			this.mindmapService = new MindmapService();
			this.mindmapNodeService = new MindmapNodeService();

			const id = parseInt(this.$route.params.id);
			this.loadMindmap(id);
		}

		loadMindmap(id: number): void {
			this.mindmapService.get(id).then(response => {
				$('#mindmap').removeClass('loading');
				if (!_.isNull(response.data)) {
					this.mindmap = response.data;
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
				const nodes: MindmapNode[] = response.data;
				const edges: {from: number, to: number}[] = [];

				if (!_.isNull(content) && !_.isNull(wrapper) && !_.isNull(container)) {
					// The mindmap should use all of the wrappers place.
					wrapper.style.height = content.clientHeight + 'px';
					container.style.height = wrapper.clientHeight + 'px';
				}

				nodes.forEach(node => {
					if (!_.isNull(node.parentId)) {
						edges.push({from: node.parentId, to: node.id});
					}
				});

				if (!_.isNull(vis) && !_.isNull(container)) {
					const network = new vis.Network(container,
						{nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges)},
						options);
					network.fit();

					network.on('click', this.showPopover);
					network.on('doubleClick', this.showPopover);
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

				const parentId = parseInt($('#mindmap').data('selected') as string);
				if (!_.isNaN(parentId)) {
					console.log('New node at: ' + params.pointer.DOM.y + ' / ' + params.pointer.DOM.x + ' / Parent: ' + parentId);
				}
			}
		}

		showRename(): void {
			const mindmapNodeId = parseInt($('#mindmap').data('selected') as string);
			if (!_.isNaN(mindmapNodeId)) {
				console.log('Edit node: ' + mindmapNodeId);
			}
		}

		create(): void {

		}

		update(): void {

		}

		remove(): void {
			const mindmapNodeId = parseInt($('#mindmap').data('selected') as string);
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
			width: 240px;
			padding: 4px 2px 4px 4px;

			form {
				display: inline-flex;
				width: 100%;

				input[type="text"] {
					width: 100%;
					min-width: 0;
					height: 38px;
					border-bottom-right-radius: 0;
					border-top-right-radius: 0;
					padding: 5px;
					margin-right: 0;
				}

				input:not([type="text"]) {
					width: 36px;
					height: 38px;
					flex: 0 0 36px;
				}

				input:not([type="text"]):not(:first-child) {
					margin-left: -1px;
				}

				input:not([type="text"]):not(:last-child) {
					border-radius: 0;
				}

				input:not([type="text"]):last-child {
					border-bottom-left-radius: 0;
					border-top-left-radius: 0;
					margin-left: -4px;
				}
			}
		}
	}
</style>
