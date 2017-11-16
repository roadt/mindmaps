<template>
	<div id="app-content">
		<div id="app-content-wrapper">
			<mindmap></mindmap>
		</div>
	</div>
</template>

<script>
	import Mindmap from './Mindmap.vue';

	export default {
		components: {
			'mindmap': Mindmap
		}
	}
</script>
