<?php
/**
 * ownCriteria of Filter fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="criteria-<?php echo $n ?>" class="item criteria">    
	<a
		href="<?php echo $this->url(sprintf('/filter/detach/own/criteria/%d', $n)) ?>"
		class="detach"
		data-target="criteria-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<!-- beginning of inner div of criteria -->
	<div id="inner-criteria-<?php echo $n ?>">
	    <?php echo $this->partial('model/filter/form/own/innercriteria', array(
	       'n' => $n,
	       'criteria' => $criteria
	    )) ?>
	</div>
	<!-- end of inner div of criteria -->
</div>
