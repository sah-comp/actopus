<?php
/**
 * Info history partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<section class="info">
<?php if ($record->getId()): ?>
    <?php echo __('history_info_template', array(date('Y-m-d H:i:s', $record->stamp), $record->user()->name())) ?>
<?php else: ?>
    <?php echo __('history_info_not_available') ?>
<?php endif ?>
</section>