<div id="app-settings">
	<div id="app-settings-header">
		<button class="settings-button" data-apps-slide-toggle="#app-settings-content">
            <span><?php p($l->t('Settings')); ?></span>
        </button>
	</div>
	<div id="app-settings-content">
        <ul>
            <li>
                <label for="refreshInterval"><?php p($l->t('Refresh interval (in minutes)')); ?></label>
                <input id="refreshInterval" name="refreshInterval" type="number" value="10">
            </li>
        </ul>
	</div>
</div>
