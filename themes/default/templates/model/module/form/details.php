<?php
/**
 * Module fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('module_legend') ?></legend>
    <div class="row">
        <label
            for="module-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('module_label_name') ?>
        </label>
        <input
            id="module-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[enabled]"
            value="0" />
        <input
            id="module-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="module-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('module_label_enabled') ?>
        </label>
    </div>
</fieldset>
