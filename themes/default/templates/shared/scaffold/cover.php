<?php
/**
 * Scaffold cover partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<form
    id="index-<?php echo $record->getMeta('type') ?>"
    action=""
    method="post"
    accept-charset="utf-8">
    <fieldset class="orderclause">
        <legend class="verbose"><?php echo __('scaffold_legend_sort') ?></legend>
        <select
            name="order">
            <?php foreach ($attributes as $_i => $_attribute): ?>
            <option
                value="<?php echo $_i ?>"
                <?php echo ($_i == $order) ? self::SELECTED : '' ?>>
                <?php echo __($record->getMeta('type').'_label_'.$_attribute['attribute']) ?>
            </option>
            <?php endforeach ?>
        </select>
        <select
            name="dir">
            <?php foreach ($sortdirs as $_i => $_sortdir): ?>
            <option
                value="<?php echo $_i ?>"
                <?php echo ($_i == $dir) ? self::SELECTED : '' ?>>
                <?php echo __('scaffold_sortdir_'.strtolower($_sortdir)) ?>
            </option>
            <?php endforeach ?>
        </select>
        <input
            type="submit"
            name="submit"
            value="<?php echo __('scaffold_submit_order') ?>" />
    </fieldset>
    <fieldset>
        <legend class="verbose"><?php echo __($record->getMeta('type').'_legend_index') ?></legend>
        <?php $_row = 0 ?>
        <?php foreach ($records as $_id => $_record): ?>
            <?php $_row++ ?>
            <div
                id="<?php echo $_record->getMeta('type') ?>-<?php echo $_record->getId() ?>"
                class="<?php echo $_record->getMeta('type') ?> cover item">
                <?php echo $this->partial(sprintf('model/%s/cover/item', $_record->getMeta('type')), array('record' => $_record, 'row' => $_row)) ?>
            </div>
        <?php endforeach ?>
    </fieldset>
</form>
