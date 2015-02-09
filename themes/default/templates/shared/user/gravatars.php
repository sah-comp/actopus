<?php
/**
 * Gravatars partial template.
 *
 * If there are any users, they will be displayed as gravatar figures.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($users) && ! empty($users)): ?>
    <?php foreach ($users as $_id => $_user): ?>
    <a
        href="<?php echo $this->url(sprintf('/profile/visit/%s', $_user->ego)) ?>"
        class="avatar"
        title="<?php echo htmlspecialchars($this->textonly($_user->i18n()->about)) ?>">
    <figure>
        <img
            class="ir circular circular-48"
            style="width: 48px; height: 48px; left: 50%; margin-left: -24px;"
            src="<?php echo $this->gravatar($_user->email, 48); ?>"
            alt="<?php echo htmlspecialchars($_user->name) ?>" />
        <figcaption>
            <?php echo htmlspecialchars($_user->name) ?>
        </figcaption>
    </figure>
    </a>
    <?php endforeach ?>
<?php endif ?>
