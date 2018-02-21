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
<div class="row">
    <label
        for="card-fee-due-date"
        class="<?php echo ($record->hasError('feeduedate')) ? 'error' : ''; ?>">
        <?php echo __('card_label_feeduedate') ?>
    </label>
    <input
        id="card-fee-due-date"
        type="text"
        name="dialog[feeduedate]"
        value="<?php echo $this->date($record->feeduedate) ?>" />
</div>
<!-- fee steps when a rule is set -->
<section class="biglistsmalltext">
<div class="row">
    <div class="span2">
        <label><?php echo __('cardfeestep_label_year') ?></label>
    </div>
    <div class="span1">
        <label><?php echo __('cardfeestep_label_royalty') ?></label>
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
            <div class="span5"><?php echo __('cardfeestep_label_date') ?></div>
            <div class="span5"><?php echo __('cardfeestep_label_net') ?></div>
            <div class="span2">&nbsp;</div>
            <!--<div class="span6"><?php echo __('cardfeestep_label_paymentstyle') ?></div>-->
        </div>
    </div>
</div>
<div id="cardfeestep-container" class="container attachable detachable cardfeestep">
<?php foreach ($cardfeesteps as $_id => $_cardfeestep): ?>
<div
    id="cardfeestep-<?php echo $_id ?>"
    class="row feestep">
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
            name="dialog[ownCardfeestep][<?php echo $_id ?>][sequence]"
            value="<?php echo $_cardfeestep->sequence ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][fy]"
            value="<?php echo htmlspecialchars($_cardfeestep->fy) ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][net]"
            value="<?php echo $this->decimal($_cardfeestep->net, 2) ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][additional]"
            value="<?php echo $this->decimal($_cardfeestep->additional, 2) ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][done]"
            value="0" />
        <input
            type="checkbox"
            class="cb"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][done]"
            <?php echo ($_cardfeestep->done) ? self::CHECKED : '' ?>
            value="1" />
        <?php //echo $_cardfeestep->fy?>
        <input
            type="text"
            class="autowidth"
            style="width: 5em;"
            name="dialog[ownCardfeestep][<?php echo $_id ?>][fy]"
            value="<?php echo $_cardfeestep->fy ?>" />
        <a
    		href="<?php echo $this->url(sprintf('/card/detach/own/cardfeestep/%d', $_id)) ?>"
    		class="detach ask"
    		data-target="cardfeestep-<?php echo $_id ?>">
    			<span><?php echo __('scaffold_detach') ?></span>
    	</a>
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
            <div class="span5">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymentdate]"
                    value="<?php echo $this->date($_cardfeestep->paymentdate) ?>" />
            </div>
            <div class="span5">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymentnet]"
                    value="<?php echo $this->decimal($_cardfeestep->paymentnet, 2) ?>" />
            </div>
            <div class="span2">
                &nbsp;
            </div>
            <!--
            <div class="span3">
                <select
                    name="dialog[ownCardfeestep][<?php echo $_id ?>][paymentstyle]">
                    <?php foreach ($paymentstyles as $_paymentstyle_id => $_paymentstyle): ?>
                    <option
                        value="<?php echo $_paymentstyle->code ?>"
                        <?php echo ($_cardfeestep->paymentstyle == $_paymentstyle->code) ? self::SELECTED : '' ?>><?php echo $_paymentstyle->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            <div class="span3">
                <?php echo $_cardfeestep->genLink($this) ?>
            </div>
            -->
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
