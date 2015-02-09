<?php
/**
 * Team fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('team_legend') ?></legend>
    <div class="row">
        <label
            for="team-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('team_label_name') ?>
        </label>
        <input
            id="team-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="team-sequence"
            class="<?php echo ($record->hasError('sequence')) ? 'error' : ''; ?>">
            <?php echo __('team_label_sequence') ?>
        </label>
        <input
            id="team-sequence"
            type="number"
            name="dialog[sequence]"
            value="<?php echo htmlspecialchars($record->sequence) ?>"
            step="10" />
    </div>
</fieldset>
