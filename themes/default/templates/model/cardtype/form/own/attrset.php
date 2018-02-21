<?php
/**
 * ownAttrset of Cardtype fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="attrset-<?php echo $n ?>" class="item attrset">
	<a
		href="<?php echo $this->url(sprintf('/cardtype/detach/own/attrset/%d', $n)) ?>"
		class="detach"
		data-target="attrset-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownAttrset][<?php echo $n ?>][type]" value="attrset" />
		<input type="hidden" name="dialog[ownAttrset][<?php echo $n ?>][id]" value="<?php echo $attrset->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">
    		<input
    			type="text"
    			name="dialog[ownAttrset][<?php echo $n; ?>][label]"
    			placeholder="<?php echo __('attrset_placeholder_label'); ?>"
    			value="<?php echo htmlspecialchars($attrset->label); ?>" />
    	</div>
	    <div class="span1">
            <input
                type="hidden"
                name="dialog[ownAttrset][<?php echo $n ?>][enabled]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownAttrset][<?php echo $n ?>][enabled]"
                <?php echo ($attrset->enabled) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	    <div class="span8">
    		<input
    			type="text"
    			name="dialog[ownAttrset][<?php echo $n; ?>][desc]"
    			placeholder="<?php echo __('attrset_placeholder_desc'); ?>"
    			value="<?php echo htmlspecialchars($attrset->desc); ?>" />
    	</div>
	</div>
</div>
