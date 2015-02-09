<?php
/**
 * ownUrl of Person fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="url-<?php echo $n ?>" class="item url">    
	<a
		href="<?php echo $this->url(sprintf('/person/detach/own/url/%d', $n)) ?>"
		class="detach ask"
		data-target="url-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownUrl][<?php echo $n ?>][type]" value="url" />
		<input type="hidden" name="dialog[ownUrl][<?php echo $n ?>][id]" value="<?php echo $url->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">
            <select name="dialog[ownUrl][<?php echo $n ?>][label]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($url->contactInfos() as $_contact_info): ?>
                <option
                    value="<?php echo $_contact_info ?>"
                    <?php echo ($url->label == $_contact_info) ? self::SELECTED : '' ?>><?php echo __('ci_url_'.$_contact_info) ?></option>
                <?php endforeach ?>
            </select>
        </div>
	    <div class="span9">
    		<input
    			type="text"
    			name="dialog[ownUrl][<?php echo $n; ?>][value]"
    			placeholder="<?php echo __('url_placeholder_value'); ?>"
    			value="<?php echo htmlspecialchars($url->value); ?>" />
    	</div>
	</div>
</div>
