<?php
/**
 * Template fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('template_legend') ?></legend>
    <div class="row">
        <label
            for="template-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('template_label_name') ?>
        </label>
        <input
            id="template-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[enabled]"
            value="0" />
        <input
            id="template-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="template-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('template_label_enabled') ?>
        </label>
    </div>
</fieldset>
<div id="template-tabs" class="bar tabbed">
    <?php echo $this->tabbed('template-tabs', array(
        'template-region' => __('template_tab_region'),
        'template-html' => __('template_tab_html'),
        'template-text' => __('template_tab_text')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="template-region"
        class="tab">
        <legend class="verbose"><?php echo __('template_legend_region') ?></legend>
        
    	<div class="row">
    	    <div class="span11"><?php echo __('region_label_name') ?></div>
    	</div>
        
        <div
            id="region-container"
            class="container attachable detachable sortable region"
            title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
            data-href="<?php echo $this->url(sprintf('/template/sortable/region/region')) ?>"
            data-container="region-container"
            data-variable="region">
        <?php foreach ($record->own('region', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'region'), array('n' => $_n, 'region' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'region')) ?>"
    			class="attach"
    			data-target="region-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="template-html"
        class="tab">
        <legend class="verbose"><?php echo __('template_legend_html') ?></legend>
        <div class="row">
            <label
                for="template-html-inp"
                class="<?php echo ($record->hasError('html')) ? 'error' : ''; ?>">
                <?php echo __('template_label_html') ?>
            </label>
            <textarea
                id="template-html-inp"
                rows="23"
                name="dialog[html]"><?php echo htmlspecialchars($record->html) ?></textarea>
        </div>
    </fieldset>
    <fieldset
        id="template-text"
        class="tab">
        <legend class="verbose"><?php echo __('template_legend_text') ?></legend>
        <div class="row">
            <label
                for="template-text-inp"
                class="<?php echo ($record->hasError('text')) ? 'error' : ''; ?>">
                <?php echo __('template_label_text') ?>
            </label>
            <textarea
                id="template-text-inp"
                rows="23"
                name="dialog[text]"><?php echo htmlspecialchars($record->text) ?></textarea>
        </div>
    </fieldset>
</div>

