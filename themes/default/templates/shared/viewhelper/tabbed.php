<?php
/**
 * Tabbed viewhelper partial.
 *
 * This will generated an unordered list to be used 
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($tabbed) && is_array($tabbed)): ?>
<ul>
    <?php foreach ($tabbed['tabs'] as $_key => $_linktext): ?>
        <li>
            <a
                class="chubbytabby"
                id="tab-<?php echo $_key ?>"
                href="#<?php echo $_key ?>"
                ><?php echo $_linktext ?>
            </a>
        </li>
    <?php endforeach ?>
</ul>
<?php endif ?>
