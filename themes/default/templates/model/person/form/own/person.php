<?php
/**
 * ownPerson of Person fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="person-<?php echo $n ?>" class="item person">    
	<a
		href="<?php echo $this->url(sprintf('/person/detach/own/person/%d', $n)) ?>"
		class="detach"
		data-target="person-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownPerson][<?php echo $n ?>][type]" value="person" />
		<input type="hidden" name="dialog[ownPerson][<?php echo $n ?>][id]" value="<?php echo $person->getId() ?>" />
	</div>
	<div class="row">	    
		<input
			type="text"
			name="dialog[ownPerson][<?php echo $n; ?>][name]"
			placeholder="<?php echo __('person_placeholder_name'); ?>"
			value="<?php echo htmlspecialchars($person->name); ?>" />
	</div>
</div>
