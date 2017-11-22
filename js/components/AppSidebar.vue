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
				<a class="close icon-close" @click="closeSidebar" :title="t('Close')"></a>
			</div>
			<ul class="tabHeaders">
				<li class="tabHeader selected" data-tabid="detailsTabView" @click="switchTab"><a href="#">{{ t('Details') }}</a></li>
				<li class="tabHeader" data-tabid="sharingTabView" @click="switchTab"><a href="#">{{ t('Sharing') }}</a></li>
			</ul>
			<div class="tabsContainer">
				<div id="detailsTabView" class="tab detailsTabView">
					<h3>{{ t('Help') }}<span class="icon icon-info" :title="t('Help')"></span></h3>
					<p v-html="helpText"></p>
					<h3>{{ t('Description') }}<a class="icon icon-edit save" href="#" :title="t('Save description')" @click="updateDescription"></a></h3>
					<textarea id="description" v-model="description" :placeholder="t('Add a description…')"></textarea>
				</div>
				<div id="sharingTabView" class="tab sharingTabView hidden">
					<input id="shareWith" class="shareWithField" v-model="participant" type="text" :placeholder="t('Enter user / group name and hit enter…')" autocomplete="off" @keyup.enter="submit">
					<ul id="shareWithList" class="shareWithList">
						<li v-for="share in shares" :key="share.id">
							<div class="avatar" :data-username="share.participant"></div>
							<span class="username">{{ share.participant }}</span>
							<a class="icon icon-delete delete" href="#" :title="t('Delete share')" @click="remove(share)"></a>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from 'vue';
	import Component from 'vue-class-component';
	import Acl from '../models/Acl';
	import AclService from '../services/AclService';
	import {AclType} from "../models/enums/AclType";
	import MindmapService from "../services/MindmapService";

	@Component
	export default class AppSidebar extends Vue {
		private service: AclService;
		private mindmapId: number;
		helpText = t('mindmaps', 'Select a node and double click anywhere in your mindmap to add a child node. ' +
			'You can also edit or delete nodes by simply clicking on them and choose the corresponding action icon. ' +
			'App icon by <a href="https://icons8.com/" rel="noopener" target="_blank">Icons8</a> and mindmaps powered by ' +
			'<a href="http://visjs.org/" rel="noopener" target="_blank">Vis.js</a>.');
		shares: Array<Acl> = [];
		description = '';
		participant = '';
		created(): void {
			this.mindmapId = parseInt(this.$route.params.id);
			this.service = new AclService();
			this.service.load(this.mindmapId).then((response) => {
				this.shares = [];
				response.data.forEach((share: Acl) => {
					this.shares.push(share);
				});
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
		closeSidebar(): void {
			// @ts-ignore
			OC.Apps.hideAppSidebar();
		}
		switchTab(event: Event): void {
			const tabId = $(event.currentTarget).attr('data-tabid');
			$('.tabHeader.selected').removeClass('selected');
			$(event.currentTarget).addClass('selected');
			$('.tab').hide();
			$('#' + tabId).show();

		}
		updateDescription(): void {
			console.log('Description: ' + this.description);
		}
		submit(): void {
			const share = new Acl();
			share.mindmapId = this.mindmapId;
			share.type = AclType.USER;
			share.participant = this.participant;
			this.service.create(share).then((response) => {
				this.shares.push(response.data);
				this.participant = '';
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
		remove(acl: Acl): void {
			this.service.remove(acl.id).then(() => {
				const index = this.shares.indexOf(acl);
				this.shares.splice(index, 1);
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>
