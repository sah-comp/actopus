<div class="row">
	<div class="span6">
	    <select
	        id="criteria-attribute-<?php echo $n ?>"
	        class="updateonchange"
	        name="filter[ownCriteria][<?php echo $n ?>][attribute]"
	        data-target="inner-criteria-wrapper-<?php echo $n ?>"
	        data-href="<?php echo $this->url(sprintf('/filter/updcriteria/%d/', $n)) ?>"
	        data-fragments='<?php echo json_encode(array('criteria-attribute-'.$n => 'on', 'filter-model' => 'on')) ?>'>
	        <?php foreach ($record->attributes('report') as $_attribute): ?>
	            <?php $_orderclause = $_attribute['orderclause'] ?>
	            <?php if (isset($_attribute['filter']['orderclause'])): ?>
	                <?php $_orderclause = $_attribute['filter']['orderclause'] ?>
	            <?php endif ?>
	        <option
	            value="<?php echo $_orderclause ?>"
	            <?php echo ($criteria->attribute == $_orderclause) ? self::SELECTED : '' ?>><?php echo __($record->getMeta('type').'_label_'.$_attribute['attribute']) ?></option>
	        <?php endforeach ?>
	    </select>
	</div>
	<div class="span6">
	    <select name="filter[ownCriteria][<?php echo $n ?>][op]">
	        <?php foreach ($criteria->getOperators($criteria->tag) as $_operator): ?>
	        <option
	            value="<?php echo $_operator ?>"
	            <?php echo ($criteria->op == $_operator) ? self::SELECTED : '' ?>><?php echo __('criteria_label_'.$_operator) ?></option>
	        <?php endforeach ?>
	    </select>
	</div>
</div>