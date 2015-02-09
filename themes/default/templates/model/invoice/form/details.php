<?php
/**
 * Invoice fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div>
    <input type="hidden" name="dialog[invoicetype][type]" value="invoicetype" />
    <input type="hidden" name="dialog[invoicetype][id]" value="<?php echo $record->invoicetype()->getId() ?>" />
    <input type="hidden" name="dialog[user][type]" value="user" />
    <input type="hidden" name="dialog[user][id]" value="<?php echo $record->user()->getId() ?>" />
    <input type="hidden" name="dialog[attorney][type]" value="user" />
    <input type="hidden" id="attorney-id" name="dialog[attorney][id]" value="<?php echo $record->attorney_id ?>" />
</div>
<fieldset>
    <legend class="verbose"><?php echo __('invoice_legend') ?></legend>
    
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <label
                for="invoice-name"
                class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>"><?php echo __('invoice_label_name') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="invoice-invoicetype"
                class="<?php echo ($record->hasError('invoicetype_id')) ? 'error' : ''; ?>"><?php echo __('invoice_label_invoicetype') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="invoice-invoicedate"
                class="<?php echo ($record->hasError('invoicedate')) ? 'error' : ''; ?>"><?php echo __('invoice_label_invoicedate') ?>
            </label>
        </div>
        <div class="span3">
            <label
                for="invoice-user"
                class="<?php echo ($record->hasError('user_id')) ? 'error' : ''; ?>"><?php echo __('invoice_label_user_id') ?>
            </label>
        </div>
    </div>
    
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <input
                id="invoice-name"
                type="text"
                name="dialog[name]"
                value="<?php echo ($record->getId()) ? htmlspecialchars($record->name) : '' ?>"
                placeholder="<?php echo __('invoice_placeholder_name') ?>"
                readonly="readonly" />
        </div>
        <div class="span2">
            <select
                id="invoice-invoicetype"
                class="autowidth"
                name="dialog[invoicetype][id]"
                <?php echo ($record->getId()) ? self::DISABLED : '' ?>>
                <?php foreach ($invoicetypes as $_invoicetype_id => $_invoicetype): ?>
                <option
                    value="<?php echo $_invoicetype->getId() ?>"
                    <?php echo ($record->invoicetype()->getId() == $_invoicetype->getId()) ? self::SELECTED : '' ?>><?php echo $_invoicetype->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="span2">
            <input
                id="invoice-invoicedate"
                type="text"
                name="dialog[invoicedate]"
                value="<?php echo $this->date($record->invoicedate) ?>"
                placeholder="<?php echo __('invoice_placeholder_invoicedate') ?>"
                <?php echo ($record->getId()) ? self::READONLY : '' ?> />
        </div>
        <div class="span3">
            <?php if ($record->getId()): ?>
                <?php echo htmlspecialchars($record->user()->name()) ?>
            <?php else: ?>
                <?php echo htmlspecialchars($user->name()) ?>
            <?php endif ?>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend class="verbose"><?php echo __('invoice_card_legend') ?></legend>
    <div class="row">

        <input
            id="invoice-card"
            type="hidden"
            name="dialog[card_id]"
            value="<?php echo $record->card_id ?>" />
        <label
            for="card-name"
            class="<?php echo ($record->hasError('cardname')) ? 'error' : ''; ?>">
            <?php echo __('invoice_label_cardname') ?>
        </label>
        <input
            id="card-name"
            type="text"
            name="dialog[cardname]"
            class="autocomplete"
            data-source="<?php echo $this->url('/search/autocomplete/card/name?callback=?') ?>"
            data-spread='<?php echo json_encode(array('attorney-id' => 'attorney_id', 'invoice-card' => 'id', 'card-name' => 'cardname', 'client-address' => 'address', 'invoice-client' => 'client_id', 'invoice-clientcode' => 'code', 'client-nickname' => 'nickname')) ?>'
            value="<?php echo htmlspecialchars($record->cardname) ?>"
            placeholder="<?php echo __('invoice_placeholder_cardname') ?>" />
        <?php echo $this->beanlink($record->card(), 'name') ?>
        <p class="info"><?php echo __('invoice_hint_cardname') ?></p>
    </div>
</fieldset>
<fieldset>
    <legend class="verbose"><?php echo __('invoice_client_legend') ?></legend>
    <div class="row">

        <input
            id="invoice-client"
            type="hidden"
            name="dialog[client_id]"
            value="<?php echo $record->client_id ?>" />
        <label
            for="client-nickname"
            class="<?php echo ($record->hasError('clientnickname')) ? 'error' : ''; ?>">
            <?php echo __('invoice_label_clientnickname') ?>
        </label>
        <input
            id="client-nickname"
            type="text"
            name="dialog[clientnickname]"
            class="autocomplete"
            data-source="<?php echo $this->url('/search/autocomplete/person/nickname?callback=?') ?>"
            data-spread='<?php echo json_encode(array('invoice-client' => 'person_id', 'client-nickname' => 'nickname', 'client-address' => 'address')) ?>'
            value="<?php echo htmlspecialchars($record->clientnickname) ?>"
            placeholder="<?php echo __('invoice_placeholder_clientnickname') ?>" />
        <?php echo $this->beanlink($record->client(), 'name') ?>
        <p class="info"><?php echo __('invoice_hint_clientnickname') ?></p>
    </div>
    <div class="row">        
        <label
            for="client-address"
            class="<?php echo ($record->hasError('clientaddress')) ? 'error' : ''; ?>">
            <?php echo __('invoice_label_clientaddress') ?>
        </label>
        <textarea
            id="client-address"
            name="dialog[clientaddress]"
            rows="6"
            placeholder="<?php echo __('invoice_placeholder_clientaddress') ?>"><?php echo htmlspecialchars($record->clientaddress) ?></textarea>
    </div>

    <div class="row">        
        <label
            for="invoice-clientcode"
            class="<?php echo ($record->hasError('clientcode')) ? 'error' : ''; ?>">
            <?php echo __('invoice_label_clientcode') ?>
        </label>
        <input
            id="invoice-clientcode"
            type="text"
            name="dialog[clientcode]"
            value="<?php echo htmlspecialchars($record->clientcode) ?>"
            placeholder="<?php echo __('invoice_placeholder_clientcode') ?>" />
    </div>
</fieldset>
