<?php
/**
 * Role fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('role_legend') ?></legend>
    <div class="row">
        <label
            for="role-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('role_label_name') ?>
        </label>
        <input
            id="role-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="role-sequence"
            class="<?php echo ($record->hasError('sequence')) ? 'error' : ''; ?>">
            <?php echo __('role_label_sequence') ?>
        </label>
        <input
            id="role-sequence"
            type="number"
            name="dialog[sequence]"
            value="<?php echo htmlspecialchars($record->sequence) ?>"
            step="10" />
    </div>
</fieldset>
