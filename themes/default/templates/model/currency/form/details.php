<?php
/**
 * Currency fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('currency_legend') ?></legend>
    <div class="row">
        <label
            for="currency-iso"
            class="<?php echo ($record->hasError('iso')) ? 'error' : ''; ?>">
            <?php echo __('currency_label_iso') ?>
        </label>
        <input
            id="action-iso"
            type="text"
            name="dialog[iso]"
            value="<?php echo htmlspecialchars($record->iso) ?>"
            required="required" />
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[enabled]"
            value="0" />
        <input
            id="currency-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
        <label
            for="currency-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('currency_label_enabled') ?>
        </label>
    </div>
    <div class="row">
        <label
            for="currency-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('currency_label_name') ?>
        </label>
        <input
            id="action-currency"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="currency-sign"
            class="<?php echo ($record->hasError('sign')) ? 'error' : ''; ?>">
            <?php echo __('currency_label_sign') ?>
        </label>
        <input
            id="action-sign"
            type="text"
            name="dialog[sign]"
            value="<?php echo htmlspecialchars($record->sign) ?>" />
    </div>
    <div class="row">
        <label
            for="currency-fractionalunit"
            class="<?php echo ($record->hasError('fractionalunit')) ? 'error' : ''; ?>">
            <?php echo __('currency_label_fractionalunit') ?>
        </label>
        <input
            id="action-fractionalunit"
            type="text"
            name="dialog[fractionalunit]"
            value="<?php echo htmlspecialchars($record->fractionalunit) ?>" />
    </div>
    <div class="row">
        <label
            for="currency-numbertobasic"
            class="<?php echo ($record->hasError('numbertobasic')) ? 'error' : ''; ?>">
            <?php echo __('currency_label_numbertobasic') ?>
        </label>
        <input
            id="action-numbertobasic"
            type="number"
            name="dialog[numbertobasic]"
            value="<?php echo htmlspecialchars($record->numbertobasic) ?>" />
    </div>
    <div class="row">
        <label
            for="currency-exchangerate"
            class="<?php echo ($record->hasError('exchangerate')) ? 'error' : ''; ?>">
            <?php echo '1 '.$this->basecurrency->name ?>
        </label>
        <input
            id="currency-exchangerate"
            type="text"
            class="number"
            size="6"
            maxlength="8"
            name="dialog[exchangerate]"
            value="<?php echo htmlspecialchars($this->decimal($record->exchangerate, 4)) ?>" />
        <?php if ($this->basecurrency->iso == $record->iso): ?>
            <p class="info"><?php echo __('currency_hint_i_am_base') ?></p>
        <?php else: ?>
            <p class="info"><?php echo __('currency_hint_exchangerate', array($this->timestamp($this->setting->tsexchangerate, 'date'))) ?></p>
        <?php endif ?>
    </div>
</fieldset>

