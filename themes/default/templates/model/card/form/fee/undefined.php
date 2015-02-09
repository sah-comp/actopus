<?php
/**
 * Partial for a fee for a card depending on country, cardtype and pricetype.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<!-- fee steps when no rule is set -->
<section class="biglistsmalltext">
<div class="row">
    <div class="span2">
        <label><?php echo __('cardfeestep_label_year') ?></label>
    </div>
    <div class="span1">
        <label><?php echo __('cardfeestep_label_net') ?></label>
    </div>
    <div class="span2">
        <label><?php echo __('cardfeestep_label_awareness') ?></label>
    </div>
    <div class="span1">
        <label><?php echo __('cardfeestep_label_order') ?></label>
    </div>
    <div class="span2">
        <label><?php echo __('cardfeestep_label_invoice') ?></label>
    </div>
    <div class="span4">
        <label><?php echo __('cardfeestep_label_payment') ?></label>
    </div>
</div>
<div class="row">
    <div class="span3">
        &nbsp;
    </div>
    <div class="span2">
        <div class="row">
            <div class="span6"><?php echo __('cardfeestep_label_date') ?></div>
            <div class="span6"><?php echo __('cardfeestep_label_net') ?></div>
        </div>
    </div>
    <div class="span1">
        <div class="row">
            <div class="span12"><?php echo __('cardfeestep_label_date') ?></div>
        </div>
    </div>
    <div class="span2">
        <div class="row">
            <div class="span6"><?php echo __('cardfeestep_label_date') ?></div>
            <div class="span6"><?php echo __('cardfeestep_label_net') ?></div>
        </div>
    </div>
    <div class="span4">
        <div class="row">
            <div class="span3"><?php echo __('cardfeestep_label_date') ?></div>
            <div class="span3"><?php echo __('cardfeestep_label_net') ?></div>
            <div class="span4"><?php echo __('cardfeestep_label_paymentstyle') ?></div>
            <div class="span2"><?php echo __('cardfeestep_label_paymenthold') ?></div>
        </div>
    </div>
</div>
<div id="cardfeestep-container" class="container attachable detachable cardfeestep">
<?php foreach ($cardfeesteps as $_id => $_cardfeestep): ?>
<div class="row feestep">
    <div class="span2">
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][type]"
            value="<?php echo $_cardfeestep->getMeta('type') ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][id]"
            value="<?php echo $_cardfeestep->getId() ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][done]"
            value="0" />
        <input
            type="checkbox"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][done]"
            <?php echo ($_cardfeestep->done) ? self::CHECKED : '' ?>
            value="1" />
        <?php //echo $_cardfeestep->fy ?>
        <input
            type="text"
            style="width: 5em;"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][fy]"
            value="<?php echo $_cardfeestep->fy ?>" />
    </div>
    <div class="span1">
        <?php echo $this->decimal($_cardfeestep->net, 2) ?>
    </div>

    <div class="span2">
        <div class="row">
            <div class="span6">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][awarenessdate]"
                    value="<?php echo $this->date($_cardfeestep->awarenessdate) ?>" />
            </div>
            <div class="span6">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][awarenessnet]"
                    value="<?php echo $this->decimal($_cardfeestep->awarenessnet, 2) ?>" />
            </div>
        </div>
    </div>
    
    <div class="span1">
        <div class="row">
            <div class="span12">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][orderdate]"
                    value="<?php echo $this->date($_cardfeestep->orderdate) ?>" />
            </div>
        </div>
    </div>
    
    <div class="span2">
        <div class="row">
            <div class="span6">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][invoicedate]"
                    value="<?php echo $this->date($_cardfeestep->invoicedate) ?>" />
            </div>
            <div class="span6">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][invoicenet]"
                    value="<?php echo $this->decimal($_cardfeestep->invoicenet, 2) ?>" />
            </div>
        </div>
    </div>
    
    <div class="span4">
        <div class="row">
            <div class="span3">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymentdate]"
                    value="<?php echo $this->date($_cardfeestep->paymentdate) ?>" />
            </div>
            <div class="span3">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymentnet]"
                    value="<?php echo $this->decimal($_cardfeestep->paymentnet, 2) ?>" />
            </div>
            <div class="span4">
                <select
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymentstyle]">
                    <?php foreach ($paymentstyles as $_paymentstyle_id => $_paymentstyle): ?>
                    <option
                        value="<?php echo $_paymentstyle->code ?>"
                        <?php echo ($record->paymentstyle == $_paymentstyle->code) ? self::SELECTED : '' ?>><?php echo $_paymentstyle->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="span2">
                <input
                    type="hidden"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymenthold]"
                    value="0" />
                <input
                    type="checkbox"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymenthold]"
                    <?php echo ($_cardfeestep->paymenthold) ? self::CHECKED : '' ?>
                    value="1" />
            </div>
        </div>
    </div>
    
</div>
<?php endforeach ?>
<a
	href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'cardfeestep')) ?>"
	class="attach"
	data-target="cardfeestep-container">
		<span><?php echo __('scaffold_attach') ?></span>
</a>
</div>
</section>