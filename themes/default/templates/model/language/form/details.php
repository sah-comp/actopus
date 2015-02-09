<?php
/**
 * Language fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('language_legend') ?></legend>
    <div class="row">
        <label
            for="language-iso"
            class="<?php echo ($record->hasError('iso')) ? 'error' : ''; ?>">
            <?php echo __('language_label_iso') ?>
        </label>
        <input
            id="language-iso"
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
            id="language-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="language-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('language_label_enabled') ?>
        </label>
    </div>
    <div class="row">
        <label
            for="language-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('language_label_name') ?>
        </label>
        
        <textarea
            id="language-name"
            name="dialog[name]"
            cols="60"
            rows="7"><?php echo htmlspecialchars($record->name) ?></textarea>
    </div>
</fieldset>
<div id="language-tabs" class="bar tabbed">
    <?php echo $this->tabbed('language-tabs', array(
        'language-children' => __('language_tab_children')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="language-children"
        class="tab">
        <legend class="verbose"><?php echo __('language_legend_language') ?></legend>
        
    	<div class="row">
    	    <div class="span3"><?php echo __('language_label_iso') ?></div>
        	<div class="span1"><?php echo __('language_label_enabled') ?></div>
        	<div class="span8"><?php echo __('language_label_name') ?></div>
    	</div>
        
        <div id="language-container" class="container attachable detachable language">
        <?php foreach ($record->own('language', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'language'), array('n' => $_n, 'language' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'language')) ?>"
    			class="attach"
    			data-target="language-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
