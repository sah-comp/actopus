<?php
/**
 * ownDomain of Domain fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="domain-<?php echo $n ?>" class="item domain">
	<a
		href="<?php echo $this->url(sprintf('/domain/detach/own/domain/%d', $n)) ?>"
		class="detach"
		data-target="domain-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownDomain][<?php echo $n ?>][type]" value="domain" />
		<input type="hidden" name="dialog[ownDomain][<?php echo $n ?>][id]" value="<?php echo $domain->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">
    		<input
    			type="text"
    			name="dialog[ownDomain][<?php echo $n; ?>][name]"
    			value="<?php echo htmlspecialchars($domain->name); ?>" />
	    </div>
	    <div class="span6">
    		<input
    			type="text"
    			name="dialog[ownDomain][<?php echo $n; ?>][url]"
    			value="<?php echo htmlspecialchars($domain->url); ?>" />
	    </div>
	    <div class="span3">	 
            <input
                type="hidden"
                name="dialog[ownDomain][<?php echo $n ?>][invisible]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownDomain][<?php echo $n ?>][invisible]"
                <?php echo ($domain->invisible) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	</div>
</div>
