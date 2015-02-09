<?php
/**
 * Backend of slice bean mode = textile.
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
        <legend class="verbose"><?php echo __('module_textile_backend') ?></legend>
        <textarea
            class="scaleable"
            name="dialog[content]"><?php echo htmlspecialchars($slice->content) ?></textarea>
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
    scaleTextareas();
    <?php if (isset($effect) && $effect): ?>
        $('#slice-'+<?php echo $slice->getId() ?>).effect('bounce', { times: 2}, 300);
    <?php endif; ?>
</script>
<!-- reinit the textareas -->
<?php endif; ?>