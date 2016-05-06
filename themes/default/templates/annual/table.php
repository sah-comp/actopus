<?php
/**
 * Annual table partial.
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
    <fieldset>
        <legend class="verbose"><?php echo __($record->getMeta('type').'_legend_index') ?></legend>
        <table>
            <caption>
                <?php echo __('scaffold_caption_table', $total) ?>
            </caption>
            <thead>
                <tr>
                    <th class="switch">&nbsp;</th>
                    <th class="scaffold-action">&nbsp;</th>
                    <?php echo $this->partial(sprintf('model/%s/table/thead/columnsannual', $record->getMeta('type'))) ?>
                </tr>
            </thead>
    
            <tfoot>
                <tr>
                    <td>
                        <input
                            class="all"
                            type="checkbox"
                            name="void"
                            value="1"
                            title="<?php echo __('scaffold_select_all') ?>" />
                    </td>
                    <td colspan="<?php echo $colspan ?>">
                        <select name="action">
                            <option value="idle"><?php echo __('action_idle') ?></option>
                            <?php foreach ($actions['table'] as $_i => $_action): ?>
                            <option value="<?php echo $_action ?>"><?php echo __('action_'.$_action) ?></option>
                            <?php endforeach ?>
                        </select>
                        <input
                            type="submit"
                            name="submit"
                            value="<?php echo __('scaffold_submit_selection') ?>" />
                    </td>
                </tr>
            </tfoot>
    
            <tbody>
            <?php $_row = 0 ?>
            <?php foreach ($records as $_id => $_record): ?>
                <?php $_row++ ?>
                <?php $_offset = ($page - 1) * $limit ?>
                <tr
                    id="<?php echo $_record->getMeta('type') ?>-<?php echo $_record->getId() ?>"
                    class="<?php echo $_record->getMeta('type') ?> table item <?php echo ($_record->invalid()) ? 'error' : '' ?>"
                    data-href="<?php echo $this->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', 'annual', $_record->getId(), $_offset + $_row, 1, $layout, $order, $dir)) ?>">
                    <td>
                        <input
                            type="checkbox"
                            class="selector"
                            name="selection[annual][<?php echo $_record->getId() ?>]"
                            value="1"
                            title="<?php echo __('scaffold_title_marker') ?>"
                            <?php echo (isset($selection['annual'][$_record->getId()]) && $selection['annual'][$_record->getId()]) ? self::CHECKED : '' ?> />
                    </td>
                    <td class="action">
                        <a href="<?php echo $this->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', 'annual', $_record->getId(), $_offset + $_row, 1, $layout, $order, $dir)) ?>" title="<?php echo __('scaffold_action_title_edit') ?>" class="edit ir"><?php echo __('action_edit') ?></a>
                    </td>
                    <!-- end of action link in a row -->
                    <?php echo $this->partial(sprintf('model/%s/table/tbody/columns', $_record->getMeta('type')), array('record' => $_record)) ?>
                    <!-- end of attributes -->
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </fieldset>
</form>
