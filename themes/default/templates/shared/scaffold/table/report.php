<?php
/**
 * Scaffold table for report partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<form
    id="report-<?php echo $record->getMeta('type') ?>"
    action=""
    method="post"
    accept-charset="utf-8">
    <div>
        <input
            type="hidden"
            name="filter[type]"
            value="filter" />
        <input
            type="hidden"
            name="filter[id]"
            value="<?php echo $filter->getId() ?>" />
        <input
            id="filter-model"
            type="hidden"
            name="filter[model]"
            value="<?php echo htmlspecialchars($filter->model) ?>" />
        <input
            type="hidden"
            name="filter[isreport]"
            value="1" />
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
    </div>
    <div class="panel filter">
        <fieldset>
            <legend class="verbose"><?php echo __($record->getMeta('type').'_legend_filter_report') ?></legend>
            <div class="row">
                <div class="span3">
                <label
                    for="filter-name"
                    class="rgt <?php echo ($filter->hasError('name')) ? 'error' : ''; ?>">
                    <?php echo __('filter_label_name') ?>
                </label>
                </div>
                <div class="span6">
                <input
                    id="filter-name"
                    class="autowidth"
                    type="text"
                    name="filter[name]"
                    value="<?php echo htmlspecialchars($filter->name) ?>" />
                </div>
                <div class="span3">

                    <select
                        id="card-country"
                        class="autowidth"
                        name="otherreport">
                        <option value=""><?php echo __('report_use_this') ?></option>
                        <?php foreach (R::find('filter', " model = 'card' AND isreport = 1 AND user_id = ? ORDER BY name", array($user->getId())) as $_report_id => $_report): ?>
                        <option
                            value="<?php echo $_report->getId() ?>"><?php echo $_report->name ?></option>
                        <?php endforeach ?>
                    </select>

                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend class="verbose"><?php echo __($record->getMeta('type').'_legend_criterias') ?></legend>
            <div id="criteria-container" class="container attachable detachable criteria">
                <?php foreach ($filter->with('ORDER BY id')->ownCriteria as $_n => $_record): ?>
                    <?php echo $this->partial(sprintf('model/%s/form/own/%s', $filter->getMeta('type'), 'criteria'), array('n' => $_n, 'criteria' => $_record)) ?>
                <?php endforeach ?>    
                    <a
                		href="<?php echo $this->url(sprintf('/%s/addcriteria/own/%s/%d', $filter->getMeta('type'), 'criteria', $filter->getId())) ?>"
                		class="attach"
                		data-target="criteria-container">
                			<span><?php echo __('scaffold_attach') ?></span>
                	</a>
            </div>
        </fieldset>
        <div class="toolbar">
            <input
                type="submit"
                name="submit"
                value="<?php echo __('filter_submit_refresh') ?>" />
                
            <input
                type="submit"
                name="submit"
                value="<?php echo __('filter_submit_clear') ?>" />
				
			<a href="<?php echo $this->url(sprintf('/%s/press/%d/%d/filter/0/0', $record->getMeta('type'), 1, 23)) ?>"><?php echo __('filter_link_export_report') ?></a>
                
            <input
                type="submit"
                name="submit"
                class="rgt"
                value="<?php echo __('filter_submit_delete') ?>" />
        </div>
    </div>
    <fieldset>
        <legend class="verbose"><?php echo __($record->getMeta('type').'_legend_report') ?></legend>
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
                            name="selection[<?php echo $_record->getMeta('type') ?>][<?php echo $_record->getId() ?>]"
                            value="1"
                            title="<?php echo __('scaffold_title_marker') ?>"
                            <?php echo (isset($selection[$_record->getMeta('type')][$_record->getId()]) && $selection[$_record->getMeta('type')][$_record->getId()]) ? self::CHECKED : '' ?> />
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
