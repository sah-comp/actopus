<?php
/**
 * ownCardfeestep of Card fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div class="row feestep">
    <div class="span2">
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $n ?>][type]"
            value="<?php echo $cardfeestep->getMeta('type') ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $n ?>][id]"
            value="<?php echo $cardfeestep->getId() ?>" />
        <input
            type="hidden"
            name="dialog[ownCardfeestep][<?php echo $n ?>][done]"
            value="0" />
        <input
            type="checkbox"
            name="dialog[ownCardfeestep][<?php echo $n ?>][done]"
            <?php echo ($cardfeestep->done) ? self::CHECKED : '' ?>
            value="1" />
        <input
            type="text"
            style="width: 5em;"
            name="dialog[ownCardfeestep][<?php echo $n ?>][fy]"
            value="<?php echo $cardfeestep->fy ?>" />
    </div>
    <div class="span1">
        <?php echo $this->decimal($cardfeestep->net, 2) ?>&nbsp;
    </div>

    <div class="span2">
        <div class="row">
            <div class="span6">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][awarenessdate]"
                    value="<?php echo $this->date($cardfeestep->awarenessdate) ?>" />
            </div>
            <div class="span6">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][awarenessnet]"
                    value="<?php echo $this->decimal($cardfeestep->awarenessnet, 2) ?>" />
            </div>
        </div>
    </div>

    <div class="span1">
        <div class="row">
            <div class="span12">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][orderdate]"
                    value="<?php echo $this->date($cardfeestep->orderdate) ?>" />
            </div>
        </div>
    </div>

    <div class="span2">
        <div class="row">
            <div class="span6">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][invoicedate]"
                    value="<?php echo $this->date($cardfeestep->invoicedate) ?>" />
            </div>
            <div class="span6">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][invoicenet]"
                    value="<?php echo $this->decimal($cardfeestep->invoicenet, 2) ?>" />
            </div>
        </div>
    </div>

    <div class="span4">
        <div class="row">
            <div class="span5">
                <input
                    type="text"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][paymentdate]"
                    value="<?php echo $this->date($cardfeestep->paymentdate) ?>" />
            </div>
            <div class="span5">
                <input
                    type="text"
                    class="number"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][paymentnet]"
                    value="<?php echo $this->decimal($cardfeestep->paymentnet, 2) ?>" />
            </div>
            <div class="span2">&nbsp;</div>
            <!--
            <div class="span3">
                <select
                    name="dialog[ownCardfeestep][<?php echo $n ?>][paymentstyle]">
                    <?php foreach (R::find('paymentstyle', ' 1 ORDER BY code') as $_paymentstyle_id => $_paymentstyle): ?>
                    <option
                        value="<?php echo $_paymentstyle->code ?>"
                        <?php echo ($cardfeestep->paymentstyle == $_paymentstyle->code) ? self::SELECTED : '' ?>><?php echo $_paymentstyle->name ?></option>
                    <?php endforeach ?>
                </select>
            </div>

            <div class="span1">
                <?php echo $cardfeestep->genLink($this) ?>
            </div>

            <div class="span2">
                <input
                    type="hidden"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][paymenthold]"
                    value="0" />
                <input
                    type="checkbox"
                    name="dialog[ownCardfeestep][<?php echo $n ?>][paymenthold]"
                    <?php echo ($cardfeestep->paymenthold) ? self::CHECKED : '' ?>
                    value="1" />
            </div>
            -->
        </div>
    </div>

</div>
