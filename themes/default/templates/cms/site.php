<?php
/**
 * Scaffold add page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div id="main" class="main">
    <section>
        <header>
            <h1 class="visuallyhidden"><?php echo __('scaffold_h1_add') ?></h1>
        </header>
        <p>
            <?php echo $this->textile(__('nyi'), null, 'textile') ?>
        </p>
    </section>
    <?php echo $this->partial('shared/scaffold/info') ?>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
