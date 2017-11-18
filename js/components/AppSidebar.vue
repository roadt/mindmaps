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

				</div>
				<div id="sharingTabView" class="tab sharingTabView hidden">

				</div>
			</div>
		</div>
	</div>
</template>

<script lang="ts">
	import Vue from 'vue';
	import Component from 'vue-class-component';
	import Mixins from "../Mixins";

	@Component({
		mixins: [Mixins]
	})
	export default class AppSidebar extends Vue {

	}
</script>
