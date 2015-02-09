<?php
/**
 * Smtp fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('smtp_legend') ?></legend>
    <div class="row">
        <label
            for="smtp-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('smtp_label_name') ?>
        </label>
        <input
            id="smtp-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            placeholder="<?php echo __('smtp_placeholder_name') ?>"
            required="required" />
    </div>
    <div class="row">
           <input
               type="hidden"
               name="dialog[enabled]"
               value="0" />
           <input
               id="country-enabled"
               type="checkbox"
               name="dialog[enabled]"
               <?php echo ($record->enabled) ? self::CHECKED : '' ?>
               value="1" />
           <label
               for="country-enabled"
               class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
               <?php echo __('smtp_label_enabled') ?>
           </label>
       </div>
    <div class="row">
        <label
            for="smtp-host"
            class="<?php echo ($record->hasError('host')) ? 'error' : ''; ?>">
            <?php echo __('smtp_label_host') ?>
        </label>
        <input
            id="smtp-host"
            type="text"
            name="dialog[host]"
            value="<?php echo htmlspecialchars($record->host) ?>"
            placeholder="<?php echo __('smtp_placeholder_host') ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="smtp-port"
            class="<?php echo ($record->hasError('port')) ? 'error' : ''; ?>">
            <?php echo __('smtp_label_port') ?>
        </label>
        <input
            id="smtp-port"
            type="number"
            size="5"
            min="0"
            step="1"
            name="dialog[port]"
            value="<?php echo htmlspecialchars($record->port) ?>"
            placeholder="<?php echo __('smtp_placeholder_port') ?>" />
    </div>
    <div class="row">
        <label
            for="smtp-user"
            class="<?php echo ($record->hasError('user')) ? 'error' : ''; ?>">
            <?php echo __('smtp_label_user') ?>
        </label>
        <input
            id="smtp-user"
            type="text"
            name="dialog[user]"
            value="<?php echo htmlspecialchars($record->user) ?>"
            placeholder="<?php echo __('smtp_placeholder_user') ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="smtp-pw"
            class="<?php echo ($record->hasError('pw')) ? 'error' : ''; ?>">
            <?php echo __('smtp_label_pw') ?>
        </label>
        <input
            id="smtp-pw"
            type="password"
            name="dialog[pw]"
            value="<?php echo htmlspecialchars($record->pw) ?>" />
    </div>
</fieldset>
