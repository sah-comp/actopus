<?php
/**
 * Card fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div>
    <input type="hidden" name="dialog[user][type]" value="user" />
    <input type="hidden" name="dialog[user][id]" value="" />
    <input type="hidden" name="dialog[country][type]" value="country" />
    <input type="hidden" name="dialog[country][id]" value="" />
    <input type="hidden" name="dialog[cardtype][type]" value="cardtype" />
    <input type="hidden" name="dialog[cardtype][id]" value="" />
    <input type="hidden" name="dialog[cardstatus][type]" value="cardstatus" />
    <input type="hidden" name="dialog[cardstatus][id]" value="" />
    <input type="hidden" name="dialog[pricetype][type]" value="pricetype" />
    <input type="hidden" name="dialog[pricetype][id]" value="" />
    <input type="hidden" name="dialog[feetype][type]" value="feetype" />
    <input type="hidden" name="dialog[feetype][id]" value="" />
</div>
<fieldset id="card-identification"
    class="sticky">
    <legend class="verbose"><?php echo __('card_legend') ?></legend>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <label
                for="card-name"
                class="left <?php echo ($record->hasError('name')) ? 'error' : ''; ?>"><?php echo __('card_label_name') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="card-country"
                class="left <?php echo ($record->hasError('country_id')) ? 'error' : ''; ?>"><?php echo __('card_label_country') ?>
            </label>
        </div>
        <div class="span1">
            <label
                for="card-cardtype"
                class="left <?php echo ($record->hasError('cardtype_id')) ? 'error' : '' ?>"><?php echo __('card_label_cardtype') ?>
            </label>
        </div>
        <div class="span1">
            <label
                for="card-cardstatus"
                class="left <?php echo ($record->hasError('cardstatus_id')) ? 'error' : ''; ?>"><?php echo __('card_label_status') ?>
            </label>
        </div>
        <div class="span1">
            <label
                for="card-user"
                class="left <?php echo ($record->hasError('user_id')) ? 'error' : ''; ?>"><?php echo __('card_label_attorney') ?>
            </label>
        </div>
        <div class="span2">
            <label
                for="card-original-name"
                class="left <?php echo ($record->hasError('original')) ? 'error' : ''; ?>"><?php echo __('card_label_original') ?>
            </label>
        </div>
    </div>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <input
                id="card-name"
                class="autowidth"
                type="text"
                name="dialog[name]"
                value="<?php echo htmlspecialchars($record->name) ?>"
                placeholder="<?php echo __('card_placeholder_name') ?>"
                required="required" />
                <a
                    class="ir familylink"
                    href="#family"
                    onclick="$('#card-family').toggle(); return false;"><?php echo __('card_toggle_family') ?></a>
        </div>



        <div class="span2">
            <select
                id="card-country"
                class="autowidth updateonchange"
                name="dialog[country][id]"
                data-target="card-fee-lineitems"
                data-href="<?php echo $this->url(sprintf('/card/fee/%d/', $record->getId())) ?>"
                data-fragments='<?php echo json_encode(array('card-pricetype' => 'on', 'card-country' => 'on', 'card-cardtype' => 'on')) ?>'>
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($countries as $_country_id => $_country): ?>
                <option
                    value="<?php echo $_country->getId() ?>"
                    <?php echo ($record->country()->getId() == $_country->getId()) ? self::SELECTED : '' ?>><?php echo $_country->name ?></option>
                <?php endforeach ?>
            </select>
        </div>


        <div class="span1">
            <select
                id="card-cardtype"
                class="autowidth updateonchange"
                name="dialog[cardtype][id]"
                data-target="card-fee-lineitems"
                data-href="<?php echo $this->url(sprintf('/card/fee/%d/', $record->getId())) ?>"
                data-fragments='<?php echo json_encode(array('card-pricetype' => 'on', 'card-country' => 'on', 'card-cardtype' => 'on')) ?>'>
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($cardtypes as $_cardtype_id => $_cardtype): ?>
                <option
                    value="<?php echo $_cardtype->getId() ?>"
                    <?php echo ($record->cardtype()->getId() == $_cardtype->getId()) ? self::SELECTED : '' ?>><?php echo $_cardtype->name ?></option>
                <?php endforeach ?>
            </select>
        </div>

        <div class="span1">
            <select
                id="card-cardstatus"
                class="autowidth"
                name="dialog[cardstatus][id]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($cardstati as $_cardstatus_id => $_cardstatus): ?>
                <option
                    value="<?php echo $_cardstatus->getId() ?>"
                    <?php echo ($record->cardstatus()->getId() == $_cardstatus->getId()) ? self::SELECTED : '' ?>><?php echo $_cardstatus->name ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="span1">

            <select
                id="card-user"
                class="autowidth"
                name="dialog[user][id]">
                <option value=""><?php echo __('select_a_option') ?></option>
                <?php foreach ($attorneys as $_attorney_id => $_attorney): ?>
                <option
                    value="<?php echo $_attorney->getId() ?>"
                    <?php echo ($record->user()->getId() == $_attorney->getId()) ? self::SELECTED : '' ?>><?php echo $_attorney->name ?></option>
                <?php endforeach ?>
            </select>
        </div>


        <div class="span2">
            <input
                id="card-original-id"
                type="hidden"
                name="dialog[original_id]"
                value="<?php echo $record->original_id ?>" />
            <input
                id="card-original-name"
                type="text"
                name="dialog[originalname]"
                class="autocomplete autowidth"
                data-source="<?php echo $this->url('/search/autocomplete/card/name?callback=?') ?>"
                data-spread='<?php echo json_encode(array('card-original-id' => 'id', 'card-original-name' => 'cardname')) ?>'
                value="<?php echo htmlspecialchars($record->original()->name) ?>"
                placeholder="<?php echo __('card_placeholder_original') ?>" />
            <?php echo $this->beanlink($record->original(), 'name') ?>
        </div>

    </div>
</fieldset>
<fieldset
    id="card-family"
    style="display:none;">
    <div class="row">
        <legend class="verbose"><?php echo __('card_legend_family') ?></legend>
        <?php if (! $record->getId()): ?>
            <p class="info"><?php echo __('card_hint_save_first') ?></p>
        <?php else: ?>
            <?php echo $this->partial('model/card/form/family/details') ?>
        <?php endif ?>
    </div>
</fieldset>

<!-- new client, applicant, foreign, ... area -->
<fieldset class="smaller-text">
    <legend></legend>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <label for="client-nickname"><?php echo __('card_label_client') ?></label>
        </div>
        <div class="span2">
            <!-- Select menu to toggle the form subpart -->
            <select name="void" class="autowidth" onchange="$('#card-applicant-block').toggle(); if ($(this).val() == '0') $('#card-applicant').val('0'); return false;">
                <option value="0" <?php echo (! $record->applicant_id) ? self::SELECTED : '' ?>><?php echo __('card_client_applicant_match') ?></option>
                <option value="1" <?php echo ($record->applicant_id) ? self::SELECTED : '' ?>><?php echo __('card_client_applicant_differ') ?></option>
            </select>
            <!-- End of Select menu for toggeling -->
        </div>
        <div class="span2">
            <!-- Select menu to toggle the form subpart -->
            <select name="void" class="autowidth" onchange="$('#card-foreign-block').toggle(); if ($(this).val() == '0') $('#card-foreign').val('0'); return false;">
                <option value="0" <?php echo (! $record->foreign_id) ? self::SELECTED : '' ?>><?php echo __('card_client_foreign_match') ?></option>
                <option value="1" <?php echo ($record->foreign_id) ? self::SELECTED : '' ?>><?php echo __('card_client_foreign_differ') ?></option>
            </select>
            <!-- End of Select menu for toggeling -->
        </div>
        <div class="span2">
            <select name="void" class="autowidth" onchange="$('#card-invreceiver-block').toggle(); if ($(this).val() == '0') $('#card-invreceiver').val('0'); return false;">
                <option value="0" <?php echo (! $record->invreceiver_id) ? self::SELECTED : '' ?>><?php echo __('card_client_invreceiver_match') ?></option>
                <option value="1" <?php echo ($record->invreceiver_id) ? self::SELECTED : '' ?>><?php echo __('card_client_invreceiver_differ') ?></option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">

            <div class="row">

                <fieldset
                    id="card-client-fieldset"
                    class="tab">
                    <input
                        id="card-client"
                        type="hidden"
                        name="dialog[client_id]"
                        value="<?php echo $record->client_id ?>" />
                    <input
                        id="client-nickname"
                        type="text"
                        name="dialog[clientnickname]"
                        class="autocomplete autowidth"
                        data-source="<?php echo $this->url('/search/autocomplete/person/nickname?callback=?') ?>"
                        data-spread='<?php echo json_encode(array('card-client' => 'person_id', 'client-nickname' => 'nickname', 'client-address' => 'address')) ?>'
                        value="<?php echo htmlspecialchars($record->clientnickname) ?>"
                        placeholder="<?php echo __('card_placeholder_clientnickname') ?>" />
                    <?php echo $this->beanlink($record->client(), 'name') ?>
                    <br /><br />
                </div>
                <div class="row">
                    <textarea
                        id="client-address"
                        class="autowidth"
                        name="dialog[clientaddress]"
                        rows="6"
                        placeholder="<?php echo __('card_placeholder_clientaddress') ?>"><?php echo htmlspecialchars($record->clientaddress) ?></textarea>
                </div>

                <div class="row">
                    <input
                        id="card-clientcode"
                        class="autowidth"
                        type="text"
                        name="dialog[clientcode]"
                        value="<?php echo htmlspecialchars($record->clientcode) ?>"
                        placeholder="<?php echo __('card_placeholder_clientcode') ?>" />
                </div>
            <fieldset>

        </div>
        <div class="span2">

            <fieldset
                id="card-applicant-block"
                style="display: <?php echo ($record->applicant_id) ? 'block' : 'none' ?>;">
                <legend class="verbose"><?php echo __('card_applicant_legend') ?></legend>
                <div class="row">

                    <input
                        id="card-applicant"
                        type="hidden"
                        name="dialog[applicant_id]"
                        value="<?php echo $record->applicant_id ?>" />
                    <input
                        id="applicant-nickname"
                        type="text"
                        name="dialog[applicantnickname]"
                        class="autocomplete autowidth"
                        data-source="<?php echo $this->url('/search/autocomplete/person/nickname?callback=?') ?>"
                        data-spread='<?php echo json_encode(array('card-applicant' => 'person_id', 'applicant-nickname' => 'nickname', 'applicant-address' => 'address')) ?>'
                        value="<?php echo htmlspecialchars($record->applicantnickname) ?>"
                        placeholder="<?php echo __('card_placeholder_applicantnickname') ?>" />
                    <?php echo $this->beanlink($record->applicant(), 'name') ?>
                </div>
                <div class="row">
                    <textarea
                        id="applicant-address"
                        class="autowidth"
                        name="dialog[applicantaddress]"
                        rows="6"
                        placeholder="<?php echo __('card_placeholder_applicantaddress') ?>"><?php echo htmlspecialchars($record->applicantaddress) ?></textarea>
                </div>

                <div class="row">
                    <input
                        id="card-applicantcode"
                        class="autowidth"
                        type="text"
                        name="dialog[applicantcode]"
                        value="<?php echo htmlspecialchars($record->applicantcode) ?>"
                        placeholder="<?php echo __('card_placeholder_applicantcode') ?>" />
                </div>
            </fieldset>&nbsp;

        </div>
        <div class="span2">

            <fieldset
                id="card-foreign-block"
                style="display: <?php echo ($record->foreign_id) ? 'block' : 'none' ?>;">
                <legend class="verbose"><?php echo __('card_foreign_legend') ?></legend>
                <div class="row">
                    <input
                        id="card-foreign"
                        type="hidden"
                        name="dialog[foreign_id]"
                        value="<?php echo $record->foreign_id ?>" />
                    <input
                        id="foreign-nickname"
                        type="text"
                        name="dialog[foreignnickname]"
                        class="autocomplete autowidth"
                        data-source="<?php echo $this->url('/search/autocomplete/person/nickname?callback=?') ?>"
                        data-spread='<?php echo json_encode(array('card-foreign' => 'person_id', 'foreign-nickname' => 'nickname', 'foreign-address' => 'address')) ?>'
                        value="<?php echo htmlspecialchars($record->foreignnickname) ?>"
                        placeholder="<?php echo __('card_placeholder_foreignnickname') ?>" />
                    <?php echo $this->beanlink($record->foreign(), 'name') ?>
                </div>
                <div class="row">
                    <textarea
                        id="foreign-address"
                        class="autowidth"
                        name="dialog[foreignaddress]"
                        rows="6"
                        placeholder="<?php echo __('card_placeholder_foreignaddress') ?>"><?php echo htmlspecialchars($record->foreignaddress) ?></textarea>
                </div>

                <div class="row">
                    <input
                        id="card-foreigncode"
                        class="autowidth"
                        type="text"
                        name="dialog[foreigncode]"
                        value="<?php echo htmlspecialchars($record->foreigncode) ?>"
                        placeholder="<?php echo __('card_placeholder_foreigncode') ?>" />
                </div>
            </fieldset>&nbsp;

        </div>
        <div class="span2">

            <fieldset
                id="card-invreceiver-block"
                style="display: <?php echo ($record->invreceiver_id) ? 'block' : 'none' ?>;">
                <legend class="verbose"><?php echo __('card_invreceiver_legend') ?></legend>
                <div class="row">

                    <input
                        id="card-invreceiver"
                        type="hidden"
                        name="dialog[invreceiver_id]"
                        value="<?php echo $record->invreceiver_id ?>" />
                    <input
                        id="invreceiver-nickname"
                        type="text"
                        name="dialog[invreceivernickname]"
                        class="autocomplete autowidth"
                        data-source="<?php echo $this->url('/search/autocomplete/person/nickname?callback=?') ?>"
                        data-spread='<?php echo json_encode(array('card-invreceiver' => 'person_id', 'invreceiver-nickname' => 'nickname', 'invreceiver-address' => 'address')) ?>'
                        value="<?php echo htmlspecialchars($record->invreceivernickname) ?>"
                        placeholder="<?php echo __('card_placeholder_invreceivernickname') ?>" />
                    <?php echo $this->beanlink($record->invreceiver(), 'name') ?>
                </div>
                <div class="row">
                    <textarea
                        id="invreceiver-address"
                        class="autowidth"
                        name="dialog[invreceiveraddress]"
                        rows="6"
                        placeholder="<?php echo __('card_placeholder_invreceiveraddress') ?>"><?php echo htmlspecialchars($record->invreceiveraddress) ?></textarea>
                </div>

                <div class="row">
                    <input
                        id="card-invreceivercode"
                        class="autowidth"
                        type="text"
                        name="dialog[invreceivercode]"
                        value="<?php echo htmlspecialchars($record->invreceivercode) ?>"
                        placeholder="<?php echo __('card_placeholder_invreceivercode') ?>" />
                </div>
            </fieldset>&nbsp;

        </div>
    </div>
</fieldset>
<!-- /new client ... area -->


<fieldset
    id="card-claim"
    class="tab">
    <legend class="verbose"><?php echo __('card_legend_claim') ?></legend>

    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span4"><?php echo __('card_claim_date') ?></div>
        <div class="span5"><?php echo __('card_claim_number') ?></div>
    </div>

    <?php foreach ($claimtypes as $_claimtype): ?>
    <div class="row">
        <div class="span3 right"><?php echo __('card_claim_type_'.$_claimtype) ?></div>
        <div class="span4">
            <input
                type="text"
                name="dialog[<?php echo $_claimtype ?>date]"
                value="<?php echo $this->date($record->{$_claimtype.'date'}) ?>" />
        </div>
        <div class="span5">
            <input
                type="text"
                name="dialog[<?php echo $_claimtype ?>number]"
                value="<?php echo htmlspecialchars($record->{$_claimtype.'number'}) ?>" />
        </div>
    </div>
    <?php endforeach ?>
</fieldset>
<div id="card-tabs" class="bar tabbed">
    <?php echo $this->tabbed('card-tabs', array(
        'card-detail' => __('card_tab_detail'),
        'card-pattern' => __('card_tab_pattern'),
        'card-priority' => __('card_tab_priority'),
        'card-fee' => __('card_tab_fee'),
        'card-invoice' => __('card_tab_invoice')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="card-detail"
        class="tab">
        <legend class="verbose"><?php echo __('card_legend_detail') ?></legend>
        <div class="row">
            <label
                for="card-title"
                class="<?php echo ($record->hasError('title')) ? 'error' : ''; ?>">
                <?php echo __('card_label_title') ?>
            </label>
            <textarea
                id="card-title"
                class="scaleable"
                name="dialog[title]"
                rows="5"><?php echo htmlspecialchars($record->title) ?></textarea>
        </div>
        <div class="row">
            <label
                for="card-codeword"
                class="<?php echo ($record->hasError('codeword')) ? 'error' : ''; ?>">
                <?php echo __('card_label_codeword') ?>
            </label>
            <textarea
                id="card-codeword"
                class="scaleable"
                name="dialog[codeword]"
                rows="5"><?php echo htmlspecialchars($record->codeword) ?></textarea>
        </div>
        <div class="row">
            <label
                for="card-note"
                class="<?php echo ($record->hasError('note')) ? 'error' : ''; ?>">
                <?php echo __('card_label_note') ?>
            </label>
            <textarea
                id="card-note"
                class="scaleable"
                name="dialog[note]"
                rows="5"><?php echo htmlspecialchars($record->note) ?></textarea>
        </div>
        <?php if ($_attrset = $record->cardtype->withCondition("enabled = 1")->ownAttrset):
            $_attrset_values = $record->box()->attrset;
        ?>
        <?php foreach ($_attrset as $_attrset_id => $_attr): ?>
        <div class="row">
            <label
                for="card-attr-<?php echo $_attrset_id ?>"
                class="attr">
                <?php echo $_attr->label ?>
            </label>
            <input
                type="text"
                name="attrset[<?php echo $_attrset_id ?>]"
                value="<?php echo isset($_attrset_values[$_attrset_id]) ? htmlspecialchars($_attrset_values[$_attrset_id]) : '' ?>" />
            <?php if ($_attr->desc): ?>
                <p class="info"><?php echo htmlspecialchars($_attr->desc) ?></p>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </fieldset>
    <fieldset
        id="card-pattern"
        class="tab">
        <legend class="verbose"><?php echo __('card_legend_pattern') ?></legend>
        <div class="row">
            <label
                for="card-pattern-count"
                class="<?php echo ($record->hasError('patterncount')) ? 'error' : ''; ?>">
                <?php echo __('card_label_patterncount') ?>
            </label>
            <input
                id="card-pattern-count"
                type="number"
                name="dialog[patterncount]"
                value="<?php echo htmlspecialchars($record->patterncount) ?>" />
        </div>
        <div class="row">
            <label
                for="card-pattern-content"
                class="<?php echo ($record->hasError('pattern')) ? 'error' : ''; ?>">
                <?php echo __('card_label_pattern') ?>
            </label>
            <textarea
                id="card-pattern-content"
                class="scaleable"
                name="dialog[pattern]"
                rows="5"><?php echo htmlspecialchars($record->pattern) ?></textarea>
        </div>
    </fieldset>
    <fieldset
        id="card-fee"
        class="tab">
        <legend class="verbose"><?php echo __('card_legend_fee') ?></legend>

        <?php if (! $record->getId()): ?>
            <p class="info"><?php echo __('card_hint_save_first') ?></p>
        <?php else: ?>
            <?php echo $this->partial('model/card/form/fee/details') ?>
        <?php endif ?>

    </fieldset>
    <fieldset
        id="card-priority"
        class="tab">
        <legend class="verbose"><?php echo __('card_legend_priority') ?></legend>
        <div class="row">
    	    <div class="span3"><?php echo __('priority_label_country') ?></div>
        	<div class="span3"><?php echo __('priority_label_date') ?></div>
        	<div class="span6"><?php echo __('priority_label_number') ?></div>
    	</div>

        <div id="priority-container" class="container attachable detachable priority">
        <?php foreach ($record->own('priority', true) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'priority'), array('n' => $_n, 'priority' => $_record)) ?>
        <?php endforeach ?>
    	    <a
    			href="<?php echo $this->url(sprintf('/%s/attach/own/%s', $record->getMeta('type'), 'priority')) ?>"
    			class="attach"
    			data-target="priority-container">
    				<span><?php echo __('scaffold_attach') ?></span>
    		</a>
		</div>
    </fieldset>
    <fieldset
        id="card-invoice"
        class="tab">
        <legend class="verbose"><?php echo __('card_legend_invoice') ?></legend>
        <ul class="util-menu">
        <?php
        foreach (R::find('invoicetype') as $_invtype_id => $_invtype):
        ?>
        <li><a href="<?php echo $this->url(sprintf('/invoice/with/%d/%d', $_invtype->getId(), $record->getId())) ?>"><?php echo $_invtype->name . ' ' . __('invtype_add_with') ?></a></li>
        <?php
        endforeach
        ?>
        </ul>
        <?php $_attributes = R::dispense('invoice')->attributes() ?>
        <table>
            <thead>
                <tr>
                    <th class="scaffold-action">&nbsp;</th>
                    <?php foreach ($_attributes as $_i => $_attribute): ?>
                        <?php
                            $_class = 'invoice fn-'.$_attribute['attribute'].' order';
                            if (isset($_attribute['class'])) {
                                $_class .= ' '.$_attribute['class'];
                            }
                        ?>
                    <th class="<?php echo $_class ?>">
                        <a href="#"><?php echo __('invoice_label_'.$_attribute['attribute']) ?></a>
                    </th>
                    <?php endforeach ?>
                </tr>
            </thead>
            <tbody>
                <?php $_row = 0 ?>
                <?php foreach ($record->with(' ORDER BY invoicedate DESC')->ownInvoice as $_n => $_record): ?>
                    <?php $_row++ ?>
                <tr
                    id="<?php echo $_record->getMeta('type') ?>-<?php echo $_record->getId() ?>"
                    class="<?php echo $_record->getMeta('type') ?> table item <?php echo ($_record->invalid()) ? 'error' : '' ?> <?php echo ($_record->deleted()) ? 'deleted' : '' ?>">
                    <td class="action">
                        <a href="<?php echo $this->url(sprintf('/%s/edit/%d', $_record->getMeta('type'), $_record->getId())) ?>" title="<?php echo __('scaffold_action_title_edit') ?>" class="edit ir"><?php echo __('action_edit') ?></a>
                    <?php echo $this->beanrow($_record, $_attributes, $surpressHtmlspecialchars = true) ?>
                </tr>
                <?php endforeach ?>
            </tbody>
        </table>
    </fieldset>
</div>
