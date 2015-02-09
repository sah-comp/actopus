<?php
/**
 * Todo fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('todo_legend') ?></legend>
    <div class="row">
        <label
            for="todo-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('todo_label_name') ?>
        </label>
        
        <textarea
            id="todo-name"
            class="scaleable"
            name="dialog[name]"
            rows="2"><?php echo htmlspecialchars($record->name) ?></textarea>
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[finished]"
            value="0" />
        <input
            id="todo-finished"
            type="checkbox"
            name="dialog[finished]"
            <?php echo ($record->finished) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="todo-finished"
            class="cb <?php echo ($record->hasError('finished')) ? 'error' : ''; ?>">
            <?php echo __('todo_label_finished') ?>
        </label>
    </div>
    
</fieldset>
<div id="todo-tabs" class="bar tabbed">
    <?php echo $this->tabbed('todo-tabs', array(
        'todo-children' => __('todo_tab_children')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="todo-children"
        class="tab">
        <legend class="verbose"><?php echo __('todo_legend_todo') ?></legend>
        
    	<div class="row">
        	<div class="span3"><?php echo __('todo_label_finished') ?></div>
        	<div class="span9"><?php echo __('todo_label_name') ?></div>
    	</div>
        
        <div
            id="todo-container"
            class="container attachable detachable sortable todo"
            title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
            data-href="<?php echo $this->url(sprintf('/todo/sortable/todo/todo')) ?>"
            data-container="todo-container"
            data-variable="todo">
        <?php foreach ($record->own('todo', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'todo'), array('n' => $_n, 'todo' => $_record)) ?>
        <?php endforeach ?>    
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'todo')) ?>"
    			class="attach"
    			data-target="todo-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>