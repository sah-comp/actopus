<?php
/**
 * Token cover row partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<a href="<?php echo $this->url(sprintf('/language/edit/%d/%d/%d/%s/%d/%d/', $record->getId(), $row, 1, $layout, $order, $dir)) ?>">
    <p class="id"><?php echo htmlspecialchars($record->getId()) ?></p>
    <p class="iso"><?php echo htmlspecialchars($record->iso) ?></p>
</a>
