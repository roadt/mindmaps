<template>
	<div id="app-navigation">
		<ul>
			<li v-for="mindmap in mindmaps">
				<a href="#">{{ mindmap.name }}</a>
			</li>
		</ul>
		<app-settings></app-settings>
	</div>
</template>

<script>
	import AppSettings from './AppSettings.vue';

	export default {
		data () {
			return {
				mindmaps: [
					{name: 'Mindmap #1'},
					{name: 'Mindmap #2'}
				]
			}
		},
		components: {
			'app-settings': AppSettings
		}
	}
</script>
