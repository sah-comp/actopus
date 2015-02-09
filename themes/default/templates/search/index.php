<?php
/**
 * Searcg index page template.
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
    <?php echo $this->partial('shared/master/gsearch') ?>
    <?php if ( ! empty($records)): ?>
    <hr />
    <?php foreach ($records as $_type => $_beans): ?>
    <?php if (empty($_beans)) continue ?>
    <section class="list gsearch-results type-<?php echo $_type ?>">
        <?php $_counter = 0 ?>
        <?php foreach ($_beans as $_bean_id => $_bean): ?>
        <?php $_counter++ ?>
        <div class="row">
            <div class="span3 talignright">
            <?php echo ($_counter == 1) ? __('domain_'.$_type) : '&nbsp;' ?>
            </div>
            <div class="span9">
                <?php echo $_bean->hitname($this) ?>
            </div>
        </div>
        <?php endforeach ?>
    </section>
    <?php endforeach ?>
    <?php endif ?>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
