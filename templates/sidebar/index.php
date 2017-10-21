<script id="sidebar-tpl" type="text/x-handlebars-template">
    <div class="sidebar-header">
        <h2>{{ title }}</h2>
        <a class="close icon-close" href="#" title="<?php p($l->t('Close')); ?>"></a>
    </div>
    <ul class="tabHeaders">
        <li class="tabHeader selected" data-tabid="detailsTabView"><a href="#"><?php p($l->t('Details')); ?></a></li>
        <li class="tabHeader" data-tabid="sharingTabView"><a href="#"><?php p($l->t('Sharing')); ?></a></li>
    </ul>
    <div class="tabsContainer">
        <div id="detailsTabView" class="tab detailsTabView">
            <h3><?php p($l->t('Help')); ?><span class="icon icon-info" title="<?php p($l->t('Help')); ?>"></span></h3>
            <p><?php print_unescaped($l->t('Select a node and double click anywhere in your mindmap to add a child node. You can also edit or delete nodes by simply clicking on them and choose the corresponding action icon. App icon by <a href="https://icons8.com/" rel="noopener" target="_blank">Icons8</a> and mindmaps powered by <a href="http://visjs.org/" rel="noopener" target="_blank">Vis.js</a>.')); ?></p>
            <h3><?php p($l->t('Description')); ?><a class="icon icon-edit save" href="#" title="<?php p($l->t('Save description')); ?>"></a></h3>
            <textarea id="description" title="<?php p($l->t('Description')); ?>">{{ description }}</textarea>
        </div>
        <div id="sharingTabView" class="tab sharingTabView hidden">
            <input id="shareWith" class="shareWithField" type="text" placeholder="<?php p($l->t('Enter username and hit enter...')); ?>" autocomplete="off">
            <ul id="shareWithList" class="shareWithList"></ul>
        </div>
    </div>
</script>
<script id="shareWithList-tpl" type="text/x-handlebars-template">
    {{#each shares}}
    <li data-id="{{ id }}">
        <div class="avatar" data-username="{{ participant }}"></div>
        <span class="username">{{ participant }}</span>
        <a class="icon icon-delete delete" href="#" title="<?php p($l->t('Delete share')); ?>"></a>
    </li>
    {{/each}}
</script>
<div id="sidebar-content"></div>
