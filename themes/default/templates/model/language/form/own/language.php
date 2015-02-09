<?php
/**
 * ownLanguage of Language fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="language-<?php echo $n ?>" class="item language">    
	<a
		href="<?php echo $this->url(sprintf('/language/detach/own/language/%d', $n)) ?>"
		class="detach"
		data-target="language-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownLanguage][<?php echo $n ?>][type]" value="language" />
		<input type="hidden" name="dialog[ownLanguage][<?php echo $n ?>][id]" value="<?php echo $language->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">	    
    		<input
    			type="text"
    			name="dialog[ownLanguage][<?php echo $n; ?>][iso]"
    			placeholder="<?php echo __('language_placeholder_iso'); ?>"
    			value="<?php echo htmlspecialchars($language->iso); ?>" />
    	</div>
	    <div class="span1">	 
            <input
                type="hidden"
                name="dialog[ownLanguage][<?php echo $n ?>][enabled]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownLanguage][<?php echo $n ?>][enabled]"
                <?php echo ($language->enabled) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	    <div class="span8">	    
    		<input
    			type="text"
    			name="dialog[ownLanguage][<?php echo $n; ?>][name]"
    			placeholder="<?php echo __('language_placeholder_name'); ?>"
    			value="<?php echo htmlspecialchars($language->name); ?>" />
    	</div>
	</div>
</div>
