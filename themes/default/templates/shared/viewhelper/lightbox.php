<?php
/**
 * Lightbox viewhelper partial.
 *
 * This will generated a lightbox link.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($media) && is_a($media, 'RedBean_OODBBean')): ?>
<a
    href="/uploads/<?php echo $media->file ?>"
    rel="lightbox<?php echo (empty($group)) ? '' : '['.$group.']' ?>">
    <img
        src="<?php echo $this->url(sprintf('/media/image/%s/%d/%d', $media->name, $width, $height)) ?>"
        style="width: <?php echo (int)$width ?>px; height: <?php echo (int)$height ?>px; border: 0;" />
</a>
<?php endif ?>
