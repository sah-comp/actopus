<?php
/**
 * Newsletter opt-in page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>

<header class="master clearfix">
    <hgroup>
        <h1><?php echo __('app_name') ?></h1>
        <h2><?php echo __('app_slogan') ?></h2>
    </hgroup>
</header>
<?php echo $this->partial('shared/master/notification') ?>
<!-- End of the master header -->


<div id="main" class="main optin">
    <article class="copy">
        <?php echo $this->textile(__('newsletter_optin_copy', null, null, 'textile')) ?>
    </article>
    <div id="content">
        <form
            id="dialog"
            class="panel optin"
            action="<?php echo $this->url('/newsletter/optin/') ?>"
            method="post"
            accept-charset="utf-8">
            <div>
                <input
                    type="hidden"
                    name="dialog[type]"
                    value="optin" />            
                <input
                    type="hidden"
                    name="dialog[id]"
                    value="<?php echo $record->getId() ?>" />
                <input
                    type="hidden"
                    name="dialog[enabled]"
                    value="0" />
            </div>
            <fieldset>
                <legend class="verbose"><?php echo __('newsletter_legend_optin') ?></legend>
                <div class="row">
                    <label
                        for="name"
                        class="<?php echo ($record->hasError('email')) ? 'error' : '' ?>">
                        <?php echo __('newsletter_optin_label_email') ?>
                    </label>
                    <input
                        id="name"
                        type="email"
                        name="dialog[email]"
                        value="<?php echo htmlspecialchars($record->email) ?>"
                        placeholder="<?php echo __('optin_placeholder_email') ?>"
                        required="required"
                        autofocus="autofocus" />
                </div>
            </fieldset>

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
                        id="optin-campaign-<?php echo $_campaign->getId() ?>"
                        name="dialog[sharedCampaign][<?php echo $_campaign->getId() ?>][id]"
                        value="<?php echo $_campaign->getId() ?>"
                        <?php echo (isset($record->sharedCampaign[$_campaign->getId()])) ? self::CHECKED : '' ?> />
                </div>
                <?php endforeach ?>
            </fieldset>

            
            <div class="toolbar">
                <input type="submit" name="submit" value="<?php echo __('newsletter_submit_optin') ?>" />
            </div>
        </form>
    </div>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
