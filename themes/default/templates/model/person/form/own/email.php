<?php
/**
 * ownEmail of Person fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="email-<?php echo $n ?>" class="item email">    
	<a
		href="<?php echo $this->url(sprintf('/person/detach/own/email/%d', $n)) ?>"
		class="detach ask"
		data-target="email-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownEmail][<?php echo $n ?>][type]" value="email" />
		<input type="hidden" name="dialog[ownEmail][<?php echo $n ?>][id]" value="<?php echo $email->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">
            <select name="dialog[ownEmail][<?php echo $n ?>][label]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($email->contactInfos() as $_contact_info): ?>
                <option
                    value="<?php echo $_contact_info ?>"
                    <?php echo ($email->label == $_contact_info) ? self::SELECTED : '' ?>><?php echo __('ci_email_'.$_contact_info) ?></option>
                <?php endforeach ?>
            </select>
        </div>
	    <div class="span9">
    		<input
    			type="email"
    			name="dialog[ownEmail][<?php echo $n; ?>][value]"
    			placeholder="<?php echo __('email_placeholder_value'); ?>"
    			value="<?php echo htmlspecialchars($email->value); ?>" />
    	</div>
	</div>
</div>
