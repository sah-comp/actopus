<?php
/**
 * Media fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('media_legend_file') ?></legend>
    <div class="row">
        <label
            for="media-file"
            class="<?php echo ($record->hasError('file')) ? 'error' : ''; ?>">
            <?php echo __('media_label_file') ?>
        </label>
        <div class="upload">
            
            <input
                type="text"
                class="uploaded <?php echo htmlspecialchars($record->extension) ?>"
                name="void"
                value="<?php echo htmlspecialchars($record->file) ?>"
                readonly="readonly" />
            <input type="hidden" name="MAX_FILE_SIZE" value="<?php echo APP_MAX_FILE_SIZE ?>" />
            <input
                id="media-file"
                type="file"
                name="file"
                value="<?php echo htmlspecialchars($record->file) ?>" />
        </div>
    </div>
</fieldset>
<?php foreach ($languages as $_language_id => $_language):
    $recordi18n = $record->i18n($_language->iso);
?>
<fieldset
    class="i18n <?php echo $_language->iso ?>"
    style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
    <legend class="verbose"><?php echo __('media_legend') ?></legend>
    <div>
        <input
            type="hidden"
            name="dialog[ownMediai18n][<?php echo $_language->getId() ?>][type]"
            value="<?php echo $recordi18n->getMeta('type') ?>" />
        <input
            type="hidden"
            name="dialog[ownMediai18n][<?php echo $_language->getId() ?>][id]"
            value="<?php echo $recordi18n->getId() ?>" />
        <input
            type="hidden"
            name="dialog[ownMediai18n][<?php echo $_language->getId() ?>][iso]"
            value="<?php echo htmlspecialchars($_language->iso) ?>" />
    </div>
    <div class="row">
        <label
            for="media-name"
            class="<?php echo ($recordi18n->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('media_label_name') ?>
        </label>
        <input
            id="media-name"
            type="text"
            name="dialog[ownMediai18n][<?php echo $_language->getId() ?>][name]"
            placeholder="<?php echo __('media_placeholder_name', $_language->iso) ?>"
            value="<?php echo htmlspecialchars($recordi18n->name) ?>" />
    </div>
    <div class="row">
        <label
            for="media-desc"
            class="<?php echo ($recordi18n->hasError('desc')) ? 'error' : ''; ?>">
            <?php echo __('media_label_desc') ?>
        </label>
        <textarea
            id="media-desc"
            name="dialog[ownMediai18n][<?php echo $_language->getId() ?>][desc]"
            rows="3"
            placeholder="<?php echo __('media_placeholder_desc', $_language->iso) ?>"><?php echo htmlspecialchars($recordi18n->desc) ?></textarea>
    </div>
</fieldset>
<?php
endforeach;
?>
<div id="media-tabs" class="bar tabbed">
    <?php echo $this->tabbed('media-tabs', array(
        'media-preview' => __('media_tab_preview')
    )) ?>
</div>
<div class="tab-container">
    <div
        class="tab">
        <?php if ($record->hasFile() && $record->isImage()): ?>
        <a
            class="preview"
            href="<?php echo sprintf('%s/../../%s/%s', $this->basehref(), 'uploads', $record->file) ?>">
            <img
                src="<?php echo $this->url(sprintf('/media/image/%d/480/auto', $record->getId())) ?>"
                style="width: 480px; height: auto;"
                alt="<?php echo htmlspecialchars($record->desc) ?>" />
        </a>
        <?php elseif ($record->hasFile()): ?>
        <a
            class="preview"
            href="<?php echo sprintf('%s/../../%s/%s', $this->basehref(), 'uploads', $record->file) ?>">
            <?php echo htmlspecialchars($record->name) ?>
        </a>
        <?php else: ?>
            <p><?php echo __('media_hint_no_file_uploaded_yet') ?></p>
        <?php endif ?>
    </div>
</div>
