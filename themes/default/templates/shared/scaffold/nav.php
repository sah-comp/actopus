<?php
/**
 * Scaffold Navigation partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($scaffold_nav) && is_a($scaffold_nav, 'Cinnebar_Menu')): ?>
    <nav class="toolbar clearfix">
        <h2><?php echo __('toolbar_'.$record->getMeta('type').'_h2') ?></h2>
        <?php echo $scaffold_nav->render(array('class' => 'scaffold-menu')) ?>
    </nav>
    <!-- End of the scaffold navigation -->
<?php endif ?>
