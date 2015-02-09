<?php
/**
 * Slices for a cms/page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php
// we need tabs or a pull down to change regions
$_regs = array();
foreach ($regions as $_region_id => $_region) {
    $_regs['region-'.$_region->getId().'-'.$this->user()->language()] = $_region->name;
}
$_regs['region-meta-'.$this->user()->language()] = __('meta');
?>
<div
    id="region-tabs"
    class="i18n <?php echo $this->user()->language() ?> bar tabbed"
    style="display: block;">
    <?php echo $this->tabbed('region-tabs', $_regs) ?>
</div>
<?php foreach ($regions as $_region_id => $_region): ?>
<div
    class="i18n <?php echo $this->user()->language() ?>"
    style="display: block;">
    <legend class="verbose"><?php echo __('region_legend') ?></legend>

    <div
        id="region-<?php echo $_region->getId() ?>-<?php echo $this->user()->language() ?>"
        class="tab">
        
        <div
            id="slice-container-<?php echo $_region->getId() ?>-<?php echo $this->user()->language() ?>"
            class="container attachable sortable slice autoheight"
            data-href="<?php echo $this->url(sprintf('/cms/sortable/slice/slice')) ?>"
            data-container="slice-container-<?php echo $_region->getId() ?>-<?php echo $this->user()->language() ?>"
            data-variable="slice">
        <?php foreach ($carticle->aka()->sliceByRegionAndLanguage($_region->getId(), $this->user()->language(), false) as $_n => $_record): ?>
            <?php echo $this->partial(sprintf('cms/article/own/%s', 'slice'), array('n' => $_n, 'slice' => $_record, 'region_id' => $_region->getId(), 'iso' => $this->user()->language())) ?>
        <?php endforeach ?>
                    
    	</div>
    	
    	<div
    	    id="region-<?php echo $_region->getId() ?>-attach-control">
            <?php echo $this->partial('cms/module-chooser', array('article' => $carticle, 'region' => $_region)) ?>
        </div>

    </div>

</div>
<?php endforeach ?>

<?php echo $this->partial('cms/article/own/meta') ?>

<?php if (isset($reinitJs) && $reinitJs): ?>
    <script>
        tabbed();
        sortables();
    </script>
<?php endif ?>