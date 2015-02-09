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
<a href="<?php echo $this->url(sprintf('/token/edit/%d/%d/%d/%s/%d/%d/', $record->getId(), $row, 1, $layout, $order, $dir)) ?>">
    <p class="id"><?php echo htmlspecialchars($record->getId()) ?></p>
    <p class="name"><?php echo htmlspecialchars($record->name) ?></p>
    <p class="translation <?php echo $this->language() ?>"><?php echo htmlspecialchars($record->in($this->language())->payload) ?></p>
</a>
