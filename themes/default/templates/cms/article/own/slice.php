<?php
/**
 * ownSlice of Article of Page fieldset for cms partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div
    id="slice-<?php echo $n ?>"
    class="cms-slice module-<?php echo $slice->mode ?> slice <?php echo (isset($active) && $active) ? 'active' : null ?>"
    data-href="<?php echo $this->url(sprintf('/cms/slice/%d', $slice->getId())) ?>"
    data-container="slice-<?php echo $n ?>">
    
    <?php echo $this->partial('cms/slice-and-tools', array('slice' => $slice, 'n' => $n, 'backend' => $backend)) ?>
</div>
