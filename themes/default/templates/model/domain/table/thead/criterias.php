<?php
/**
 * Filter for Domain table row partial.
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
    <input
        type="text"
        class="filter"
        name="filter[ownCriteria][<?php echo $_i ?>][value]"
        value="<?php echo htmlspecialchars($_criteria->value) ?>"
        placeholder="<?php echo __('filter_placeholder_any') ?>" />
    <?php else: ?>
        &nbsp;
    <?php endif ?>
</th>
<?php endforeach ?>
