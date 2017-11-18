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
	<div id="mindmap">{{ t('Mindmap loading...') }}</div>
</template>

<script lang="ts">
	import {MindmapNodeService} from '../services/MindmapNodeService';
	import * as vis from 'vis';
	import Vue from 'vue';
	import Component from 'vue-class-component';
	import {MindmapNode} from "../models/MindmapNode";
	import Mixins from "../Mixins";

	@Component({
		mixins: [Mixins]
	})
	export default class Mindmap extends Vue {
		created() {
			let service = new MindmapNodeService();
			service.load(23).then((response) => {
				let wrapper: HTMLElement | null = document.getElementById('app-content-wrapper');
				let container: HTMLElement | null = document.getElementById('mindmap');
				let options = {
					physics: {enabled: false},
					interaction: {dragNodes: false},
					locale: OC.getLocale()
				};
				let nodes: Array<MindmapNode> = response.data;
				let edges: Array<{from: number, to: number}> = [];

				if (container !== null && wrapper !== null) {
					// The mindmap should use all of the wrappers place.
					container.style.height = wrapper.clientHeight + 'px';
				}

				nodes.forEach((val: MindmapNode) => {
					if (val.parentId !== 0) {
						edges.push({from: val.parentId, to: val.id});
					}
				});

				if (vis !== null && container !== null) {
					let network = new vis.Network(container,
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
