<?php
/**
 * ownFeestep of Fee fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="feestep-<?php echo $n ?>" class="item feestep">    
	<a
		href="<?php echo $this->url(sprintf('/fee/detach/own/feestep/%d', $n)) ?>"
		class="detach ask"
		data-target="feestep-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownFeestep][<?php echo $n ?>][type]" value="feestep" />
		<input type="hidden" name="dialog[ownFeestep][<?php echo $n ?>][id]" value="<?php echo $feestep->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">	    
    		<input
    			type="number"
    			name="dialog[ownRulestep][<?php echo $n; ?>][offset]"
    			placeholder="<?php echo __('feestep_placeholder_offset'); ?>"
    			value="<?php echo htmlspecialchars($feestep->offset); ?>" />
    	</div>
	    <div class="span9">	 
    		<input
    			type="text"
    			name="dialog[ownRulestep][<?php echo $n; ?>][name]"
    			placeholder="<?php echo __('feestep_placeholder_name'); ?>"
    			value="<?php echo htmlspecialchars($feestep->name); ?>" />
    	</div>
	</div>
</div>
