<template>
	<div id="app-sidebar">
		<div id="sidebar-content">
			<div class="sidebar-header">
				<h2>{{ translate('Title') }}</h2>
				<a class="close icon-close" href="#" title="Close"></a>
			</div>
			<ul class="tabHeaders">
				<li class="tabHeader selected" data-tabid="detailsTabView"><a href="#">{{ translate('Details') }}</a></li>
				<li class="tabHeader" data-tabid="sharingTabView"><a href="#">{{ translate('Sharing') }}</a></li>
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

<script>
	import Mixins from '../Mixins';

	export default {
		mixins: [Mixins]
	}
</script>
