<?php
/**
 * Rule fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div>    
    <input type="hidden" name="dialog[rule][type]" value="rule" />
    <input type="hidden" name="dialog[rule][id]" value="" />
    <input type="hidden" name="dialog[pricetype][type]" value="pricetype" />
    <input type="hidden" name="dialog[pricetype][id]" value="" />
</div>
<fieldset class="sticky">
    <legend class="verbose"><?php echo __('fee_legend') ?></legend>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span3">
            <label
                for="fee-rule"
                class="left <?php echo ($record->hasError('rule_id')) ? 'error' : ''; ?>"><?php echo __('fee_label_rule') ?>
            </label>
        </div>
        <div class="span3">
            <label
                for="fee-pricetype"
                class="left <?php echo ($record->hasError('pricetype_id')) ? 'error' : ''; ?>"><?php echo __('fee_label_pricetype') ?>
            </label>
        </div>
    </div>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span3">
            <select
                id="fee-rule"
                class="autowidth updateonchange"
                name="dialog[rule][id]"
                data-target="fee-step"
                data-href="<?php echo $this->url(sprintf('/fee/rule/%d/', $record->getId())) ?>"
                data-fragments='<?php echo json_encode(array('fee-rule' => 'on')) ?>'>
                <option value="0"><?php echo __('fee_select_a_rule') ?></option>
                <?php foreach ($rules as $_rule_id => $_rule): ?>
                <option
                    value="<?php echo $_rule->getId() ?>"
                    <?php echo ($record->rule()->getId() == $_rule->getId()) ? self::SELECTED : '' ?>><?php echo $_rule->displayName() ?></option>
                <?php endforeach ?>
            </select>
        </div>


        <div class="span3">
            <select
                id="fee-pricetype"
                class="autowidth"
                name="dialog[pricetype][id]">
                <?php foreach ($pricetypes as $_feeype_id => $_pricetype): ?>
                <option
                    value="<?php echo $_pricetype->getId() ?>"
                    <?php echo ($record->pricetype()->getId() == $_pricetype->getId()) ? self::SELECTED : '' ?>><?php echo $_pricetype->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend class="verbose"><?php echo __('fee_legend_description') ?></legend>
    <div class="row">
        <label for="fee-description"><?php echo __('fee_label_description') ?></label>
        <textarea
            id="fee-description"
            class="scaleable"
            name="dialog[description]"><?php echo htmlspecialchars($record->description) ?></textarea>
    </div>
</fieldset>
<div id="fee-tabs" class="bar tabbed">
    <?php echo $this->tabbed('fee-tabs', array(
        'fee-step' => __('fee_tab_step')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="fee-step"
        class="tab">
        <legend class="verbose"><?php echo __('fee_legend_step') ?></legend>
        
        <?php if ($record->getId() && $record->rule()->getId()): ?>
            <?php echo $this->partial(sprintf('model/fee/form/rule/%s', $rulestyles[$record->rule()->style]), array('fee' => $record, 'rule' => $record->rule())) ?>
            <?php else: ?>
            <?php echo $this->partial('model/fee/form/rule/undefined', array('fee' => $record, 'rule' => $record->rule())) ?>
        <?php endif ?>
		
    </fieldset>
</div>
