<?php
/**
 * Sandbox partial template.
 *
 * Example of a main template that is built by using mostly shared partials. By using a shared partial
 * template you are able to setup a complete html page, while focussing on the page's content.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div class="main">

    <article>
        <section>
            <h1>This page uses partials</h1>
            <p>You just have to add the content of your special page.</p>
        </section>
    </article>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
