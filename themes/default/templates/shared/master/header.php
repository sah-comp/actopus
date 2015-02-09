<?php
/**
 * Master Header partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<header class="master clearfix">
    <hgroup>
        <h1><?php echo __('app_name') ?></h1>
        <h2><?php echo __('app_slogan') ?></h2>
    </hgroup>
    <?php echo $this->partial('shared/master/nav') ?>
    <?php echo $this->partial('shared/master/urhere') ?>
</header>
<!-- End of the master header -->
<?php echo $this->partial('shared/master/notification') ?>
<?php echo $this->partial('shared/master/housekeeping') ?>