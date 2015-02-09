<?php
/**
 * CMS index page template.
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

    <form
        id="index-<?php echo $record->getMeta('type') ?>"
        action=""
        method="post"
        accept-charset="utf-8">
        <fieldset>
            <legend class="verbose"><?php echo __($record->getMeta('type').'_legend_index') ?></legend>
            <?php $_row = 0 ?>
            <?php foreach ($records as $_id => $_record): ?>
                <?php $_row++ ?>
                <div
                    id="<?php echo $_record->getMeta('type') ?>-<?php echo $_record->getId() ?>"
                    class="<?php echo $_record->getMeta('type') ?> cover item site">
                    <?php echo $this->partial(sprintf('model/%s/cover/site', $_record->getMeta('type')), array('record' => $_record, 'row' => $_row)) ?>
                </div>
            <?php endforeach ?>
        </fieldset>
    </form>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
