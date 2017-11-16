<template>
	<div id="app-navigation">
		<button class="icon-add svg app-content-list-button" id="new-mindmap-button">{{ translate('New Mindmap') }}</button>
		<ul>
			<template v-for="mindmap in mindmaps">
				<li v-bind:class="mindmap.classes" v-bind:data-id="mindmap.id">
					<a href="#">{{ mindmap.title }}</a>
					<div class="app-navigation-entry-utils">
						<ul>
							<li class="app-navigation-entry-utils-menu-share svg" v-if="mindmap.shared">
								<i class="icon icon-share" v-bind:title="translate('Shared with / by you')"></i>
							</li>
							<li class="app-navigation-entry-utils-menu-button">
								<button class="icon-more svg" v-bind:title="translate('More')"></button>
							</li>
						</ul>
					</div>
					<div class="app-navigation-entry-menu" v-show="mindmap.menu">
						<ul>
							<li><button class="icon-rename svg" v-bind:title="translate('Rename Mindmap')"></button></li>
							<li><button class="icon-delete svg" v-bind:title="translate('Delete Mindmap')"></button></li>
						</ul>
					</div>
				</li>
			</template>
		</ul>
		<app-settings></app-settings>
	</div>
</template>

<script>
	import Mixins from '../Mixins';
	import AppSettings from './AppSettings.vue';

	export default {
		mixins: [Mixins],
		data () {
			return {
				mindmaps: [
					{classes:'with-menu active', id: 1, title: 'Mindmap #1', active: true, shared: false, menu: false},
					{classes:'with-menu', id: 2, title: 'Mindmap #2', active: false, shared: true, menu: false},
					{classes:'with-menu', id: 3, title: 'Mindmap #3', active: false, shared: false, menu: false}
				]
			}
		},
		components: {
			'app-settings': AppSettings
		}
	}
</script>
