<?php
/**
 * ownCountry of Country fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="country-<?php echo $n ?>" class="item country">    
	<a
		href="<?php echo $this->url(sprintf('/country/detach/own/country/%d', $n)) ?>"
		class="detach"
		data-target="country-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownCountry][<?php echo $n ?>][type]" value="country" />
		<input type="hidden" name="dialog[ownCountry][<?php echo $n ?>][id]" value="<?php echo $country->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">	    
    		<input
    			type="text"
    			name="dialog[ownCountry][<?php echo $n; ?>][iso]"
    			placeholder="<?php echo __('country_placeholder_iso'); ?>"
    			value="<?php echo htmlspecialchars($country->iso); ?>" />
    	</div>
	    <div class="span1">	 
            <input
                type="hidden"
                name="dialog[ownCountry][<?php echo $n ?>][enabled]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownCountry][<?php echo $n ?>][enabled]"
                <?php echo ($country->enabled) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	    <div class="span8">	    
    		<input
    			type="text"
    			name="dialog[ownCountry][<?php echo $n; ?>][name]"
    			placeholder="<?php echo __('country_placeholder_name'); ?>"
    			value="<?php echo htmlspecialchars($country->name); ?>" />
    	</div>
	</div>
</div>
