<?php
/**
 * Master info partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($record) && is_a($record, 'RedBean_OODBBean')): ?>
    <?php if ($record->info()->getId()): ?>
        <p class="history info clearfix">
            <?php echo __('footer_template_info', array($record->info()->user()->name(), __('history_action_'.$record->info()->action), $this->timestamp($record->info()->stamp))) ?>
        </p>
    <?php endif ?>
    <!-- End of the master info on a bean/model/record -->
<?php endif ?>
