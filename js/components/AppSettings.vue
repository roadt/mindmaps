<template>
	<div id="app-settings">
		<div id="app-settings-header">
			<button class="settings-button" data-apps-slide-toggle="#app-settings-content">{{ translate('Settings') }}</button>
		</div>
		<div id="app-settings-content">

		</div>
	</div>
</template>

<script>
	import Mixins from '../Mixins';

	export default {
		mixins: [Mixins]
	}
</script>
