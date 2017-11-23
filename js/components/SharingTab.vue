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
	<div id="sharingTabView" class="tab sharingTabView hidden">
		<input id="shareWith" class="shareWithField" v-model="participant" type="text" :placeholder="t('Enter user / group / circle name and hit enter…')"
			   autocomplete="off" @keyup.enter="submit">
		<ul id="shareWithList" class="shareWithList">
			<li v-for="share in shares" :key="share.id">
				<div class="avatar" :data-participant="share.participant" :data-type="share.type"></div>
				<span class="username">{{ share.participant }}</span>
				<a class="icon icon-delete delete" href="#" :title="t('Delete share')" @click="remove(share)"></a>
			</li>
		</ul>
	</div>
</template>

<script lang="ts">
	import {Component, Prop, Vue, Watch} from 'vue-property-decorator';
	import * as _ from 'lodash';
	import Acl from '../models/Acl';
	import AclService from '../services/AclService';
	import Mindmap from '../models/Mindmap';

	@Component
	export default class SharingTab extends Vue {
		private aclService: AclService;
		@Prop()
		mindmap: Mindmap;
		shares: Array<Acl> = [];
		participant = '';

		private filterSuggestions(type: number, suggestions: Array<any>): Array<any> {
			const sharedWith = this.shares.filter((share) => {
				return share.type === type;
			}).map((share) => {
				return share.participant;
			});

			suggestions = suggestions.filter((share: any) => {
				return sharedWith.indexOf(share.value.shareWith) < 0;
			});

			return suggestions;
		}

		@Watch('mindmap.id', { deep: true })
		onMindmapIdChanged(id: number): void {
			if (id > 0) {
				this.aclService.load(id).then((response) => {
					response.data.forEach((share: Acl) => {
						this.shares.push(share);
					});
				}).catch((error) => {
					console.error('Error: ' + error.message);
				});
			}
		}

		@Watch('participant')
		onParticipantChanged(newValue: string): void {
			_.throttle(() => {
				this.aclService.getAutocomplete(newValue).then((response) => {
					const users = this.filterSuggestions(
						OC.Share.SHARE_TYPE_USER,
						response.data.ocs.data.exact.users.concat(
							response.data.ocs.data.users,
							[{value: {shareWidth: OC.getCurrentUser()}}]
						)
					);
					const groups = this.filterSuggestions(
						OC.Share.SHARE_TYPE_GROUP,
						response.data.ocs.data.exact.groups.concat(response.data.ocs.data.groups)
					);
					let circles = [];
					if (!_.isUndefined(response.data.ocs.data.circles)) {
						circles = this.filterSuggestions(
							OC.Share.SHARE_TYPE_CIRCLE,
							response.data.ocs.data.exact.circles.concat(response.data.ocs.data.circles)
						);
					}
					const suggestions  = users.concat(groups, circles);
					suggestions.forEach((suggestion: any) => {
						switch (suggestion.value.shareType) {
							case OC.Share.SHARE_TYPE_USER:
								console.log(suggestion.label + ' (user)');
								break;
							case OC.Share.SHARE_TYPE_GROUP:
								console.log(suggestion.label + ' (group)');
								break;
							case OC.Share.SHARE_TYPE_CIRCLE:
								console.log(suggestion.label + ' (' + suggestion.value.circleInfo + ', ' + suggestion.value.circleOwner + ')');
								break;
						}
					});
				}).catch((error) => {
					console.error('Error: ' + error.message);
				});
			}, 250)();
		}

		created(): void {
			this.aclService = new AclService();
		}

		updated(): void {
			// @ts-ignore
			$('.shareWithList > li').each((index: number, element: Node) => {
				const $item = $(element).find('.avatar');
				const type = parseInt($item.data('type') as string);
				const participant = $item.data('participant') as string;
				if (type === OC.Share.SHARE_TYPE_USER) {
					$item.avatar(participant);
				} else {
					$item.imageplaceholder(participant);
				}
			});
		}

		submit(): void {
			const share = new Acl();
			share.mindmapId = this.mindmap.id;
			share.type = OC.Share.SHARE_TYPE_USER;
			share.participant = this.participant;
			this.aclService.create(share).then((response) => {
				this.shares.push(response.data);
				this.participant = '';
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}

		remove(acl: Acl): void {
			this.aclService.remove(acl.id).then(() => {
				const index = this.shares.indexOf(acl);
				this.shares.splice(index, 1);
			}).catch((error) => {
				console.error('Error: ' + error.message);
			});
		}
	}
</script>

<style lang="scss" scoped>
	#shareWith {
		width: 100%;
	}

	.avatar {
		margin-right: 8px;
		display: inline-block;
		overflow: hidden;
		vertical-align: middle;
		width: 32px;
		height: 32px;
	}

	.username {
		padding-right: 8px;
		white-space: nowrap;
		text-overflow: ellipsis;
		max-width: 254px;
		display: inline-block;
		overflow: hidden;
		vertical-align: middle;
	}
</style>
