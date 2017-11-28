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
	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button" data-apps-slide-toggle="#app-settings-content">{{ t('Settings') }}</button>
		</div>
		<div id="app-settings-content">
			<ul>
				<li>
					<label for="refreshInterval">{{ t('Refresh interval (in minutes)') }}</label>
					<input id="refreshInterval" type="number" v-model="refreshInterval">
				</li>
			</ul>
		</div>
	</div>
</template>

<script lang="ts">
	import {Component, Vue} from 'vue-property-decorator';

	@Component
	export default class AppSettings extends Vue {
		refreshInterval = 0;

		created(): void {
			this.refreshInterval = 10;
		}
	}
</script>
