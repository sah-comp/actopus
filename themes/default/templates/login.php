<?php
/**
 * Login page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>
<?php echo $this->partial('shared/master/header') ?>

<?php if ( ! empty($fck)): ?>
<!-- Start of notifications -->
<section class="notifications">
    <div class="alert alert-error">
        <?php echo __('login_failed_please_try_again') ?>
    </div>
</section>
<!-- End of notifications -->
<?php endif ?>

<div id="main" class="main login">
    <article class="copy login">
        <?php echo $this->textile(__('login_copy', null, null, 'textile')) ?>
    </article>
    <div id="content">
        <form
            id="dialog"
            class="panel login"
            action="<?php echo $this->url('/login/index/') ?>"
            method="post"
            accept-charset="utf-8">
            <div>
                <input
                    type="hidden"
                    name="dialog[goto]"
                    value="<?php echo htmlspecialchars($record->goto) ?>" />
            </div>
            <fieldset>
                <legend class="verbose"><?php echo __('login_legend') ?></legend>
                <div class="row">
                    <label for="name">
                        <?php echo __('login_label_username') ?>
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="dialog[name]"
                        value="<?php echo htmlspecialchars($record->name) ?>"
                        required="required"
                        autofocus="autofocus" />
                </div>
                <div class="row">
                    <label for="pw">
                        <?php echo __('login_label_password') ?>
                    </label>
                    <input
                        id="pw"
                        type="password"
                        name="dialog[pw]"
                        value="<?php echo htmlspecialchars($record->pw) ?>"
                        required="required" />
                </div>
            </fieldset>
            
            <div class="toolbar">
                <input type="submit" name="submit" value="<?php echo __('login_submit') ?>" />
            </div>
        </form>
    </div>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
