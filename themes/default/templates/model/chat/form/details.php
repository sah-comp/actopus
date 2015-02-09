<?php
/**
 * Chat fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('chat_legend') ?></legend>
    <div class="row">
        <label
            for="chat-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('chat_label_name') ?>
        </label>
        <input
            id="chat-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
</fieldset>
<div id="chat-tabs" class="bar tabbed">
    <?php echo $this->tabbed('chat-tabs', array(
        'chat-attendee' => __('chat_tab_attendee')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="chat-attendee"
        class="tab">
        <legend class="verbose"><?php echo __('chat_legend_attendee') ?></legend>
        
    	<div class="row">
    	    <div class="span12"><?php echo __('attendee_label_email') ?></div>
    	</div>
        
        <div id="attendee-container" class="container attachable detachable attendee">
        <?php foreach ($record->own('attendee', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'attendee'), array('n' => $_n, 'attendee' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'attendee')) ?>"
    			class="attach"
    			data-target="attendee-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
