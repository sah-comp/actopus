<?php
/**
 * Page cover row partial when acting as a site.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<a href="<?php echo $this->url(sprintf('/cms/root/%d/', $record->getId())) ?>">
    <p class="name"><?php echo htmlspecialchars($record->i18n()->name) ?></p>
</a>
