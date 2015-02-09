<?php
/**
 * Sitemap viewhelper partial.
 *
 * This will generated an unordered list to be used 
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($sitemap) && is_a($sitemap, 'Cinnebar_Menu')): ?>
<?php echo $sitemap->render(array('id' => 'sitemap')) ?>
<?php endif ?>
