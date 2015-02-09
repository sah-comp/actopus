<?php
/**
 * ownArticle of Page fieldset for cms partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="article-<?php echo $n ?>" class="item article">
    <a
        href="<?php echo $this->url(sprintf('/cms/article/%d', $article->getId())) ?>"
        class="hook <?php echo ($article->getId() == $carticle->getId()) ? 'active' : '' ?> <?php echo ($article->invisible) ? 'inactive' : '' ?> <?php echo ($article->fetchAs('article')->aka) ? 'alias' : '' ?>">
        <?php echo htmlspecialchars($article->aka()->i18n()->name) ?>
    </a>
</div>
<?php if (isset($reinitJs) && $reinitJs): ?>
    <script>
        $('#article-<?php echo $n ?> > a').trigger('click');
    </script>
<?php endif ?>
