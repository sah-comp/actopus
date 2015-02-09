<?php
/**
 * Newsletter thankyou page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>

<header class="master clearfix">
    <hgroup>
        <h1><?php echo __('app_name') ?></h1>
        <h2><?php echo __('app_slogan') ?></h2>
    </hgroup>
</header>
<?php echo $this->partial('shared/master/notification') ?>
<!-- End of the master header -->


<div id="main" class="main">
    <article class="copy">
        <?php echo $this->textile(__('newsletter_thankyou_for_' . $thankyoufor, null, null, 'textile')) ?>
    </article>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
