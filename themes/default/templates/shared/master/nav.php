<?php
/**
 * Master navigation (nav) partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (isset($nav) && is_a($nav, 'Cinnebar_Menu')): ?>
    <nav class="master">
        <h2 class="visuallyhidden"><?php echo __('menu_main') ?></h2>
        <?php echo $nav->render(array('class' => 'main-menu clearfix')) ?>
    </nav>
    <?php if (isset($user)): ?>
    <nav class="account">
        <!-- account menu -->
        <ul class="account-navigation clearfix">
            <li>
                <a
                    href="<?php echo $this->url('/profile/') ?>">
        			<img
        				src="<?php echo $this->gravatar($user->email, 16); ?>"
        				width="16"
        				height="16"
        				alt="<?php echo htmlspecialchars($user->name()) ?>" />
        			<?php echo htmlspecialchars($user->name()) ?>
                </a>
            </li>
            <li>
                <a
                    href="<?php echo $this->url('/logout/') ?>">
                    <?php echo __('domain_logout') ?>
                </a>
            </li>
        </ul>
        <!-- End of account menu -->
    </nav>
    <?php endif ?>
    <!-- End of the master navigation (nav) -->
<?php endif ?>
<?php if (isset($navfunc) && is_a($navfunc, 'Cinnebar_Menu')): ?>
    <nav class="navfunc">
        <h2 class="visuallyhidden"><?php echo __('menu_functions') ?></h2>
        <?php echo $navfunc->render(array('class' => 'func-menu clearfix')) ?>
    </nav>
<?php endif ?>
<?php if (isset($user)) echo $this->partial('shared/master/gsearchnav') ?>
