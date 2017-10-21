<script id="navigation-tpl" type="text/x-handlebars-template">
    {{#each mindmaps}}
        <li class="mindmap with-menu {{#if active}}active{{/if}}" data-id="{{ id }}">
            <a href="#">{{ title }}</a>
            <div class="app-navigation-entry-utils">
                <ul>
                    {{#if shared}}
                    <li class="app-navigation-entry-utils-menu-share svg">
                        <i class="icon icon-share" title="<?php p($l->t('Shared with / by you')); ?>"></i>
                    </li>
                    {{/if}}
                    <li class="app-navigation-entry-utils-menu-button">
                        <button class="icon-more svg" title="<?php p($l->t('More')); ?>"></button>
                    </li>
                </ul>
            </div>
            <div class="app-navigation-entry-menu" style="display: none">
                <ul>
                    <li><button class="mindmap-rename icon-rename svg" title="<?php p($l->t('Rename Mindmap')); ?>"></button></li>
                    <li><button class="mindmap-delete icon-delete svg" title="<?php p($l->t('Delete Mindmap')); ?>"></button></li>
                </ul>
            </div>
        </li>
    {{/each}}
</script>
<button class="icon-add svg app-content-list-button" id="new-mindmap-button"><?php p($l->t('New Mindmap')); ?></button>
<ul id="mindmap-list"></ul>
