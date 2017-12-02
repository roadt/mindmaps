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
		<ul>
			<li>
				<a class="icon-add" @click="showNew">{{ t('New Mindmap') }}</a>
				<div class="app-navigation-entry-edit">
					<form @submit.prevent="create">
						<input type="text" :placeholder="t('New mindmap')" maxlength="255" v-model="title">
						<input type="submit" value="" class="icon-close">
						<input type="submit" value="" class="icon-checkmark">
					</form>
				</div>
			</li>
			<li class="with-menu" v-for="mindmap in mindmaps" :key="mindmap.id" :data-id="mindmap.id">
				<router-link :to="`/mindmaps/${mindmap.id}`">{{ mindmap.title }}</router-link>
				<div class="app-navigation-entry-utils">
					<ul>
						<li class="app-navigation-entry-utils-menu-share" v-if="mindmap.shared">
							<span class="icon-share" :title="t('Shared with / by you')"></span>
						</li>
						<li class="app-navigation-entry-utils-menu-button">
							<button class="icon-more" :title="t('View more')" @click="showMenu">
								<span class="hidden-visually">{{ t('View more') }}</span>
							</button>
						</li>
					</ul>
				</div>
				<div class="app-navigation-entry-menu">
					<ul>
						<li>
							<button class="icon-rename" :title="t('Rename Mindmap')" @click="showEdit">
								<span class="hidden-visually">{{ t('Rename Mindmap') }}</span>
							</button>
						</li>
						<li>
							<button class="icon-delete" :title="t('Delete Mindmap')" @click="remove(mindmap)">
								<span class="hidden-visually">{{ t('Delete Mindmap') }}</span>
							</button>
						</li>
					</ul>
				</div>
				<div class="app-navigation-entry-edit">
					<form @submit.prevent="update(mindmap)">
						<input type="text" maxlength="255" v-model="mindmap.title">
						<input type="submit" value="" class="icon-close">
						<input type="submit" value="" class="icon-checkmark">
					</form>
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

			$(document).on('click', () => {
				$('.app-navigation-entry-menu').removeClass('open');
			});
		}

		showMenu(event: Event): void {
			$(event.currentTarget).parents('.with-menu').find('.app-navigation-entry-menu').addClass('open');
			event.stopPropagation();
		}

		showNew(event: Event): void {
			$(event.currentTarget).parent().addClass('editing');
		}

		showEdit(event: Event): void {
			$(event.currentTarget).parents('.with-menu').addClass('editing');
		}

		create(): void {
			let mindmap = new Mindmap();
			mindmap.title = this.title;
			mindmap.description = '';
			this.mindmapService.create(mindmap).then((response) => {
				this.mindmaps.push(response.data);
				this.title = '';
				$('#app-navigation').find('ul > li').first().removeClass('editing');
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}

		update(mindmap: Mindmap): void {
			this.mindmapService.update(mindmap).then(() => {
				$(`.with-menu[data-id=${mindmap.id}]`).removeClass('editing');
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
		.app-navigation-entry-menu ul {
			flex-direction: row !important;
		}
	}
</style>
