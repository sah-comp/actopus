<?php
/**
 * Partial for a fee based on a perpetual rule.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div class="row">
    <div class="span3">
        <label>
            <?php echo __('fee_rule_perpetual_template', $rule->period) ?>
        </label>
    </div>
    <div class="span3">
        <label>
            <?php echo __('fee_label_net') ?>
        </label>
    </div>
    <div class="span3">
        <label>
            <?php echo __('fee_label_additional') ?>
        </label>
    </div>
</div>
<div class="row">
    <div class="span3">&nbsp;</div>
    <div class="span3">
        <select
            id="fee-multiplier"
            class="autowidth"
            name="dialog[multiplier]">
            <?php foreach ($multipliers as $_multiplier): ?>
            <option
                value="<?php echo $_multiplier ?>"
                <?php echo ($record->multiplier == $_multiplier) ? self::SELECTED : '' ?>><?php echo __('fee_multiplier_'.$_multiplier) ?></option>
            <?php endforeach ?>
        </select>
    </div>
</div>
<div class="row">
    <div class="span3">
        <input
            id="fee-included"
            style="width: 3em;"
            type="number"
            name="dialog[included]"
            value="<?php echo htmlspecialchars($fee->included) ?>" />
            <label
                for="fee-included"
                style="width: auto; float: none;">
                <?php echo __('fee_label_included') ?>
            </label>
    </div>
    <div class="span3">
        <input
            type="text"
            class="number"
            name="dialog[netincluded]"
            value="<?php echo $this->decimal($fee->netincluded, 2) ?>" />
    </div>
    <div class="span3">
        <input
            type="text"
            class="number"
            name="dialog[additionalincluded]"
            value="<?php echo $this->decimal($fee->additionalincluded, 2) ?>" />
    </div>
</div>
<div class="row">
    <div class="span3">
        <label>
            <?php echo __('fee_label_excluded') ?>
        </label>
    </div>
    <div class="span3">
        <input
            type="text"
            class="number"
            name="dialog[netexcluded]"
            value="<?php echo $this->decimal($fee->netexcluded, 2) ?>" />
    </div>
    <div class="span3">
        <input
            type="text"
            class="number"
            name="dialog[additionalexcluded]"
            value="<?php echo $this->decimal($fee->additionalexcluded, 2) ?>" />
    </div>
</div>
