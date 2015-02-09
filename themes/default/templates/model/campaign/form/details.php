<?php
/**
 * Campaign fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('campagin_legend') ?></legend>
    <div class="row">
        <label
            for="campagin-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('campaign_label_name') ?>
        </label>
        <input
            id="campagin-name"
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
            id="campaign-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="campaign-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('campaign_label_enabled') ?>
        </label>
    </div>
</fieldset>
<div id="campaign-tabs" class="bar tabbed">
    <?php echo $this->tabbed('campaign-tabs', array(
        'campaign-optin' => __('campaign_tab_optin'),
        'campaign-attribute' => __('campaign_tab_attribute')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="campaign-attribute"
        class="tab">
        <legend class="verbose"><?php echo __('campaign_legend_attribute') ?></legend>
        
    	<div class="row">
    	    <div class="span2"><?php echo __('attribute_label_name') ?></div>
        	<div class="span2"><?php echo __('attribute_label_tag') ?></div>
        	<div class="span3"><?php echo __('attribute_label_placeholder') ?></div>
        	<div class="span2"><?php echo __('attribute_label_default') ?></div>
        	<div class="span1"><?php echo __('attribute_label_required') ?></div>
        	<div class="span1"><?php echo __('attribute_label_enabled') ?></div>
    	</div>
        
        <div
            id="attribute-container"
            class="container attachable detachable sortable attribute"
            title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
            data-href="<?php echo $this->url(sprintf('/campaign/sortable/attribute/attribute')) ?>"
            data-container="attribute-container"
            data-variable="attribute">
        <?php foreach ($record->own('attribute', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'attribute'), array('n' => $_n, 'attribute' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'attribute')) ?>"
    			class="attach"
    			data-target="attribute-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>    

    <fieldset
        id="campaign-optin"
        class="tab">
        <legend class="verbose"><?php echo __('campaign-optin-legend') ?></legend>
        
    	<div class="row">
    	    <div class="span8"><?php echo __('optin_label_email') ?></div>
        	<div class="span4"><?php echo __('optin_label_enabled') ?></div>
    	</div>
    	
        <div id="optin-container" class="container attachable detachable optin">
        <?php foreach ($record->shared('optin', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/shared/%s', $record->getMeta('type'), 'optin'), array('n' => $_n, 'optin' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/shared/%s', $record->getMeta('type'), 'optin')) ?>"
    			class="attach"
    			data-target="optin-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
