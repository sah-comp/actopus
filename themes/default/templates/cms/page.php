<?php
/**
 * CMS page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div id="main" class="main">

    <div
        id="page-<?php echo $record->getId() ?>"
        class="panel cms">
            <div class="row">
                <div
                    id="page-container"
                    class="page span3">
                    <a
                        href="<?php echo $this->url(sprintf('/cms/page/%d', $record->getId())) ?>"
                        class="root active">
                        <?php echo htmlspecialchars($record->i18n()->name) ?>
                    </a>
                    <?php echo $sitemap->render(array('id' => 'sitemap')) ?>
                </div>
                <!-- End of page-container -->
                <div
                    id="article-slice-container"
                    class="row span9">
                    <?php echo $this->partial('cms/article-container') ?>
                    <?php echo $this->partial('cms/slice-container') ?>
                </div>
                <!-- End of article-slice-container -->
            </div>
    </div>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
