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
			mindmapService.load().then(() => {
				const mindmap = mindmapService.find(id);
				if (!_.isNull(mindmap)) {
					this.mindmap = mindmap;
				}
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});

			const mindmapNodeService = new MindmapNodeService();
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
				}
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>
