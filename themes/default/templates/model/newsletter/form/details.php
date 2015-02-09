<?php
/**
 * Newsletter fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('newsletter_legend') ?></legend>
    
    <div class="row">
        <label
            for="newsletter-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('newsletter_label_name') ?>
        </label>
        <input
            id="newsletter-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    
    <div class="row">
        <input
            type="hidden"
            name="dialog[issue][type]"
            value="issue" />
        <input
            type="hidden"
            name="dialog[issue][id]"
            value="<?php echo $record->issue()->getId() ?>" />
        <label
            for="newsletter-issue-year"
            class="<?php echo ($record->issue()->hasError('y')) ? 'error' : ''; ?>">
            <?php echo __('newsletter_label_issue') ?>
        </label>
        <input
            id="newsletter-issue-year"
            type="number"
            min="1970"
            step="1"
            max="<?php echo (int)date('Y')+23 ?>"
            required="required"
            name="dialog[issue][y]"
            value="<?php echo htmlspecialchars($record->issue()->y) ?>" />
        <input
            id="newsletter-issue-month"
            type="number"
            min="1"
            step="1"
            max="12"
            required="required"
            name="dialog[issue][m]"
            value="<?php echo htmlspecialchars($record->issue()->m) ?>" />
    </div>
    
    <div class="row">
        <label
            for="newsletter-teaser"
            class="<?php echo ($record->hasError('teaser')) ? 'error' : ''; ?>">
            <?php echo __('newsletter_label_teaser') ?>
        </label>
        <textarea
            id="newsletter-teaser"
            class="scaleable"
            name="dialog[teaser]"
            rows="3"><?php echo htmlspecialchars($record->teaser) ?></textarea>
    </div>
    <div class="row">
        <label
            for="newsletter-content"
            class="<?php echo ($record->hasError('content')) ? 'error' : ''; ?>">
            <?php echo __('newsletter_label_content') ?>
        </label>
        <textarea
            id="newsletter-content"
            class="scaleable"
            name="dialog[content]"
            rows="6"><?php echo htmlspecialchars($record->content) ?></textarea>
    </div>
</fieldset>
<div id="user-tabs" class="bar tabbed">
    <?php echo $this->tabbed('user-tabs', array(
        'newsletter-article' => __('newsletter_tab_article'),
        'newsletter-optin' => __('newsletter_tab_optin'),
        'newsletter-smtp' => __('newsletter_tab_smtp'),
        'newsletter-meta' => __('newsletter_tab_meta')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="newsletter-article"
        class="tab sortable"
        title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
        data-href="<?php echo $this->url(sprintf('/newsletter/sortable/article/sequence')) ?>"
        data-container="newsletter-article">
        <legend class="verbose"><?php echo __('newsletter_legend_article') ?></legend>
        <?php foreach ($articles as $_id => $_article): ?>
        <div
            id="sequence_<?php echo $_article->getId() ?>"
            class="row">
            <input
                type="hidden"
                name="dialog[sharedArticle][<?php echo $_article->getId() ?>][type]"
                value="article" />
            <input
                type="hidden"
                name="dialog[sharedArticle][<?php echo $_article->getId() ?>][id]"
                value="0" />
            <label
                for="newsletter-article-<?php echo $_article->getId() ?>"
                class="cb">
                <a
                    href="<?php echo $this->url(sprintf('/article/edit/%d', $_article->getId())) ?>"
                    class="modal"><?php echo $_article->name ?></a>
            </label>
            <input
                type="checkbox"
                id="newsletter-article-<?php echo $_article->getId() ?>"
                name="dialog[sharedArticle][<?php echo $_article->getId() ?>][id]"
                value="<?php echo $_article->getId() ?>"
                <?php echo (isset($record->sharedArticle[$_article->getId()])) ? self::CHECKED : '' ?> />
        </div>
        <?php endforeach ?>
    </fieldset>
    <fieldset
        id="newsletter-optin"
        class="tab">
        <legend class="verbose"><?php echo __('newsletter_legend_optin') ?></legend>
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
                for="newsletter-campaign-<?php echo $_campaign->getId() ?>"
                class="cb">
                <a
                    href="<?php echo $this->url(sprintf('/campaign/edit/%d', $_campaign->getId())) ?>"
                    class="modal"><?php echo $_campaign->name ?>
                </a>
            </label>
            <input
                type="checkbox"
                id="newsletter-campaign-<?php echo $_campaign->getId() ?>"
                name="dialog[sharedCampaign][<?php echo $_campaign->getId() ?>][id]"
                value="<?php echo $_campaign->getId() ?>"
                <?php echo (isset($record->sharedCampaign[$_campaign->getId()])) ? self::CHECKED : '' ?> />
        </div>
        <?php endforeach ?>
    </fieldset>
    
    <?php if (empty($smtps)): ?>
        
    <fieldset
        id="newsletter-smtp"
        class="tab">
        <legend class="verbose"><?php echo __('newsletter_legend_smtp') ?></legend>
        <div class="row">
            <input type="hidden" name="dialog[service]" value="local" />
            <p><?php echo __('newsletter_service_no_smtp_available_use_local') ?></p>
        </div>
    </fieldset>
        
    <?php else: ?>
    
    <fieldset
        id="newsletter-smtp"
        class="tab">
        <legend class="verbose"><?php echo __('newsletter_legend_smtp') ?></legend>
        <div class="row">
            <input
                id="newsletter-service-smtp"
                type="radio"
                name="dialog[service]"
                value="smtp"
                <?php echo ($record->service == 'smtp') ? self::CHECKED : '' ?> />
            <select
                name="dialog[smtp_id]"
                class="cb"
                onfocus="$('#newsletter-service-smtp').attr('checked', 'checked')">
                <?php foreach ($smtps as $_id => $_smtp): ?>
                <option
                    value="<?php echo $_smtp->getId() ?>"
                    <?php echo ($record->smtp_id == $_smtp->getId()) ? self::SELECTED : '' ?>><?php echo $_smtp->name ?></option>   
                <?php endforeach ?>
            </select>
        </div>
        <div class="row">
            <input
                id="newsletter-service-local"
                type="radio"
                name="dialog[service]"
                value="local"
                <?php echo ($record->service == 'local') ? self::CHECKED : '' ?> />
            <label
                for="newsletter-service-local"
                class="cb">
                <?php echo __('newsletter_service_local') ?>
            </label>
        </div>
        <div class="row">
            <label
                for="newsletter-listmanageremail"
                class="<?php echo ($record->hasError('listmanageremail')) ? 'error' : ''; ?>">
                <?php echo __('newsletter_label_listmanageremail') ?>
            </label>
            <input
                id="newsletter-listmanageremail"
                type="email"
                name="dialog[listmanageremail]"
                value="<?php echo htmlspecialchars($record->listmanageremail) ?>"
                required="required" />
        </div>
        <div class="row">
            <label
                for="newsletter-listmanagername"
                class="<?php echo ($record->hasError('listmanagername')) ? 'error' : ''; ?>">
                <?php echo __('newsletter_label_listmanagername') ?>
            </label>
            <input
                id="newsletter-listmanagername"
                type="text"
                name="dialog[listmanagername]"
                value="<?php echo htmlspecialchars($record->listmanagername) ?>"
                required="required" />
        </div>
    </fieldset>
    
    <?php endif; ?>
    
    <fieldset
        id="newsletter-meta"
        class="tab">
        <legend class="verbose"><?php echo __('newsletter_legend_meta') ?></legend>
        <div>
            <input
                type="hidden"
                name="dialog[meta][type]"
                value="meta" />
            <input
                type="hidden"
                name="dialog[meta][id]"
                value="<?php echo $record->meta()->getId() ?>" />
        </div>
        <div class="row">
            <label
                for="meta-name"
                class="<?php echo ($record->meta()->hasError('name')) ? 'error' : ''; ?>">
                <?php echo __('meta_label_name') ?>
            </label>
            <input
                id="meta-name"
                type="text"
                name="dialog[meta][name]"
                value="<?php echo htmlspecialchars($record->meta()->name) ?>" />
        </div>
        <div class="row">
            <label
                for="meta-package"
                class="<?php echo ($record->meta()->hasError('package')) ? 'error' : ''; ?>">
                <?php echo __('meta_label_package') ?>
            </label>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo APP_MAX_FILE_SIZE ?>" />
            <input
                id="meta-package"
                type="file"
                accept="application/zip"
                name="package" />            
        <?php if ($record->meta()->package): ?>
            <p class="info">
            <a href="<?php echo $this->durl($record->meta()->package) ?>"><?php echo $record->meta()->package ?></a>
            </p>
        <?php endif ?>
        </div>
    </fieldset>
</div>
