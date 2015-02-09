<?php
/**
 * Partial for filter::ownCriteria.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php foreach ($attributes as $_i => $_attribute): ?>
<th>
    <?php if (isset($_attribute['filter']) && is_array($_attribute['filter'])): ?>
    <?php $_criteria = $filter->getCriteria($_attribute) ?>
    <input
        type="hidden"
        name="filter[ownCriteria][<?php echo $_i ?>][type]"
        value="criteria" />
    <input
        type="hidden"
        name="filter[ownCriteria][<?php echo $_i ?>][id]"
        value="<?php echo $_criteria->getId() ?>" />
    <input
        type="hidden"
        name="filter[ownCriteria][<?php echo $_i ?>][op]"
        value="<?php echo htmlspecialchars($_criteria->op) ?>" />
    <input
        type="hidden"
        name="filter[ownCriteria][<?php echo $_i ?>][tag]"
        value="<?php echo htmlspecialchars($_criteria->tag) ?>" />
    <input
        type="hidden"
        name="filter[ownCriteria][<?php echo $_i ?>][attribute]"
        value="<?php echo htmlspecialchars($_criteria->attribute) ?>" />
    <?php if ($_criteria->tag == 'bool'): ?>
    <select
        class="filter-select"
        name="filter[ownCriteria][<?php echo $_i ?>][value]">
        <option
            value="">
            <?php echo __('filter_placeholder_any') ?>
        </option>
        <?php foreach (array(
            0 => __('bool_off'),
            1 => __('bool_on')
            ) as $_bool_val => $_bool_text): ?>
        <option
            value="<?php echo $_bool_val ?>"
            <?php echo ($_criteria->value != '' && $_bool_val == (int)$_criteria->value) ? 'selected="selected"' : '' ?>>
            <?php echo $_bool_text ?>
        </option>
        <?php endforeach ?>
    </select>
    <?php else: ?>
    <input
        type="text"
        class="filter"
        name="filter[ownCriteria][<?php echo $_i ?>][value]"
        value="<?php echo htmlspecialchars($_criteria->value) ?>"
        placeholder="<?php echo __('filter_placeholder_any') ?>" />
    <?php endif ?>
    <?php else: ?>
        &nbsp; <!-- no criteria for filtering -->
    <?php endif ?>
</th>
<?php endforeach ?>
