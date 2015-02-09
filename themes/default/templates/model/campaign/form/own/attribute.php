<?php
/**
 * ownAttribute of Campaign fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="attribute-<?php echo $n ?>" class="item attribute">    
	<a
		href="<?php echo $this->url(sprintf('/attribute/detach/own/attribute/%d', $n)) ?>"
		class="detach"
		data-target="attribute-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownAttribute][<?php echo $n ?>][type]" value="attribute" />
		<input type="hidden" name="dialog[ownAttribute][<?php echo $n ?>][id]" value="<?php echo $attribute->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span2">	    
    		<input
    			type="text"
    			name="dialog[ownAttribute][<?php echo $n; ?>][name]"
    			placeholder="<?php echo __('attribute_placeholder_name'); ?>"
    			value="<?php echo htmlspecialchars($attribute->name); ?>" />
    	</div>
	    <div class="span2">	    
    		<input
    			type="text"
    			name="dialog[ownAttribute][<?php echo $n; ?>][tag]"
    			placeholder="<?php echo __('attribute_placeholder_tag'); ?>"
    			value="<?php echo htmlspecialchars($attribute->tag); ?>" />
    	</div>
    	<div class="span3">	    
    		<input
    			type="text"
    			name="dialog[ownAttribute][<?php echo $n; ?>][placeholder]"
    			placeholder="<?php echo __('attribute_placeholder_placeholder'); ?>"
    			value="<?php echo htmlspecialchars($attribute->placeholder); ?>" />
    	</div>
    	<div class="span2">	    
    		<input
    			type="text"
    			name="dialog[ownAttribute][<?php echo $n; ?>][default]"
    			placeholder="<?php echo __('attribute_placeholder_default'); ?>"
    			value="<?php echo htmlspecialchars($attribute->default); ?>" />
    	</div>
	    <div class="span1">	 
            <input
                type="hidden"
                name="dialog[ownAttribute][<?php echo $n ?>][required]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownAttribute][<?php echo $n ?>][required]"
                <?php echo ($attribute->required) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	    <div class="span1">	 
            <input
                type="hidden"
                name="dialog[ownAttribute][<?php echo $n ?>][enabled]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownAttribute][<?php echo $n ?>][enabled]"
                <?php echo ($attribute->enabled) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	</div>
</div>
