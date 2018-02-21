<?php
/**
 * Multipay fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('multipay_legend') ?></legend>
    <div class="row">
        <label
            for="multipay-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('multipay_label_name') ?>
        </label>
        <input
            id="multipay-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
        <p class="info"><?php echo __('multipay_hint_name') ?></p>
        <?php if ($record->getId() && !$record->sent): ?>
        <p class="info">
            <a href="<?php echo $this->url(sprintf('/multipay/activate/%d', $record->getId())) ?>"><?php echo __('multipay_action_makemecurrent') ?></a>
        </p>
        <?php endif; ?>
    </div>
</fieldset>
<div id="multipay-tabs" class="bar tabbed">
    <?php echo $this->tabbed('multipay-tabs', array(
        'multipay-multipayfee' => __('multipay_tab_multipayfee')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="multipay-multipayfee"
        class="tab">
        <legend class="verbose"><?php echo __('multipayfee_legend') ?></legend>

    	<div class="row">
    	    <div class="span2"><?php echo __('multipayfee_label_cardname') ?></div>
        	<div class="span3"><?php echo __('multipayfee_label_applicant') ?></div>
        	<div class="span2"><?php echo __('multipayfee_label_applicationnumber') ?></div>
        	<div class="span1"><?php echo __('multipayfee_label_typeoffee') ?></div>
            <div class="span1"><?php echo __('multipayfee_label_amount') ?></div>
            <div class="span2"><?php echo __('multipayfee_label_datedue') ?></div>
    	</div>

        <div id="multipayfee-container" class="container attachable detachable multipayfee">
        <?php foreach ($record->own('multipayfee', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'multipayfee'), array('n' => $_n, 'multipayfee' => $_record)) ?>
        <?php endforeach ?>
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'multipayfee')) ?>"
    			class="attach"
    			data-target="multipayfee-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
