<?php
/**
 * ownRulestep of Rule fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="rulestep-<?php echo $n ?>" class="item rulestep">    
	<a
		href="<?php echo $this->url(sprintf('/rule/detach/own/rulestep/%d', $n)) ?>"
		class="detach askÍ"
		data-target="rulestep-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownRulestep][<?php echo $n ?>][type]" value="rulestep" />
		<input type="hidden" name="dialog[ownRulestep][<?php echo $n ?>][id]" value="<?php echo $rulestep->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">	    
    		<input
    			type="number"
    			name="dialog[ownRulestep][<?php echo $n; ?>][offset]"
    			placeholder="<?php echo __('rulestep_placeholder_offset'); ?>"
    			value="<?php echo htmlspecialchars($rulestep->offset); ?>" />
    	</div>
	    <div class="span9">	 
    		<input
    			type="text"
    			name="dialog[ownRulestep][<?php echo $n; ?>][name]"
    			placeholder="<?php echo __('rulestep_placeholder_name'); ?>"
    			value="<?php echo htmlspecialchars($rulestep->name); ?>" />
    	</div>
	</div>
</div>
