<?php
/**
 * Article-Container partial for CMS template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div
    id="article-container"
    class="container attachable detachable sortable article span3"
    data-href="<?php echo $this->url(sprintf('/cms/sortable/article/article')) ?>"
    data-container="article-container"
    data-variable="article">
<?php foreach ($articles as $_n => $_record): ?>
    <?php echo $this->partial(sprintf('cms/page/own/%s', 'article'), array('n' => $_n, 'article' => $_record)) ?>
<?php endforeach ?>
</div>
