<?php
/**
 * Master urhere partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($urhere) && is_a($urhere, 'Cinnebar_Menu')): ?>
    <nav class="urhere">
        <h2 class="visuallyhidden"><?php echo __('menu_urhere') ?></h2>
        <?php echo $urhere->render(array('class' => 'urhere-menu clearfix')) ?>
    </nav>
    <!-- End of the urhere navigation (urhere) -->
<?php endif ?>
