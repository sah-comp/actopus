<?php
/**
 * ownTodo of Todo fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="todo-<?php echo $n ?>" class="item todo">    
	<a
		href="<?php echo $this->url(sprintf('/todo/detach/own/todo/%d', $n)) ?>"
		class="detach"
		data-target="todo-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownTodo][<?php echo $n ?>][type]" value="todo" />
		<input type="hidden" name="dialog[ownTodo][<?php echo $n ?>][id]" value="<?php echo $todo->getId() ?>" />
	</div>
	<div class="row">
	    <div class="span3">	 
            <input
                type="hidden"
                name="dialog[ownTodo][<?php echo $n ?>][finished]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownTodo][<?php echo $n ?>][finished]"
                <?php echo ($todo->finished) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	    <div class="span9">	    
    		<textarea
    			name="dialog[ownTodo][<?php echo $n; ?>][name]"
    			class="scaleable"
    			rows="2"
    			placeholder="<?php echo __('todo_placeholder_name'); ?>"><?php echo htmlspecialchars($todo->name); ?></textarea>
    	</div>
	</div>
</div>
