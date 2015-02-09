<?php
/**
 * Newsletter test page template.
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
    <div id="content">
        <form
            id="dialog"
            class="panel test"
            action="<?php echo $this->url(sprintf('/newsletter/test/%d/%d/%d/%s/%d/%d', $record->getId(), $page, $limit, $layout, $order, $dir)) ?>"
            method="post"
            accept-charset="utf-8">
            <div>
                <input
                    type="hidden"
                    name="dialog[type]"
                    value="newsletter" />
                <input
                    type="hidden"
                    name="dialog[id]"
                    value="<?php echo $record->getId() ?>" />
            </div>
            <fieldset>
                <legend class="verbose"><?php echo __('newsletter_legend_test') ?></legend>
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
                        disabled="disabled" />
                </div>
                <div class="row">
                    <label
                        for="newsletter-optin"
                        class="<?php echo ($record->hasError('optin_id')) ? 'error' : ''; ?>">
                        <?php echo __('newsletter_label_testemail') ?>
                    </label>
                    <select
                        id="newsletter-optin"
                        name="dialog[optin_id]">
                        <?php foreach ($optins as $_id => $_optin): ?>
                        <option
                            value="<?php echo $_optin->getId() ?>"
                            <?php echo ($record->optin_id == $_optin->getId()) ? self::SELECTED : '' ?>><?php echo $_optin->email ?></option>   
                        <?php endforeach ?>
                    </select>
                </div>
            </fieldset>
            <div class="toolbar">
                <input type="submit" name="submit" value="<?php echo __('newsletter_submit_test') ?>" /> <?php echo __('or') ?> <a href="<?php echo $this->url(sprintf('/newsletter/edit/%d/%d/%d/%s/%d/%d', $record->getId(), $page, $limit, $layout, $order, $dir)) ?>"><?php echo __('go_back_to_newsletter') ?></a>
            </div>
        </form>
    </div>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
