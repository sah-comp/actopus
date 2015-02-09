<?php
/**
 * Scaffold report page template.
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
    <section class="list">
        <header>
            <h1 class="visuallyhidden"><?php echo __('scaffold_h1_index') ?></h1>
        </header>
    <?php
        /**
         * If the requested layout 
         */
        if ($this->exists(sprintf('model/%s/%s', $record->getMeta('type'), $layout))) {
            echo $this->partial(sprintf('model/%s/%s', $record->getMeta('type'), $layout));
        } else {
            echo $this->partial(sprintf('shared/scaffold/table/%s', 'report'));
        }
    ?>
    </section>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
