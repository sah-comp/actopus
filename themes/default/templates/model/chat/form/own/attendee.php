<?php
/**
 * ownAttendee of Chat fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="attendee-<?php echo $n ?>" class="item attendee">    
	<a
		href="<?php echo $this->url(sprintf('/chat/detach/own/attendee/%d', $n)) ?>"
		class="detach"
		data-target="attendee-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownAttendee][<?php echo $n ?>][type]" value="attendee" />
		<input type="hidden" name="dialog[ownAttendee][<?php echo $n ?>][id]" value="<?php echo $attendee->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span12">	    
    		<input
    		    id="attendee-<?php echo $n ?>-email"
    			type="text"
    			name="dialog[ownAttendee][<?php echo $n; ?>][email]"	
                class="autocomplete"
                data-source="<?php echo $this->url('/search/autocomplete/user/email?callback=?') ?>"
                data-spread='<?php echo json_encode(array('attendee-'.$n.'-email' => 'label')) ?>'
    			placeholder="<?php echo __('attendee_placeholder_email'); ?>"
    			value="<?php echo htmlspecialchars($attendee->email); ?>" />
    	</div>
	</div>
</div>
