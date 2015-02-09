<?php
/**
 * Optin fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('optin_legend') ?></legend>
    <div class="row">
        <label
            for="optin-email"
            class="<?php echo ($record->hasError('email')) ? 'error' : ''; ?>">
            <?php echo __('optin_label_email') ?>
        </label>
        <input
            id="optin-email"
            type="email"
            name="dialog[email]"
            value="<?php echo htmlspecialchars($record->email) ?>"
            required="required" />
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[enabled]"
            value="0" />
        <label
            for="optin-enabled"
            class="cb <?php echo ($record->hasError('enabled')) ? 'error' : ''; ?>">
            <?php echo __('optin_label_enabled') ?>
        </label>
        <input
            id="optin-enabled"
            type="checkbox"
            name="dialog[enabled]"
            <?php echo ($record->enabled) ? self::CHECKED : '' ?>
            value="1" />
    </div>
</fieldset>
<fieldset>
    <legend class="verbose"><?php echo __('optin_legend_person') ?></legend>
    <div class="row">
        <label
            for="optin-attention"
            class="<?php echo ($record->hasError('attention')) ? 'error' : ''; ?>">
            <?php echo __('optin_label_attention') ?>
        </label>
        <input
            id="optin-attention"
            type="text"
            name="dialog[attention]"
            value="<?php echo htmlspecialchars($record->attention) ?>" />
    </div>
    <div class="row">
        <label
            for="optin-firstname"
            class="<?php echo ($record->hasError('firstname')) ? 'error' : ''; ?>">
            <?php echo __('optin_label_firstname') ?>
        </label>
        <input
            id="optin-firstname"
            type="text"
            name="dialog[firstname]"
            value="<?php echo htmlspecialchars($record->firstname) ?>" />
    </div>
    <div class="row">
        <label
            for="optin-lastname"
            class="<?php echo ($record->hasError('lastname')) ? 'error' : ''; ?>">
            <?php echo __('optin_label_lastname') ?>
        </label>
        <input
            id="optin-lastname"
            type="text"
            name="dialog[lastname]"
            value="<?php echo htmlspecialchars($record->lastname) ?>" />
    </div>
    <div class="row">
        <label
            for="optin-organization"
            class="<?php echo ($record->hasError('organization')) ? 'error' : ''; ?>">
            <?php echo __('optin_label_organization') ?>
        </label>
        <input
            id="optin-organization"
            type="text"
            name="dialog[organization]"
            value="<?php echo htmlspecialchars($record->organization) ?>" />
    </div>
</fieldset>
<div id="optin-tabs" class="bar tabbed">
    <?php echo $this->tabbed('optin-tabs', array(
        'optin-campaign' => __('optin_tab_campaign')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="optin-campaign"
        class="tab">
        <legend class="verbose"><?php echo __('optin_legend_campaign') ?></legend>
        <?php foreach ($campaigns as $_id => $_campaign): ?>
        <div class="row">
            <input
                type="hidden"
                name="dialog[sharedCampaign][<?php echo $_campaign->getId() ?>][type]"
                value="campaign" />
            <input
                type="hidden"
                name="dialog[sharedCampaign][<?php echo $_campaign->getId() ?>][id]"
                value="0" />
            <label
                for="optin-campaign-<?php echo $_campaign->getId() ?>"
                class="cb"><?php echo __($_campaign->name) ?></label>
            <input
                type="checkbox"
                class="xtoggle"
                data-container="optin-survey-<?php echo $_campaign->getId() ?>"
                id="optin-campaign-<?php echo $_campaign->getId() ?>"
                name="dialog[sharedCampaign][<?php echo $_campaign->getId() ?>][id]"
                value="<?php echo $_campaign->getId() ?>"
                <?php echo (isset($record->sharedCampaign[$_campaign->getId()])) ? self::CHECKED : '' ?> />
        </div>
        <?php endforeach ?>
    </fieldset>
</div>
