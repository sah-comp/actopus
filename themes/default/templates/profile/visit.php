<?php
/**
 * Profile visit page template.
 *
 * This template is a readonly view of a users profile.
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

<article>
    <header>
        <h1><?php echo htmlspecialchars($record->name) ?></h1>
        <h2><a href="mailto:<?php echo htmlspecialchars($record->email) ?>"><?php echo htmlspecialchars($record->email) ?></a></h2>
        <img
            src="<?php echo $this->gravatar($record->email, 72) ?>"
            width="72"
            height="72"
            alt="<?php echo htmlspecialchars($record->name()) ?>"
            title="<?php echo htmlspecialchars($record->name()) ?>"
            class="circular" />
    <header>
<?php echo $this->textile($record->i18n()->about) ?>
</article>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
