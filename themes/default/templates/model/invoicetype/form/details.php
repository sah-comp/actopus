<?php
/**
 * Invoicetype fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('invoicetype_legend') ?></legend>
    <div class="row">
        <label
            for="invoicetype-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('invoicetype_label_name') ?>
        </label>
        <input
            id="invoicetype-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="invoicetype-serial"
            class="<?php echo ($record->hasError('serial')) ? 'error' : ''; ?>">
            <?php echo __('invoicetype_label_serial') ?>
        </label>
        <input
            id="invoicetype-serial"
            type="number"
            name="dialog[serial]"
            value="<?php echo htmlspecialchars($record->serial) ?>"
            required="required" />
        <p class="info"><?php echo __('invoicetype_hint_serial') ?></p>
    </div>
</fieldset>

