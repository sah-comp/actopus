<?php
/**
 * Partial for a fee based on a limit(ed) rule.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div class="row">
    <div class="span3">&nbsp;</div>
    <div class="span3">
        <label for="fee-multiplier" class="<?php echo $fee->hasError('multiplier') ? 'error' : '' ?>">
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
<?php foreach ($rule->own('rulestep') as $_rulestep_id => $_rulestep): ?>
<?php $_feestep = $fee->getFeestep($_rulestep_id) ?>
<div>
    <input type="hidden" name="dialog[ownFeestep][<?php echo $_rulestep_id ?>][type]" value="<?php echo $_feestep->getMeta('type') ?>" />
    <input type="hidden" name="dialog[ownFeestep][<?php echo $_rulestep_id ?>][id]" value="<?php echo $_feestep->getId() ?>" />
    <input type="hidden" name="dialog[ownFeestep][<?php echo $_rulestep_id ?>][rulestep][type]" value="<?php echo $_rulestep->getMeta('type') ?>" />
    <input type="hidden" name="dialog[ownFeestep][<?php echo $_rulestep_id ?>][rulestep][id]" value="<?php echo $_rulestep->getId() ?>" />
</div>
<div class="row">
    <div class="span3 right">
        <?php echo $_rulestep->name ?>
    </div>
    <div class="span3">
        <input
            type="text"
            class="number"
            name="dialog[ownFeestep][<?php echo $_rulestep_id ?>][net]"
            value="<?php echo htmlspecialchars($this->decimal($_feestep->net, 2)) ?>" />
    </div>
    <div class="span3">
        <input
            type="text"
            class="number"
            name="dialog[ownFeestep][<?php echo $_rulestep_id ?>][additional]"
            value="<?php echo htmlspecialchars($this->decimal($_feestep->additional, 2)) ?>" />
    </div>
</div>
<?php endforeach ?>
