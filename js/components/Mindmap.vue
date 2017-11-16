<template>
	<div id="mindmap">{{ translate('Mindmap loading...') }}</div>
</template>

<script>
	import Mixins from '../Mixins';
	import {MindmapNodeService} from '../services/MindmapNodeService';
	import vis from 'vis';

	export default {
		mixins: [Mixins],
		mounted() {
			let service = new MindmapNodeService();
			service.loadAll(23).then((response) => {
				let container = document.getElementById('mindmap');
				let options = {
					physics: {enabled: false},
					interaction: {dragNodes: false},
					locale: OC.getLocale()
				};
				let nodes = response.data;
				let edges = [];

				nodes.forEach((val) => {
					if (val.parentId !== 0) {
						edges.push({from: val.parentId, to: val.id});
					}
				});

				if (vis !== null) {
					let network = new vis.Network(container, {nodes: new vis.DataSet(nodes), edges: new vis.DataSet(edges)}, options);
					network.fit();
				}
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>
