<?php
/**
 * Token fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('token_legend') ?></legend>
    <div class="row">
        <label
            for="token-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('token_label_name') ?>
        </label>
        <input
            id="token-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="token-desc"
            class="<?php echo ($record->hasError('desc')) ? 'error' : ''; ?>">
            <?php echo __('token_label_desc') ?>
        </label>
        <textarea
            id="token-desc"
            name="dialog[desc]"
            rows="3"
            placeholder="<?php echo __('token_placeholder_desc') ?>"><?php echo htmlspecialchars($record->desc) ?></textarea>
    </div>
</fieldset>
<div id="token-tabs" class="bar tabbed">
    <?php echo $this->tabbed('token-tabs', array(
        'token-translation' => __('token_tab_translation')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="token-translation"
        class="tab">
        <legend class="verbose"><?php echo __('translation_legend') ?></legend>
        <?php foreach ($languages as $_language_id => $_language): ?>
            <?php $_translation = $record->in($_language->iso) ?>
            <div class="row">
                <input
                    type="hidden"
                    name="dialog[ownTranslation][<?php echo $_language_id ?>][type]"
                    value="translation" />
                <input
                    type="hidden"
                    name="dialog[ownTranslation][<?php echo $_language_id ?>][id]"
                    value="<?php echo $_translation->getId() ?>" />
                <input
                    type="hidden"
                    name="dialog[ownTranslation][<?php echo $_language_id ?>][iso]"
                    value="<?php echo $_translation->iso ?>" />
                <label
                    for="translation-<?php echo $_translation->getId() ?>"
                    class="<?php echo ($_translation->hasError('payload')) ? 'error' : ''; ?>">
                    <?php echo __('language_'.$_translation->iso) ?>
                </label>
                <textarea
                    id="translation-<?php echo $_translation->getId() ?>"
                    class="scaleable"
                    name="dialog[ownTranslation][<?php echo $_language_id ?>][payload]"
                    cols="60"
                    rows="2"><?php echo htmlspecialchars($_translation->payload) ?></textarea>
                <?php if ($record->mode): ?>
                    <p class="info"><?php echo __('token_mode_'.$record->mode) ?></p>
                <?php endif ?>
    
            </div>
        <?php endforeach ?>
    </fieldset>
</div>
