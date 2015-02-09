<?php
/**
 * Domain fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('domain_legend') ?></legend>
    <div class="row">
        <label
            for="domain-parent"
            class="<?php echo ($record->hasError('domain_id')) ? 'error' : ''; ?>">
            <?php echo __('domain_label_parent') ?>
        </label>
        <select
            id="domain-parent"
            name="dialog[domain_id]">
            <option value="0"><?php echo __('domain_root') ?></option>
            <?php foreach ($domains as $_id => $_domain): ?>
            <option
                value="<?php echo $_domain->getId() ?>"
                <?php echo ($record->domain_id == $_domain->getId()) ? self::SELECTED : '' ?>><?php echo __('domain_'.$_domain->name) ?></option>   
            <?php endforeach ?>
        </select>
    </div>
    <div class="row">
        <label
            for="domain-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('domain_label_name') ?>
        </label>
        <input
            id="domain-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
        <p class="info"><?php echo __('domain_hint_name') ?></p>
    </div>
    <div class="row">
        <label
            for="domain-url"
            class="<?php echo ($record->hasError('url')) ? 'error' : ''; ?>">
            <?php echo __('domain_label_url') ?>
        </label>
        <input
            id="domain-url"
            type="text"
            name="dialog[url]"
            value="<?php echo htmlspecialchars($record->url) ?>" />
    </div>
    <div class="row">
        <label
            for="domain-sequence"
            class="<?php echo ($record->hasError('sequence')) ? 'error' : ''; ?>">
            <?php echo __('domain_label_sequence') ?>
        </label>
        <input
            id="domain-sequence"
            type="number"
            min="0"
            step="10"
            name="dialog[sequence]"
            value="<?php echo htmlspecialchars($record->sequence) ?>" />
        <p class="info"><?php echo __('domain_hint_sequence') ?></p>
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[invisible]"
            value="0" />
        <input
            id="domain-invisible"
            type="checkbox"
            name="dialog[invisible]"
            <?php echo ($record->invisible) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="domain-invisible"
            class="cb <?php echo ($record->hasError('invisible')) ? 'error' : ''; ?>">
            <?php echo __('domain_label_invisible') ?>
        </label>
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[blessed]"
            value="0" />
        <input
            id="domain-blessed"
            type="checkbox"
            name="dialog[blessed]"
            <?php echo ($record->blessed) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="domain-blessed"
            class="cb <?php echo ($record->hasError('blessed')) ? 'error' : ''; ?>">
            <?php echo __('domain_label_blessed') ?>
        </label>
    </div>
</fieldset>
<div id="domain-tabs" class="bar tabbed">
    <?php echo $this->tabbed('domain-tabs', array(
        'domain-children' => __('domain_tab_children')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="domain-children"
        class="tab">
        <legend class="verbose"><?php echo __('domain_legend_domain') ?></legend>

    	<div class="row">
    	    <div class="span3"><?php echo __('domain_label_name') ?></div>
        	<div class="span6"><?php echo __('domain_label_url') ?></div>
        	<div class="span3"><?php echo __('domain_label_invisible') ?></div>
    	</div>

        <div
            id="domain-container"
            class="container attachable detachable sortable domain"
            title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
            data-href="<?php echo $this->url(sprintf('/domain/sortable/domain/domain')) ?>"
            data-container="domain-container"
            data-variable="domain">
        <?php foreach ($record->own('domain', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'domain'), array('n' => $_n, 'domain' => $_record)) ?>
        <?php endforeach ?>
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'domain')) ?>"
    			class="attach"
    			data-target="domain-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
</div>
