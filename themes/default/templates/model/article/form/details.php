<?php
/**
 * Article fieldset for editing partial.
 *
 * @todo Think of making a gestalt class to spit out this stuff more easily
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php foreach ($languages as $_language_id => $_language):
    $recordi18n = $record->i18n($_language->iso);
?>
<fieldset
    class="i18n <?php echo $_language->iso ?>"
    style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
    <legend class="verbose"><?php echo __('article_legend') ?></legend>
    <div>
        <input
            type="hidden"
            name="dialog[ownArticlei18n][<?php echo $_language->getId() ?>][type]"
            value="<?php echo $recordi18n->getMeta('type') ?>" />
        <input
            type="hidden"
            name="dialog[ownArticlei18n][<?php echo $_language->getId() ?>][id]"
            value="<?php echo $recordi18n->getId() ?>" />
        <input
            type="hidden"
            name="dialog[ownArticlei18n][<?php echo $_language->getId() ?>][iso]"
            value="<?php echo $_language->iso ?>" />
    </div>
    <div class="row">
        <label
            for="article-name-<?php echo $_language->iso ?>"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('article_label_name') ?>
        </label>
        <input
            id="article-name-<?php echo $_language->iso ?>"
            type="text"
            name="dialog[ownArticlei18n][<?php echo $_language->getId() ?>][name]"
            value="<?php echo htmlspecialchars($recordi18n->name) ?>"
            required="required" />
    </div>
</fieldset>
<?php
endforeach;
?>
<div id="article-tabs" class="bar tabbed">
    <?php echo $this->tabbed('article-tabs', array(
        'article-slice' => __('article_tab_slice'),
        'article-meta' => __('article_tab_meta')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="article-slice"
        class="tab">
        <legend class="verbose"><?php echo __('article_legend_slice') ?></legend>
        <?php foreach ($languages as $_language_id => $_language): ?>
            <?php $recordi18n = $record->i18n($_language->iso); ?>
            <?php $regions = $recordi18n->regions(); ?>
            <?php if (count($regions) > 1) {
                // we need tabs or a pull down to change regions
                $_regs = array();
                foreach ($regions as $_region_id => $_region) {
                    $_regs['region-'.$_region->getId().'-'.$_language->iso] = $_region->name;
                }
                ?>
                <div
                    id="region-tabs"
                    class="i18n <?php echo $_language->iso ?> bar tabbed"
                    style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
                    <?php echo $this->tabbed('region-tabs', $_regs) ?>
                </div>
                <?php
            }
            ?>
            <?php foreach ($regions as $_region_id => $_region): ?>
        <fieldset
            class="i18n <?php echo $_language->iso ?>"
            style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
            <legend class="verbose"><?php echo __('region_legend') ?></legend>
            <fieldset
                id="region-<?php echo $_region->getId() ?>-<?php echo $_language->iso ?>"
                class="tab">
                <legend class="verbose"><?php echo __('slice_legend') ?></legend>

                <div class="row">
            	    <div class="span3"><?php echo __('slice_label_mode') ?></div>
                	<div class="span9"><?php echo __('slice_label_content') ?></div>
            	</div>

                <div
                    id="slice-container-<?php echo $_region->getId() ?>-<?php echo $_language->iso ?>"
                    class="container attachable detachable sortable slice"
                    title="<?php echo __('tooltip_drag_drop_to_sort_items') ?>"
                    data-href="<?php echo $this->url(sprintf('/article/sortable/slice/sliceid')) ?>"
                    data-container="slice-container-<?php echo $_region->getId() ?>-<?php echo $_language->iso ?>"
                    data-variable="sliceid">
                <?php foreach ($record->sliceByRegionAndLanguage($_region->getId(), $_language->iso, false) as $_n => $_record): ?>
                    <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'slice'), array('n' => $_n, 'slice' => $_record, 'region_id' => $_region->getId(), 'iso' => $_language->iso)) ?>
                <?php endforeach ?>
            	    <a
            			href="<?php echo $this->url(sprintf('/%s/attachownslice/%d/%s', $record->getMeta('type'), $_region->getId(), $_language->iso)) ?>"
            			class="attach"
            			data-target="slice-container-<?php echo $_region->getId() ?>-<?php echo $_language->iso ?>">
            				<span><?php echo __('scaffold_attach') ?></span>
            		</a>
        		</div>

            </fieldset>
        </fieldset>
            <?php endforeach ?>
        <?php endforeach ?>
    </fieldset>
    <fieldset
        id="article-meta"
        class="tab">
        <legend class="verbose"><?php echo __('article_legend_meta') ?></legend>
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