<?php
/**
 * ownRegion of Template fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="region-<?php echo $n ?>" class="item region">    
	<a
		href="<?php echo $this->url(sprintf('/template/detach/own/region/%d', $n)) ?>"
		class="detach"
		data-target="region-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownRegion][<?php echo $n ?>][type]" value="region" />
		<input type="hidden" name="dialog[ownRegion][<?php echo $n ?>][id]" value="<?php echo $region->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span11">	    
    		<input
    			type="text"
    			name="dialog[ownRegion][<?php echo $n; ?>][name]"
    			placeholder="<?php echo __('region_placeholder_name'); ?>"
    			value="<?php echo htmlspecialchars($region->name); ?>" />
    	</div>
	</div>
</div>
