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
		<button class="icon-add svg app-navigation-list-button" @click="showNew">{{ t('New Mindmap') }}</button>
		<form @submit.prevent="saveNew" class="app-navigation-entry-edit" style="display: none">
			<input type="text" v-model="title">
			<button class="icon-checkmark svg" type="submit"></button>
		</form>
		<ul>
			<li class="with-menu" v-for="mindmap in mindmaps" :key="mindmap.id">
				<router-link :to="`/mindmaps/${mindmap.id}`">{{ mindmap.title }}</router-link>
				<div class="app-navigation-entry-utils">
					<ul>
						<li class="app-navigation-entry-utils-menu-share" v-if="mindmap.shared">
							<i class="icon icon-share svg" :title="t('Shared with / by you')"></i>
						</li>
						<li class="app-navigation-entry-utils-menu-button">
							<button class="icon-more svg" :title="t('More')" @click="openMenu"></button>
						</li>
					</ul>
				</div>
				<div class="app-navigation-entry-menu">
					<ul>
						<li><button class="icon-rename svg" :title="t('Rename Mindmap')" @click="showEdit"></button></li>
						<li><button class="icon-delete svg" :title="t('Delete Mindmap')" @click="remove(mindmap)"></button></li>
					</ul>
				</div>
				<form @submit.prevent="saveEdit(mindmap)" class="app-navigation-entry-edit" style="display: none">
					<input type="text" v-model="mindmap.title">
					<button class="icon-checkmark svg" type="submit"></button>
				</form>
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
		private mindmapService: MindmapService;
		mindmaps: Array<Mindmap> = [];
		title: string;

		created(): void {
			this.title = '';
			this.mindmapService = new MindmapService();
			this.mindmapService.load().then((response) => {
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

		openMenu(event: Event): void {
			$(event.currentTarget).parents('.with-menu').find('.app-navigation-entry-menu').addClass('open');
		}

		showNew(event: Event): void {
			$(event.currentTarget).hide();
			$(event.currentTarget).parents().find('.app-navigation-entry-edit').first().show();
		}

		showEdit(event: Event): void {
			$(event.currentTarget).parents('.with-menu').children().hide();
			$(event.currentTarget).parents('.with-menu').find('.app-navigation-entry-edit').show();
		}

		saveNew(): void {
			let mindmap = new Mindmap();
			mindmap.title = this.title;
			mindmap.description = '';
			this.mindmapService.create(mindmap).then((response) => {
				this.mindmaps.push(response.data);
				this.title = '';
				$('.app-navigation-entry-edit').hide();
				$('.app-navigation-list-button').show();
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}

		saveEdit(mindmap: Mindmap): void {
			this.mindmapService.update(mindmap).then(() => {
				$('.app-navigation-entry-edit').hide();
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}

		remove(mindmap: Mindmap): void {
			this.mindmapService.remove(mindmap.id).then(() => {
				const index = this.mindmaps.indexOf(mindmap);
				this.mindmaps.splice(index, 1);
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>

<style lang="scss">
	#app-navigation {
		// A lot of inspiration here came from Contacts / Deck app <3
		.app-navigation-list-button {
			display: block;
			margin: 14px auto;
			padding: 10px 10px 10px 34px;
			width: calc(100% - 20px) !important;
			text-align: left;
			background-position: 10px center;
		}

		.app-navigation-entry-menu ul {
			flex-direction: row;
		}

		.app-navigation-entry-utils .app-navigation-entry-utils-menu-share {
			display: flex !important;
			padding: 14px;
			opacity: 0.5;
		}

		.app-navigation-entry-edit {
			input {
				border-bottom-right-radius: 0;
				border-top-right-radius: 0;
				width: calc(100% - 40px);
				padding: 5px;
				margin-right: 0;
				margin-left: 4px;
				height: 38px;
				float: left;
				border: 1px solid rgba(186, 186, 186, 0.9);
			}

			button {
				width: 30px;
				height: 38px;
				float: left;
			}
		}
	}
</style>
