<?php
/**
 * Setting index page template.
 *
 * This templates generates a form to set general setting beans.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div id="main" class="main">

    <form
    	id="setting-index"
    	class="panel"
    	action="<?php echo $this->url('/setting/index'); ?>"
    	method="post"
    	accept-charset="utf-8"
        enctype="multipart/form-data">
        <div>
            <input type="hidden" name="dialog[type]" value="setting" />
            <input type="hidden" name="dialog[id]" value="<?php echo $record->getId() ?>" />
        </div>
        <fieldset>
    		<legend class="verbose"><?php echo __('setting_legend_blessedfolter'); ?></legend>
            <div class="row">
                <label
                    for="setting-blessedfolder"
                    class="<?php echo ($record->hasError('blessedfolder')) ? 'error' : ''; ?>">
                    <?php echo __('setting_label_blessedfolder') ?>
                </label>
                <input type="hidden" name="dialog[blessedfolder][type]" value="domain" />
                <input type="hidden" name="dialog[blessedfolder][id]" value="0" />
                <select
                    id="setting-blessedfolder"
                    name="dialog[blessedfolder][id]">
                    <?php foreach ($domains as $_id => $_domain): ?>
                    <option
                        value="<?php echo $_domain->getId() ?>"
                        <?php echo ($record->blessedfolder()->getId() == $_domain->getId()) ? self::SELECTED : '' ?>><?php echo __('domain_'.$_domain->name) ?></option>   
                    <?php endforeach ?>
                </select>
                <p class="info"><?php echo __('setting_hint_blessedfolder') ?></p>
            </div>
            <div class="row">
                <label
                    for="setting-feebase"
                    class="<?php echo ($record->hasError('feebase')) ? 'error' : ''; ?>">
                    <?php echo __('setting_label_feebase') ?>
                </label>
                <input type="hidden" name="dialog[feebase][type]" value="pricetype" />
                <input type="hidden" name="dialog[feebase][id]" value="0" />
                <select
                    id="setting-feebase"
                    name="dialog[feebase][id]">
                    <?php foreach (R::findAll('pricetype') as $_id => $_pricetype): ?>
                    <option
                        value="<?php echo $_pricetype->getId() ?>"
                        <?php echo ($record->feebase()->getId() == $_pricetype->getId()) ? self::SELECTED : '' ?>><?php echo htmlspecialchars($_pricetype->name) ?></option>   
                    <?php endforeach ?>
                </select>
                <p class="info"><?php echo __('setting_hint_feebase') ?></p>
            </div>           
    	</fieldset>
    	
        <fieldset>
            <legend class="verbose"><?php echo __('setting_legend_currency_exchangerates') ?></legend>
            <div class="row">
                <label
                    for="setting-loadexchangerates"
                    class="<?php echo ($record->hasError('loadexchangerates')) ? 'error' : ''; ?>">
                    <?php echo __('setting_label_loadexchangerates') ?>
                </label>
                <select
                    id="setting-loadexchangerates"
                    name="dialog[loadexchangerates]">
                    <option value="0"><?php echo __('setting_loadexchangerates_no', array($this->timestamp($record->tsexchangerate, 'date'))) ?></value>
                    <option value="1"><?php echo __('setting_loadexchangerates_yes') ?></value>
                </select>
                <p class="info"><?php echo __('setting_hint_loadexchangerates') ?></p>
            </div>
        </fieldset>
        
        <fieldset>
            <legend class="verbose"><?php echo __('setting_legend_media') ?></legend>
            <div class="row">
                <input
                    type="hidden"
                    name="dialog[retina]"
                    value="0" />
                <input
                    id="setting-retina"
                    type="checkbox"
                    name="dialog[retina]"
                    <?php echo ($record->retina) ? self::CHECKED : '' ?>
                    value="1" />
                <label
                    for="setting-retina"
                    class="cb <?php echo ($record->hasError('retina')) ? 'error' : ''; ?>">
                    <?php echo __('setting_label_retina') ?>
                </label>
            </div>
        </fieldset>
        
        <fieldset>
            <legend class="verbose"><?php echo __('setting_legend_fiscal') ?></legend>
            <div class="row">
                <label
                    for="setting-fiscalyear"
                    class="<?php echo ($record->hasError('fy')) ? 'error' : ''; ?>">
                    <?php echo __('setting_label_fy') ?>
                </label>
                <input
                    id="setting-fiscalyear"
                    type="number"
                    name="dialog[fy]"
                    value="<?php echo htmlspecialchars($record->fy) ?>"
                    required="required" />
            </div>
        </fieldset>
        
        <?php if (count($invoicetypes)): ?>
        <fieldset>
            <legend class="verbose"><?php echo __('setting_legend_invoicetype') ?></legend>
            <?php foreach ($invoicetypes as $_invtype_id => $_invoicetype): ?>
            <div class="row">
                <div>
                    <input
                        type="hidden"
                        name="dialog[sharedInvoicetype][<?php echo $_invtype_id ?>][type]"
                        value="invoicetype" />
                    <input
                        type="hidden"
                        name="dialog[sharedInvoicetype][<?php echo $_invtype_id ?>][id]"
                        value="<?php echo $_invoicetype->getId() ?>" />
                    <input
                        type="hidden"
                        name="dialog[sharedInvoicetype][<?php echo $_invtype_id ?>][name]"
                        value="<?php echo htmlspecialchars($_invoicetype->name) ?>" />
                </div>
                <label
                    for="setting-invoicetype-<?php echo $_invtype_id ?>">
                    <?php echo htmlspecialchars($_invoicetype->name) ?>
                </label>
                <input
                    id="setting-invoicetype-<?php echo $_invtype_id ?>"
                    type="number"
                    name="dialog[sharedInvoicetype][<?php echo $_invtype_id ?>][serial]"
                    value="<?php echo htmlspecialchars($_invoicetype->serial) ?>"
                    required="required" />
            </div>
            <?php endforeach ?>
        </fieldset>
        <?php endif ?>
        
        <fieldset>
            <legend class="verbose"><?php echo __('setting_legend_housedata') ?></legend>
            <div class="row">
                <label for="house-name-1"><?php echo __('house_name_1') ?></label>
                <input
                    id="house-name-1"
                    type="text"
                    name="dialog[housename1]"
                    value="<?php echo htmlspecialchars($record->housename1) ?>" />
            </div>
            <div class="row">
                <label for="house-name-2"><?php echo __('house_name_2') ?></label>
                <input
                    id="house-name-2"
                    type="text"
                    name="dialog[housename2]"
                    value="<?php echo htmlspecialchars($record->housename2) ?>" />
            </div>
            <div class="row">
                <label for="house-address-1"><?php echo __('house_address_1') ?></label>
                <input
                    id="house-address-1"
                    type="text"
                    name="dialog[houseaddr1]"
                    value="<?php echo htmlspecialchars($record->houseaddr1) ?>" />
            </div>
            <div class="row">
                <label for="house-address-2"><?php echo __('house_address_2') ?></label>
                <input
                    id="house-address-2"
                    type="text"
                    name="dialog[houseaddr2]"
                    value="<?php echo htmlspecialchars($record->houseaddr2) ?>" />
            </div>
            <div class="row">
                <label for="house-address-3"><?php echo __('house_address_3') ?></label>
                <input
                    id="house-address-3"
                    type="text"
                    name="dialog[houseaddr3]"
                    value="<?php echo htmlspecialchars($record->houseaddr3) ?>" />
            </div>
            <div class="row">
                <label for="house-address-4"><?php echo __('house_address_4') ?></label>
                <input
                    id="house-address-4"
                    type="text"
                    name="dialog[houseaddr4]"
                    value="<?php echo htmlspecialchars($record->houseaddr4) ?>" />
            </div>
            <div class="row">
                <label for="house-epo-bank"><?php echo __('house_epo_bank') ?></label>
                <input
                    id="house-epo-bank"
                    type="text"
                    name="dialog[houseepobank]"
                    value="<?php echo htmlspecialchars($record->houseepobank) ?>" />
            </div>
            <div class="row">
                <label for="house-epo-account"><?php echo __('house_epo_account') ?></label>
                <input
                    id="house-epo-account"
                    type="text"
                    name="dialog[houseepoaccount]"
                    value="<?php echo htmlspecialchars($record->houseepoaccount) ?>" />
            </div>
        </fieldset>

    	<div class="toolbar">
    		<input
    			type="submit"
    			id="submit"
    			class="default"
    			name="submit"
    			value="<?php echo __('setting_submit'); ?>"
    			accesskey="s" />
    	</div>
    </form>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
