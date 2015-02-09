<?php
/**
 * Partial to generate thead th columns of a bean.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php foreach ($attributes as $_i => $_attribute): ?>
    <?php
        $_class = $record->getMeta('type').' fn-'.$_attribute['attribute'].' order';
        $_dir = 0;
        if ($order == $_i):
            $_dir = ! $dir;
            $_class .= ' active '.$orderclass;
        endif;
        if (isset($_attribute['class'])) $_class .= ' '.$_attribute['class'];
    ?>
<th class="<?php echo $_class ?>">
    <a href="<?php echo $this->url(sprintf('/%s/index/%d/%d/%s/%d/%d/', $record->getMeta('type'), 1, $limit, $layout, $_i, $_dir)) ?>"><?php echo __($record->getMeta('type').'_label_'.$_attribute['attribute']) ?></a>
</th>
<?php endforeach ?>
