<?php
/**
 * Partial for a criteria (inner criteria).
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div>
	<input type="hidden" name="filter[ownCriteria][<?php echo $n ?>][type]" value="criteria" />
	<input type="hidden" name="filter[ownCriteria][<?php echo $n ?>][id]" value="<?php echo $criteria->getId() ?>" />
	<input type="hidden" name="filter[ownCriteria][<?php echo $n ?>][tag]" value="<?php echo $criteria->tag ?>" />
</div>
<div class="row">
    <div class="span2">
		<div class="rgt">
			<input
				type="text"
				name="filter[ownCriteria][<?php echo $n; ?>][sequence]"
				value="<?php echo htmlspecialchars($criteria->sequence); ?>" />
		</div>
    </div>
	<div class="span1">
		<div class="rgt">
			<input type="hidden" name="filter[ownCriteria][<?php echo $n ?>][switch]" value="0" />
			<input
				type="checkbox"
				name="filter[ownCriteria][<?php echo $n ?>][switch]"
				value="1"
				<?php echo ($criteria->switch) ? 'checked="checked"' : '' ?> />
		</div>
	</div>
	<div class="span4">
		<div id="inner-criteria-wrapper-<?php echo $n ?>">
		    <?php echo $this->partial('model/filter/form/own/innercriteriawrapper', array(
		       'n' => $n,
		       'criteria' => $criteria,
			   'record' => $record
		    )) ?>
		</div>
	</div>
    <div class="span5">    
		<input
			type="text"
			name="filter[ownCriteria][<?php echo $n; ?>][value]"
			value="<?php echo htmlspecialchars($criteria->value); ?>" />
	</div>
</div>
