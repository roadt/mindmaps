<template>
	<div id="app-content">
		<div id="app-content-wrapper">
			<mindmap></mindmap>
		</div>
		<app-sidebar></app-sidebar>
	</div>
</template>

<script>
	import Mixins from '../Mixins';
	import Mindmap from './Mindmap.vue';
	import AppSidebar from './AppSidebar.vue';

	export default {
		mixins: [Mixins],
		components: {
			'mindmap': Mindmap,
			'app-sidebar': AppSidebar
		}
	}
</script>
