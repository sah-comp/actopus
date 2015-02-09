<?php
/**
 * ownQueue of Newsletter fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="queue-<?php echo $n ?>" class="item queue">
	<div>
		<input type="hidden" name="dialog[ownQueue][<?php echo $n ?>][type]" value="queue" />
		<input type="hidden" name="dialog[ownQueue][<?php echo $n ?>][id]" value="<?php echo $queue->getId() ?>" />
	</div>
	<div class="row <?php echo ($queue->error) ? 'error' : '' ?>">
	    <div class="span6">
            <?php echo htmlspecialchars($queue->email); ?>
	    </div>
	    <div class="span3">	
	        <?php if ($queue->sent): ?>
                <?php echo $this->timestamp($queue->sent); ?>
            <?php else: ?>
                <?php echo __('queue_sent_unknown') ?>
	        <?php endif ?> 
    	</div>
	    <div class="span3">	
	        <?php if ($queue->open): ?>
                <?php echo $this->timestamp($queue->open); ?>
            <?php else: ?>
                <?php echo __('queue_open_unknown') ?>
	        <?php endif ?> 
    	</div>
	</div>
</div>
