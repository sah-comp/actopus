<?php
/**
 * Backend of slice bean mode = image.
 *
 * @uses $slice which is a slice bean
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<form
    id="inplace-<?php echo $slice->getMeta('type') ?>-<?php echo $slice->getId() ?>"
    class="inplace <?php echo $slice->getMeta('type') ?>"
    method="post"
    action="<?php echo $this->url(sprintf('/cms/slice/%d', $slice->getId())) ?>"
    accept-charset="utf-8"
    data-container="slice-<?php echo $slice->getId() ?>"
    enctype="multipart/form-data">
    <fieldset>
        <legend class="verbose"><?php echo __('module_image_backend') ?></legend>
        <select
            name="dialog[content]">
            <?php foreach ($medias as $_media_id => $_media): ?>
            <option
                value="<?php echo $_media->file ?>"
                <?php echo ($slice->content == $_media->file) ? self::SELECTED : '' ?>><?php echo $_media->mediaName() ?></option>
            <?php endforeach ?>
        </select>
    </fieldset>

    <div class="row">
        <input
            type="submit"
            name="submit"
            value="<?php echo __('action_module_submit') ?>" />
        <a
            href="<?php echo $this->url(sprintf('/cms/delete/slice/%d', $slice->getId())) ?>"
            class="drop"
            data-target="slice-<?php echo $slice->getId() ?>"><?php echo __('action_module_delete') ?></a>
    </div>
</form>
<?php if (isset($reinitJs) && $reinitJs): ?>
<!-- end of module dialog -->
<script>
    tabbed();
    <?php if (isset($effect) && $effect): ?>
        $('#slice-'+<?php echo $slice->getId() ?>).effect('bounce', { times: 2}, 300);
    <?php endif; ?>
</script>
<!-- reinit the textareas -->
<?php endif; ?>