<?php
/**
 * Page fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php foreach ($languages as $_language_id => $_language):
    $recordi18n = $record->i18n($_language->iso);
?>
<fieldset
    class="i18n <?php echo $_language->iso ?>"
    style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
    <legend class="verbose"><?php echo __('page_legend') ?></legend>
    <div>
        <input
            type="hidden"
            name="dialog[ownPagei18n][<?php echo $_language->getId() ?>][type]"
            value="<?php echo $recordi18n->getMeta('type') ?>" />
        <input
            type="hidden"
            name="dialog[ownPagei18n][<?php echo $_language->getId() ?>][id]"
            value="<?php echo $recordi18n->getId() ?>" />
        <input
            type="hidden"
            name="dialog[ownPagei18n][<?php echo $_language->getId() ?>][iso]"
            value="<?php echo $_language->iso ?>" />
    </div>
    <div class="row">
        <label
            for="page-name"
            class="<?php echo ($recordi18n->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('page_label_name') ?>
        </label>
        <input
            id="page-name"
            type="text"
            name="dialog[ownPagei18n][<?php echo $_language->getId() ?>][name]"
            placeholder="<?php echo __('page_placeholder_name') ?>"
            value="<?php echo htmlspecialchars($recordi18n->name) ?>" />
    </div>
</fieldset>
<?php
endforeach;
?>
<fieldset>
    <legend class="verbose"><?php echo __('page_legend') ?></legend>
    <div class="row">
        <input
            type="hidden"
            name="dialog[invisible]"
            value="0" />
        <input
            id="page-invisible"
            type="checkbox"
            name="dialog[invisible]"
            <?php echo ($record->invisible) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="page-invisible"
            class="cb <?php echo ($record->hasError('invisible')) ? 'error' : ''; ?>">
            <?php echo __('page_label_invisible') ?>
        </label>
    </div>
</fieldset>
<div id="page-tabs" class="bar tabbed">
    <?php echo $this->tabbed('page-tabs', array(
        'page-children' => __('page_tab_children'),
        'page-article' => __('page_tab_article')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="page-children"
        class="tab">
        <legend class="verbose"><?php echo __('page_legend_page') ?></legend>
        
    	<div class="row">
        	<div class="span10"><?php echo __('page_label_name') ?></div>
            <div class="span2"><?php echo __('page_label_invisible') ?></div>
    	</div>
        
        <div
            id="page-container"
            class="container attachable detachable sortable page"
            title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
            data-href="<?php echo $this->url(sprintf('/page/sortable/page/page')) ?>"
            data-container="page-container"
            data-variable="page">
        <?php foreach ($record->own('page', false) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'page'), array('n' => $_n, 'page' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'page')) ?>"
    			class="attach"
    			data-target="page-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="page-article"
        class="tab">
        <legend class="verbose"><?php echo __('page_legend_article') ?></legend>
        
    	<div class="row">
        	<div class="span5"><?php echo __('article_label_name') ?></div>
            <div class="span5"><?php echo __('article_label_template') ?></div>
            <div class="span2"><?php echo __('article_label_invisible') ?></div>
    	</div>
        
        <div
            id="article-container"
            class="container attachable detachable sortable article"
            title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
            data-href="<?php echo $this->url(sprintf('/page/sortable/article/article')) ?>"
            data-container="article-container"
            data-variable="article">
        <?php foreach ($record->own('article', false) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'article'), array('n' => $_n, 'article' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'article')) ?>"
    			class="attach"
    			data-target="article-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
