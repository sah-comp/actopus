<?php
/**
 * Action fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('action_legend') ?></legend>
    <div class="row">
        <label
            for="action-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('action_label_name') ?>
        </label>
        <input
            id="action-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
</fieldset>

