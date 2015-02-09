<?php
/**
 * sharedOptin of Campaign fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="optin-<?php echo $n ?>" class="item optin">    
	<a
		href="<?php echo $this->url(sprintf('/campaign/detach/shared/optin/%d', $n)) ?>"
		class="detach"
		data-target="optin-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[sharedOptin][<?php echo $n ?>][type]" value="optin" />
		<input type="hidden" name="dialog[sharedOptin][<?php echo $n ?>][id]" value="<?php echo $optin->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span8">
    		<input
    			type="email"
    			name="dialog[sharedOptin][<?php echo $n; ?>][email]"
    			value="<?php echo htmlspecialchars($optin->email); ?>" />
	    </div>
	    <div class="span4">
            <input
                type="hidden"
                name="dialog[sharedOptin][<?php echo $n ?>][enabled]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[sharedOptin][<?php echo $n ?>][enabled]"
                <?php echo ($optin->enabled) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	</div>
</div>
