<?php
/**
 * ownPriority of Card fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="priority-<?php echo $n ?>" class="item priority">    
	<a
		href="<?php echo $this->url(sprintf('/card/detach/own/priority/%d', $n)) ?>"
		class="detach ask"
		data-target="priority-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownPriority][<?php echo $n ?>][type]" value="priority" />
		<input type="hidden" name="dialog[ownPriority][<?php echo $n ?>][id]" value="<?php echo $priority->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">
            <select name="dialog[ownPriority][<?php echo $n ?>][country_id]">
                <option value=""><?php echo __('priority_select_country') ?></option>
                <?php foreach ($countries as $_country_id => $_country): ?>
                <option
                    value="<?php echo $_country->getId() ?>"
                    <?php echo ($priority->country_id == $_country->getId()) ? self::SELECTED : '' ?>><?php echo $_country->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
	    <div class="span3">
    		<input
    			type="text"
    			name="dialog[ownPriority][<?php echo $n; ?>][date]"
    			placeholder="<?php echo __('priority_placeholder_date'); ?>"
    			value="<?php echo $this->date($priority->date); ?>" />
    	</div>
	    <div class="span6">
    		<input
    			type="text"
    			name="dialog[ownPriority][<?php echo $n; ?>][number]"
    			placeholder="<?php echo __('priority_placeholder_number'); ?>"
    			value="<?php echo htmlspecialchars($priority->number); ?>" />
    	</div>
	</div>
</div>
