<?php
/**
 * Master Pagination partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($pagination) && is_a($pagination, 'Cinnebar_Pagination')): ?>
    <nav class="pagination clearfix">
        <h2 class="visuallyhidden"><?php echo __('menu_pagination') ?></h2>
        <?php echo $pagination->render() ?>
    </nav>
    <!-- End of the master pagination -->
<?php endif ?>
