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
	<div id="app-navigation">
		<button class="icon-add svg app-content-list-button" id="new-mindmap-button">{{ t('New Mindmap') }}</button>
		<ul>
			<li class="with-menu" v-for="mindmap in mindmaps" :key="mindmap.id">
				<router-link :to="`/mindmaps/${mindmap.id}`">{{ mindmap.title }}</router-link>
				<div class="app-navigation-entry-utils">
					<ul>
						<li class="app-navigation-entry-utils-menu-share" v-if="mindmap.shared">
							<i class="icon icon-share svg" :title="t('Shared with / by you')"></i>
						</li>
						<li class="app-navigation-entry-utils-menu-button">
							<button class="icon-more svg" :title="t('More')"></button>
						</li>
					</ul>
				</div>
				<div class="app-navigation-entry-menu">
					<ul>
						<li><button class="icon-rename svg" :title="t('Rename Mindmap')"></button></li>
						<li><button class="icon-delete svg" :title="t('Delete Mindmap')"></button></li>
					</ul>
				</div>
			</li>
		</ul>
		<app-settings></app-settings>
	</div>
</template>

<script lang="ts">
	import {Component, Vue} from 'vue-property-decorator';
	import * as _ from 'lodash';
	import AppSettings from './AppSettings.vue';
	import MindmapService from '../services/MindmapService';
	import Mindmap from '../models/Mindmap';

	@Component({
		components: {
			'app-settings': AppSettings
		}
	})
	export default class AppNavigation extends Vue {
		mindmaps: Array<Mindmap> = [];

		created(): void {
			const service = new MindmapService();
			service.load().then((response) => {
				response.data.forEach((mindmap: Mindmap) => {
					this.mindmaps.push(mindmap);
				});
				// Load the first mindmap
				if (_.isUndefined(this.$route.params.id) && this.mindmaps.length > 0) {
					this.$router.push({path: `/mindmaps/${this.mindmaps[0].id}`});
				}
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>
