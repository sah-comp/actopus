<?php
/**
 * Master Header partial template.
 *
 * You may use replacements to show some debug information:
 * {{memory_usage}}MB - {{execution_time}}s - IP: {{remote_addr}}
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<footer class="master-footer">
    <?php echo $this->partial('shared/master/info') ?>
    <?php echo $this->partial('shared/master/pagination') ?>
    <?php echo $this->textile(__('app_footer', null, null, 'textile')) ?>
    <p class="sys-credit"><?php echo __('app_credit') ?></p>
    <p class="sys-usage"><?php echo __('app_name'), ' ', Cinnebar_Facade::RELEASE ?></p>
</footer>
<!-- End of the master footer -->
