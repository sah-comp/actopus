<?php
/**
 * Paymentstyle fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('paymentstyle_legend') ?></legend>
    <div class="row">
        <label
            for="paymentstyle-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('paymentstyle_label_name') ?>
        </label>
        <input
            id="paymentstyle-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="paymentstyle-code"
            class="<?php echo ($record->hasError('code')) ? 'error' : ''; ?>">
            <?php echo __('paymentstyle_label_code') ?>
        </label>
        <input
            id="paymentstyle-code"
            type="text"
            name="dialog[code]"
            value="<?php echo htmlspecialchars($record->code) ?>" />
    </div>
</fieldset>

