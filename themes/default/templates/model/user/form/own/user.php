<?php
/**
 * ownUser of User fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="user-<?php echo $n ?>" class="item user">    
	<a
		href="<?php echo $this->url(sprintf('/user/detach/own/user/%d', $n)) ?>"
		class="detach"
		data-target="user-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownUser][<?php echo $n ?>][type]" value="user" />
		<input type="hidden" name="dialog[ownUser][<?php echo $n ?>][id]" value="<?php echo $user->getId() ?>" />
	</div>
	<div class="row">	    
		<input
			type="text"
			name="dialog[ownUser][<?php echo $n; ?>][email]"
			placeholder="<?php echo __('user_placeholder_email'); ?>"
			value="<?php echo htmlspecialchars($user->email); ?>" />
	</div>
</div>
