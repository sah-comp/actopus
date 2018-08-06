<?php
/**
 * Scaffold table partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<form
    id="index-<?php echo $record->getMeta('type') ?>"
    class="scaffold-table"
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

                    <?php echo $this->partial(sprintf('model/%s/table/thead/columns', $record->getMeta('type')), array('record' => $record, 'attributes' => $attributes)) ?>
                </tr>
                <?php if (isset($filter) && is_a($filter, 'RedBean_OODBBean') && $filter->hasFilter($attributes) && $_filter_html = $this->partial(sprintf('shared/scaffold/criterias', $record->getMeta('type')), array('attributes' => $attributes))): ?>
                <tr
                    class="filter">
                    <th>
                        <input
                            type="hidden"
                            name="filter[type]"
                            value="filter" />
                        <input
                            type="hidden"
                            name="filter[id]"
                            value="<?php echo $filter->getId() ?>" />
                        <input
                            type="hidden"
                            name="filter[model]"
                            value="<?php echo htmlspecialchars($filter->model) ?>" />
                        <input
                            type="hidden"
                            name="filter[name]"
                            value="<?php echo htmlspecialchars($filter->name) ?>" />
                        <input
                            type="hidden"
                            name="filter[logic]"
                            value="<?php echo htmlspecialchars($filter->logic) ?>" />
                        <input
                            type="hidden"
                            name="filter[rowsperpage]"
                            value="<?php echo (int)$filter->rowsperpage ?>" />
                        <input
                            type="hidden"
                            name="filter[user][type]"
                            value="user" />
                        <input
                            type="hidden"
                            name="filter[user][id]"
                            value="<?php echo $filter->user->getId() ?>" />
                        <input
                            type="submit"
                            class="ir filter-refresh"
                            name="submit"
                            title="<?php echo __('filter_submit_refresh') ?>"
                            value="<?php echo __('filter_submit_refresh') ?>" />
                    </th>
                    <th>
                        <input
                            type="submit"
                            class="ir filter-clear"
                            name="submit"
                            title="<?php echo __('filter_submit_clear') ?>"
                            value="<?php echo __('filter_submit_clear') ?>" />
                    </th>
                    <?php echo $_filter_html ?>

                </tr>
                <?php endif ?>
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
                    class="<?php echo $_record->getMeta('type') ?> table item <?php echo ($_record->invalid()) ? 'error' : '' ?> <?php echo ($_record->deleted()) ? 'deleted' : '' ?>"
                    data-href="<?php echo $this->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $_record->getMeta('type'), $_record->getId(), $_offset + $_row, 1, $layout, $order, $dir)) ?>">
                    <td>
                        <input
                            type="checkbox"
                            class="selector"
                            data-model="<?php echo $_record->getMeta('type') ?>"
                            data-id="<?php echo $_record->getId() ?>"
                            data-collector="<?php echo $this->url(sprintf('/%1$s/collector/%1$s/%2$d', $_record->getMeta('type'), $_record->getId())) ?>"
                            name="selection[<?php echo $_record->getMeta('type') ?>][<?php echo $_record->getId() ?>]"
                            value="1"
                            title="<?php echo __('scaffold_title_marker') ?>"
                            <?php echo ((isset($_SESSION['collector'][$_record->getMeta('type')][$_record->getId()]) && $_SESSION['collector'][$_record->getMeta('type')][$_record->getId()]) || (isset($selection[$_record->getMeta('type')][$_record->getId()]) && $selection[$_record->getMeta('type')][$_record->getId()])) ? self::CHECKED : '' ?> />
                    </td>

                    <td class="action">
                        <a href="<?php echo $this->url(sprintf('/%s/edit/%d/%d/%d/%s/%d/%d/', $_record->getMeta('type'), $_record->getId(), $_offset + $_row, 1, $layout, $order, $dir)) ?>" title="<?php echo __('scaffold_action_title_edit') ?>" class="edit ir"><?php echo __('action_edit') ?></a>
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
