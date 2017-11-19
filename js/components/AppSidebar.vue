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
	<div id="app-sidebar">
		<div id="sidebar-content">
			<div class="sidebar-header">
				<h2>{{ t('Title') }}</h2>
				<a class="close icon-close" href="#" title="Close"></a>
			</div>
			<ul class="tabHeaders">
				<li class="tabHeader selected" data-tabid="detailsTabView"><a href="#">{{ t('Details') }}</a></li>
				<li class="tabHeader" data-tabid="sharingTabView"><a href="#">{{ t('Sharing') }}</a></li>
			</ul>
			<div class="tabsContainer">
				<div id="detailsTabView" class="tab detailsTabView">
					<h3>{{ t('Help') }}<span class="icon icon-info" :title="t('Help')"></span></h3>
					<p v-html="helpText"></p>
					<h3>{{ t('Description') }}<a class="icon icon-edit save" href="#" :title="t('Save description')"></a></h3>
					<textarea id="description">{{ description }}</textarea>
				</div>
				<div id="sharingTabView" class="tab sharingTabView hidden">
					<ul id="shareWithList" class="shareWithList">
						<template v-for="share in shares">
							<li :data-id="share.id">
								<div class="avatar" :data-username="share.participant"></div>
								<span class="username">{{ share.participant }}</span>
								<a class="icon icon-delete delete" href="#">{{ t('Delete share') }}</a>
							</li>
						</template>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from 'vue';
	import Component from 'vue-class-component';
	import Mixins from '../Mixins';
	import {Acl} from '../models/Acl';
	import {AclService} from '../services/AclService';

	@Component({
		mixins: [Mixins]
	})
	export default class AppSidebar extends Vue {
		helpText: string = t('mindmaps', 'Select a node and double click anywhere in your mindmap to add a child node. ' +
			'You can also edit or delete nodes by simply clicking on them and choose the corresponding action icon. ' +
			'App icon by <a href="https://icons8.com/" rel="noopener" target="_blank">Icons8</a> and mindmaps powered by ' +
			'<a href="http://visjs.org/" rel="noopener" target="_blank">Vis.js</a>.');
		shares: Array<Acl> = [];
		description: string = '';
		created() {
			let service = new AclService();
			service.load(23).then((response) => {
				this.shares = [];
				response.data.forEach((share: Acl) => {
					this.shares.push(share);
				});
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>
