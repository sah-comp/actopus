<?php
/**
 * Sandbox template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php $this->addStyle('sandbox/menu') ?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<div class="main">
    <pre><?php var_dump($menu) ?></pre>
    <hr />
    <nav>
        <?php echo $menu->render(array('class' => 'master'), $this->url('/profile')) ?>
    </nav>
    <hr />
    
    <pre><?php var_dump($menu2) ?></pre>
    <hr />
    <nav>
        <?php echo $menu2->render(array('class' => 'master'), $this->url('/home/mail/inbox')) ?>
    </nav>    
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
