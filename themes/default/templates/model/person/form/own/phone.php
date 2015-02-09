<?php
/**
 * ownPhone of Person fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="phone-<?php echo $n ?>" class="item phone">    
	<a
		href="<?php echo $this->url(sprintf('/person/detach/own/phone/%d', $n)) ?>"
		class="detach ask"
		data-target="phone-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownPhone][<?php echo $n ?>][type]" value="phone" />
		<input type="hidden" name="dialog[ownPhone][<?php echo $n ?>][id]" value="<?php echo $phone->getId() ?>" />
	</div>
	<div class="row">	
	    <div class="span3">
            <select name="dialog[ownPhone][<?php echo $n ?>][label]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($phone->contactInfos() as $_contact_info): ?>
                <option
                    value="<?php echo $_contact_info ?>"
                    <?php echo ($phone->label == $_contact_info) ? self::SELECTED : '' ?>><?php echo __('ci_phone_'.$_contact_info) ?></option>
                <?php endforeach ?>
            </select>
        </div>
	    <div class="span9">    
    		<input
    			type="tel"
    			name="dialog[ownPhone][<?php echo $n; ?>][value]"
    			placeholder="<?php echo __('phone_placeholder_value'); ?>"
    			value="<?php echo htmlspecialchars($phone->value); ?>" />
    	</div>
	</div>
</div>
