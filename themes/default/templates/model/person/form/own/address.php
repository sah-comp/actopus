<?php
/**
 * ownAddress of Person fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="address-<?php echo $n ?>" class="item address">    
	<a
		href="<?php echo $this->url(sprintf('/person/detach/own/address/%d', $n)) ?>"
		class="detach"
		data-target="address-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownAddress][<?php echo $n ?>][type]" value="address" />
		<input type="hidden" name="dialog[ownAddress][<?php echo $n ?>][id]" value="<?php echo $address->getId() ?>" />
	</div>
	<div class="row">	
	    <div class="span3">
            <select name="dialog[ownAddress][<?php echo $n ?>][label]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($address->contactInfos() as $_contact_info): ?>
                <option
                    value="<?php echo $_contact_info ?>"
                    <?php echo ($address->label == $_contact_info) ? self::SELECTED : '' ?>><?php echo __('ci_address_'.$_contact_info) ?></option>
                <?php endforeach ?>
            </select>
        </div>
	    <div class="span9">   
	        <div class="row"> 
        		<textarea
        		    id="address-street-<?php echo $n ?>"
        			name="dialog[ownAddress][<?php echo $n; ?>][street]"
                    class="autocomplete"
                    data-source="<?php echo $this->url('/search/autocomplete/address/street?callback=?') ?>"
                    data-spread='<?php echo json_encode(array('address-street-'.$n => 'street', 'address-zip-'.$n => 'zip', 'address-city-'.$n => 'city', 'address-county-'.$n => 'county', 'address-iso-'.$n => 'iso', 'address-country-'.$n => 'country')) ?>'
        			placeholder="<?php echo __('address_placeholder_street'); ?>"
        			rows="3"><?php echo htmlspecialchars($address->street); ?></textarea><br /><br />
        	</div>
        	<div class="row">
        	    <div class="span4">
        	        <input
            		    id="address-zip-<?php echo $n ?>"
        	            type="text"
        	            name="dialog[ownAddress][<?php echo $n ?>][zip]"
            			placeholder="<?php echo __('address_placeholder_zip'); ?>"
        	            value="<?php echo htmlspecialchars($address->zip) ?>" />
        	    </div>
        	     <div class="span8">
        	        <input
             		    id="address-city-<?php echo $n ?>"
        	            type="text"
        	            name="dialog[ownAddress][<?php echo $n ?>][city]"
            			placeholder="<?php echo __('address_placeholder_city'); ?>"
        	            value="<?php echo htmlspecialchars($address->city) ?>" />
            	</div>
        	</div>
        	<div class="row">
    	        <input
        		    id="address-county-<?php echo $n ?>"
    	            type="text"
    	            name="dialog[ownAddress][<?php echo $n ?>][county]"
        			placeholder="<?php echo __('address_placeholder_county'); ?>"
    	            value="<?php echo htmlspecialchars($address->county) ?>" />
        	</div>
    	    <div class="row last">
    	        <input
    	            id="address-iso-<?php echo $n ?>"
    	            type="hidden"
    	            name="dialog[ownAddress][<?php echo $n ?>][iso]"
    	            value="<?php echo htmlspecialchars($address->iso) ?>" />    	        
                <input
                    id="address-country-<?php echo $n ?>"
                    type="text"
                    name="dialog[ownAddress][<?php echo $n ?>][country]"
                    class="autocomplete"
                    data-source="<?php echo $this->url('/search/autocomplete/country/name?callback=?') ?>"
                    data-spread='<?php echo json_encode(array('address-iso-'.$n => 'iso', 'address-country-'.$n => 'label')) ?>'
                    value="<?php echo htmlspecialchars($address->country) ?>"
                    placeholder="<?php echo __('address_placeholder_country') ?>" />
        	</div>
    	</div>
	</div>
</div>
