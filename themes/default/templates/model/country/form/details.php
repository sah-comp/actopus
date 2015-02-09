<?php
/**
 * Country fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('country_legend') ?></legend>
    <div class="row">
        <label
            for="country-iso"
            class="<?php echo ($record->hasError('iso')) ? 'error' : ''; ?>">
            <?php echo __('country_label_iso') ?>
        </label>
        <input
            id="country-iso"
            type="text"
            name="dialog[iso]"
            value="<?php echo htmlspecialchars($record->iso) ?>"
            required="required" />
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[enabled]"
            value="0" />
        <input
            id="country-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="country-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('country_label_enabled') ?>
        </label>
    </div>
    <div class="row">
        <label
            for="country-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('country_label_name') ?>
        </label>
        <input
            id="country-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>" />
    </div>
</fieldset>
<div id="country-tabs" class="bar tabbed">
    <?php echo $this->tabbed('country-tabs', array(
        'country-children' => __('country_tab_children')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="country-children"
        class="tab">
        <legend class="verbose"><?php echo __('country_legend_country') ?></legend>
        
    	<div class="row">
    	    <div class="span3"><?php echo __('country_label_iso') ?></div>
        	<div class="span1"><?php echo __('country_label_enabled') ?></div>
        	<div class="span8"><?php echo __('country_label_name') ?></div>
    	</div>
        
        <div id="country-container" class="container attachable detachable country">
        <?php foreach ($record->own('country', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'country'), array('n' => $_n, 'country' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'country')) ?>"
    			class="attach"
    			data-target="country-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
