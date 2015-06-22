<?php
/**
 * Partial of fee header information.
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
            <?php echo __('card_label_rerule') ?>
        </label>
    </div>
    <div class="span2">
        <label
            for="card-pricetype"
            class="<?php echo ($record->hasError('pricetype')) ? 'error' : ''; ?>">
            <?php echo __('card_label_pricetype') ?>
        </label>
    </div>
    <div class="span2">
        <label
            for="card-feetype"
            class="<?php echo ($record->hasError('feetype')) ? 'error' : ''; ?>">
            <?php echo __('card_label_feetype') ?>
        </label>
    </div>
    <div class="span2">
        <label
            for="card-revenueaccount"
            class="<?php echo ($record->hasError('revenueaccount')) ? 'error' : ''; ?>">
            <?php echo __('card_label_revenueaccount') ?>
        </label>
    </div>
    <div class="span1">
        <label
            for="card-feeorderneeded"
            class="<?php echo ($record->hasError('feeorderneeded')) ? 'error' : ''; ?>">
            <?php echo __('card_label_feeorderneeded') ?>
        </label>
    </div>
    <div class="span1">
        <label
            for="card-onhold"
            class="<?php echo ($record->hasError('onhold')) ? 'error' : ''; ?>">
            <?php echo __('card_label_onhold') ?>
        </label>
    </div>
    <div class="span1">
        <label
            for="card-fee-inactive"
            class="<?php echo ($record->hasError('noannuals')) ? 'error' : ''; ?>">
            <?php echo __('card_label_feeinactive_perv') ?>
        </label>
    </div>
</div>
<div class="row">
    <div class="span3">
        <input type="hidden" id="card-rerule-switch" name="card-rerule-switch" value="1" />
        <button
            id="card-rerule"
            type="button"
            class="ir updateonclick card-rerule ask"
            name="rerule"
            title="<?php echo __('card_rerule_hint') ?>"
            data-target="card-fee-lineitems"
            data-href="<?php echo $this->url(sprintf('/card/fee/%d/', $record->getId())) ?>"
            data-fragments='<?php echo json_encode(array('card-pricetype' => 'on', 'card-country' => 'on', 'card-cardtype' => 'on', 'card-rerule-switch' => 'on')) ?>'>
            <?php echo __('card_rerule') ?>
        </button>
    </div>
    <div class="span2">
        <select
            id="card-pricetype"
            class="updateonchange"
            name="dialog[pricetype][id]"
            data-target="card-fee-lineitems"
            data-href="<?php echo $this->url(sprintf('/card/fee/%d/', $record->getId())) ?>"
            data-fragments='<?php echo json_encode(array('card-pricetype' => 'on', 'card-country' => 'on', 'card-cardtype' => 'on')) ?>'>
            <option value=""><?php echo __('select_a_option') ?></option>
            <?php foreach ($pricetypes as $_pricetype_id => $_pricetype): ?>
            <option
                value="<?php echo $_pricetype->getId() ?>"
                <?php echo ($record->pricetype()->getId() == $_pricetype->getId()) ? self::SELECTED : '' ?>><?php echo $_pricetype->name ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="span2">
        <select
            id="card-feetype"
            name="dialog[feetype][id]">
            <option value=""><?php echo __('select_a_option') ?></option>
            <?php foreach ($feetypes as $_feetype_id => $_feetype): ?>
            <option
                value="<?php echo $_feetype->getId() ?>"
                <?php echo ($record->feetype()->getId() == $_feetype->getId()) ? self::SELECTED : '' ?>><?php echo $_feetype->name ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <div class="span2">
        <input
            id="card-revenueaccount"
            type="text"
            name="dialog[revenueaccount]"
            value="<?php echo htmlspecialchars($record->revenueaccount) ?>" />
    </div>
    <div class="span1">
        <input
            type="hidden"
            name="dialog[feeorderneeded]"
            value="0" />
        <input
            id="card-feeorderneeded"
            type="checkbox"
            name="dialog[feeorderneeded]"
            <?php echo ($record->feeorderneeded) ? self::CHECKED : '' ?>
            value="1" />
    </div>
    <div class="span1">
        <input
            type="hidden"
            name="dialog[onhold]"
            value="0" />
        <input
            id="card-onhold"
            type="checkbox"
            name="dialog[onhold]"
            <?php echo ($record->onhold) ? self::CHECKED : '' ?>
            value="1" />
    </div>
    <div class="span1">
        <input
            type="hidden"
            name="dialog[feeinactive]"
            value="0" />
        <input
            id="card-fee-inactive"
            type="checkbox"
            name="dialog[feeinactive]"
            <?php echo ($record->feeinactive) ? self::CHECKED : '' ?>
            value="1" />
    </div>
</div>
<div class="row">
    <label
        for="card-customeraccount"
        class="<?php echo ($record->hasError('customeraccount')) ? 'error' : ''; ?>">
        <?php echo __('card_label_customeraccount') ?>
    </label>
    <input
        id="card-customeraccount"
        type="text"
        name="dialog[customeraccount]"
        value="<?php echo htmlspecialchars($record->customeraccount) ?>" />
</div>
<div class="row">        
    <label
        for="card-feesubject"
        class="<?php echo ($record->hasError('feesubject')) ? 'error' : ''; ?>">
        <?php echo __('card_label_feesubject') ?>
    </label>
    <textarea
        id="card-feesubject"
        name="dialog[feesubject]"
        class="scaleable"
        placeholder="<?php echo __('card_placeholder_feesubject') ?>"><?php echo htmlspecialchars($record->feesubject) ?></textarea>
</div>

<!-- Start of fee lineitems based upon country, cardtype and pricetype -->
<fieldset>
    <legend class="verbose"><?php echo __('card_legend_fee_lineitems') ?></legend>
    
    <div id="card-fee-lineitems">
    <!-- replacement area -->
    <?php if ( ! $_lineitems = $record->own('cardfeestep', false)): ?>
        <?php $_lineitems = array(R::dispense('cardfeestep')) ?>
    <?php endif ?>
    <?php echo $this->partial('model/card/form/fee/cardfeesteps', array('cardfeesteps' => $_lineitems)) ?>
    </div>
</fieldset>
<!-- End of fee lineitems based upon country, cardtype and pricetype -->