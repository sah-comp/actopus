<?php
/**
 * Cardstatus fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('cardstatus_legend') ?></legend>
    <div class="row">
        <label
            for="cardstatus-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('cardstatus_label_name') ?>
        </label>
        <input
            id="cardstatus-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
</fieldset>

