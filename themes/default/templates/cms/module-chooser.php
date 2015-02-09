<?php
/**
 * Form to add a new slice using a certain module.
 *
 * This partial is used by cms/slices and it allows the user to choose a module
 * and add a slice to a certain region and language of an article.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if ($carticle->getId() != $carticle->aka()->getId()) return // no module chooser on aka arts ?>
<form
    id="module-chooser-<?php echo $region->getId() ?>-<?php echo $this->user()->language() ?>"
    class="otherplace"
    method="post"
    action="<?php echo $this->url('/cms/add/slice') ?>"
    accept-charset="utf-8"
    data-container="slice-container-<?php echo $region->getId() ?>-<?php echo $this->user()->language() ?>"
    enctype="multipart/form-data">
    <div>
        <input
            type="hidden"
            name="dialog[article_id]"
            value="<?php echo $article->getId() ?>" />
        <input
            type="hidden"
            name="dialog[region]"
            value="<?php echo $region->getId() ?>" />
        <input
            type="hidden"
            name="dialog[iso]"
            value="<?php echo $this->user()->language() ?>" />
    </div>
    <fieldset>
        <legend class="verbose"><?php echo __('module_chooser_legend') ?></legend>
        <select
            name="dialog[mode]">
            <option value=""><?php echo __('select_a_mode') ?></option>
            <?php foreach ($modules as $_module_id => $_module): ?>
            <option value="<?php echo $_module->name ?>"><?php echo __('module_'.$_module->name) ?></option>
            <?php endforeach ?>
        </select>
        <input
            type="submit"
            name="submit"
            value="<?php echo __('action_module_chooser_submit') ?>" />
    </fieldset>
</form>
