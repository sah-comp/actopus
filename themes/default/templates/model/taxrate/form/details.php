<?php
/**
 * Taxrate fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('taxrate_legend') ?></legend>
    <div class="row">
        <label
            for="taxrate-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('taxrate_label_name') ?>
        </label>
        <input
            id="taxrate-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="taxrate-percentage"
            class="<?php echo ($record->hasError('percentage')) ? 'error' : ''; ?>">
            <?php echo __('taxrate_label_percentage') ?>
        </label>
        <input
            id="taxrate-percentage"
            class="number"
            size="6"
            maxlength="8"
            type="text"
            name="dialog[percentage]"
            value="<?php echo htmlspecialchars($this->decimal($record->percentage)) ?>" />
    </div>
</fieldset>

