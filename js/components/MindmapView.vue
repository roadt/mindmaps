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
			<div class="popovermenu bubble" style="display: none">
				<ul>
					<li><button class="node-rename icon-rename svg" :title="t('Edit Mindmap')"></button></li>
					<li><button class="node-delete icon-delete svg" :title="t('Delete Mindmap')"></button></li>
				</ul>
			</div>
			<div id="mindmap">{{ t('Mindmap loading…') }}</div>
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
		mindmap: Mindmap = new Mindmap();

		created(): void {
			const id = parseInt(this.$route.params.id);
			const mindmapService = new MindmapService();
			const mindmapNodeService = new MindmapNodeService();

			mindmapService.load().then(() => {
				const mindmap = mindmapService.find(id);
				if (!_.isNull(mindmap)) {
					this.mindmap = mindmap;
				}
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});

			mindmapNodeService.load(id).then((response) => {
				const content = document.getElementById('app-content');
				const wrapper = document.getElementById('app-content-wrapper');
				const container = document.getElementById('mindmap');
				const options = {
					physics: {enabled: false},
					interaction: {dragNodes: false},
					locale: OC.getLocale()
				};
				let nodes: Array<MindmapNode> = response.data;
				let edges: Array<{from: number, to: number}> = [];

				// TODO: Find a better solution here!
				if (content !== null && wrapper !== null && container !== null) {
					// The mindmap should use all of the wrappers place.
					wrapper.style.height = content.clientHeight + 'px';
					container.style.height = wrapper.clientHeight + 'px';
				}

				nodes.forEach((val: MindmapNode) => {
					if (val.parentId !== 0) {
						edges.push({from: val.parentId, to: val.id});
					}
				});

				if (vis !== null && container !== null) {
					const network = new vis.Network(container,
						{nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges)},
						options);
					network.fit();

					network.on('click', this.showPopover);
					network.on('doubleClick', this.showNew);
				}
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}

		showPopover(params: any): void {
			const $popover = $('.popovermenu');
			if (params.nodes.length === 1) {
				$popover.css('display', 'block');
				$popover.css('width', '72px');
				$popover.css('top', params.pointer.DOM.y + 30);
				$popover.css('left', params.pointer.DOM.x - 60);
			} else {
				$popover.css('display', 'none');
			}
		}

		showNew(params: any): void {
			console.log('New node at: ' + params.pointer.DOM.y + ' / ' + params.pointer.DOM.x);
		}

	}
</script>

<style lang="scss">
	#app-content-wrapper {
		.popovermenu ul {
			flex-direction: row;
		}
	}
</style>
