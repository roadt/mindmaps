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
			<template v-for="mindmap in mindmaps">
				<li class="with-menu" v-bind:data-id="mindmap.id">
					<a href="#">{{ mindmap.title }}</a>
					<div class="app-navigation-entry-utils">
						<ul>
							<li class="app-navigation-entry-utils-menu-share svg" v-if="mindmap.shared">
								<i class="icon icon-share" v-bind:title="t('Shared with / by you')"></i>
							</li>
							<li class="app-navigation-entry-utils-menu-button">
								<button class="icon-more svg" v-bind:title="t('More')"></button>
							</li>
						</ul>
					</div>
					<div class="app-navigation-entry-menu">
						<ul>
							<li><button class="icon-rename svg" v-bind:title="t('Rename Mindmap')"></button></li>
							<li><button class="icon-delete svg" v-bind:title="t('Delete Mindmap')"></button></li>
						</ul>
					</div>
				</li>
			</template>
		</ul>
		<app-settings></app-settings>
	</div>
</template>

<script lang="ts">
	import AppSettings from './AppSettings.vue';
	import Vue from 'vue';
	import Component from 'vue-class-component';
	import Mixins from "../Mixins";
	import {MindmapService} from "../services/MindmapService";
	import {Mindmap} from "../models/Mindmap";

	@Component({
		mixins: [Mixins],
		components: {
			'app-settings': AppSettings
		}
	})
	export default class AppNavigation extends Vue {
		mindmaps: Array<Mindmap> = [];
		created() {
			this.mindmaps = [];
			let service = new MindmapService();
			service.load().then((response) => {
				this.mindmaps = [];
				response.data.forEach((mindmap: Mindmap) => {
					this.mindmaps.push(mindmap);
				});
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>
