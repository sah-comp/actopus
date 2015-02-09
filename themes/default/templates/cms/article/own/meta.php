<?php
/**
 * Meta of Article of Page fieldset for cms partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div
    class="i18n <?php echo $this->user()->language() ?>"
    style="display: block;">
    <legend class="verbose"><?php echo __('region_legend_meta') ?></legend>

    <div
        id="region-meta-<?php echo $this->user()->language() ?>"
        class="tab">
        
        <form
            id="meta-editor"
            class="metaplace"
            method="post"
            action="<?php echo $this->url('/cms/meta') ?>"
            data-container="article-slice-container"
            accept-charset="utf-8"
            enctype="multipart/form-data">
            <div>
                <input
                    type="hidden"
                    name="page_id"
                    value="<?php echo $record->getId() ?>" />
                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $carticle->getId() ?>" />
                <input
                    type="hidden"
                    name="articlei18n_id"
                    value="<?php echo $carticle->i18n()->getId() ?>" />
            </div>
            
            <fieldset>
                <legend class="verbose"><?php echo __('cms_legend_meta') ?></legend>
                <div class="row">
                    <input
                        id="aka-id"
                        type="hidden"
                        name="aka_id"
                        value="<?php echo $carticle->aka_id ?>" />
                    <label for="meta-name"><?php echo __('article_label_name') ?></label>
                    <input
                        id="meta-name"
                        type="text"
                        name="dialog[name]"
                        class="autocomplete"
                        data-source="<?php echo $this->url('/search/autocomplete/article/name?callback=?') ?>"
                        data-spread='<?php echo json_encode(array('aka-id' => 'id', 'meta-name' => 'label', 'meta-article-thumbnail' => 'thumbnail', 'meta-article-keywords' => 'keywords', 'meta-article-description' => 'description', 'article-invisible' => 'invisible', 'meta-article-template' => 'template_id')) ?>'
                        value="<?php echo htmlspecialchars($carticle->i18n()->name) ?>"
                        placeholder="<?php echo __('article_placeholder_name') ?>"
                        required="required" />
                        
                        <?php if ($carticle->fetchAs('article')->aka): ?>
                        <a
                            href="#noaka"
                            class="droprelated"
                            title="<?php echo __('cms_action_noaka') ?>"
                            onclick="$('#aka-id').val('0'); $(this).hide(); return false;"><?php echo __('cms_action_noaka') ?></a>
                        <?php endif ?>
                        
                </div>
                <div class="row">
                    <label for="meta-article-thumbnail"><?php echo __('article_label_thumbnail') ?></label>
                    <select
                        id="meta-article-thumbnail"
                        name="dialog[thumbnail]">
                        <option value=""><?php echo __('article_thumbnail_none') ?></option>
                        <?php foreach ($medias as $_media_id => $_media): ?>
                        <option
                            value="<?php echo $_media->file ?>"
                            <?php echo ($carticle->i18n()->thumbnail == $_media->file) ? self::SELECTED : '' ?>><?php echo $_media->mediaName() ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="row">
                    <label for="meta-article-keywords"><?php echo __('article_label_keywords') ?></label>
                    <textarea
                        id="meta-article-keywords"
                        class="meta-keywords"
                        name="dialog[keywords]"
                        rows="2"
                        cols="60"><?php echo htmlspecialchars($carticle->i18n()->keywords) ?></textarea>
                </div>
                <div class="row">
                    <label for="meta-article-description"><?php echo __('article_label_description') ?></label>
                    <textarea
                        id="meta-article-description"
                        class="meta-description"
                        name="dialog[description]"
                        rows="5"
                        cols="60"><?php echo htmlspecialchars($carticle->i18n()->description) ?></textarea>
                </div>
                <div class="row">
                    <input
                        type="hidden"
                        name="invisible"
                        value="0" />
                    <input
                        id="article-invisible"
                        type="checkbox"
                        name="invisible"
                        <?php echo ($carticle->invisible) ? self::CHECKED : '' ?>
                        value="1" />
                    <label
                        for="article-invisible"
                        class="cb <?php echo ($carticle->hasError('invisible')) ? 'error' : ''; ?>">
                        &nbsp;<?php echo __('article_label_invisible') ?>
                    </label>
                </div>
                <div class="row">
                    <label for="meta-article-template"><?php echo __('article_label_template') ?></label>
                    <select
                        id="meta-article-template"
                        name="dialog[template_id]">
                        <option value="0"><?php echo __('template_none') ?></option>
                        <?php foreach ($templates as $_id => $_template): ?>
                        <option
                            value="<?php echo $_template->getId() ?>"
                            <?php echo ($carticle->i18n()->template_id == $_template->getId()) ? self::SELECTED : '' ?>><?php echo htmlspecialchars($_template->name) ?></option>   
                        <?php endforeach ?>
                    </select>
                </div>
            </fieldset>
            <div class="toolbar">
                <input
                    id="article-delete"
                    type="hidden"
                    name="delete"
                    value="0" />
                <input
                    type="submit"
                    name="submit"
                    value="<?php echo __('cms_meta_submit') ?>" />
                <input
                    type="submit"
                    class="delete"
                    name="submit"
                    onclick="$('#article-delete').val('1');"
                    value="<?php echo __('cms_meta_delete') ?>" />
            </div>
        </form>

    </div>

</div>
<!-- re-init clairvoyants -->
<script>
    if (typeof clairvoyants == 'function') {
        clairvoyants();
    }
</script>
<!-- /re-init clairvoyants -->
