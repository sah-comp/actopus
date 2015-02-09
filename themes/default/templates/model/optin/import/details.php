<?php
/**
 * Import optin fieldset partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php $campaigns = R::findAll('campaign', ' ORDER BY name') ?>
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
