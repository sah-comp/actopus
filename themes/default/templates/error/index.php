<?php
/**
 * Error page template for web.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div id="main" class="main row">
    <article class="copy">
        <?php echo $this->textile(__('error_content_'.$code, null, null, 'textile')) ?>
    </article>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
