<?php
/**
 * Cardtype fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('cardtype_legend') ?></legend>
    <div class="row">
        <label
            for="cardtype-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('cardtype_label_name') ?>
        </label>
        <input
            id="cardtype-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
</fieldset>
<div id="cardtype-tabs" class="bar tabbed">
    <?php echo $this->tabbed('cardtype-tabs', array(
        'cardtype-children' => __('cardtype_tab_attrsets')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="cardtype-children"
        class="tab">
        <legend class="verbose"><?php echo __('cardtype_legend_attrsets') ?></legend>

    	<div class="row">
    	    <div class="span3"><?php echo __('attrset_label_label') ?></div>
        	<div class="span1"><?php echo __('attrset_label_enabled') ?></div>
        	<div class="span8"><?php echo __('attrset_label_desc') ?></div>
    	</div>

        <div id="cardtype-container" class="container attachable detachable attrset">
        <?php foreach ($record->own('attrset', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'attrset'), array('n' => $_n, 'attrset' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'attrset')) ?>"
    			class="attach"
    			data-target="cardtype-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
