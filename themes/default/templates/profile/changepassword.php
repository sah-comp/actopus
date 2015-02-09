<?php
/**
 * Change password page template.
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

<form
    id="index-profile"
    class="panel profile"
    method="post"
    action=""
    accept-charset="utf-8">
    <div>
        <input type="hidden" name="dialog[type]" value="<?php echo $record->getMeta('type') ?>" />
        <input type="hidden" name="dialog[id]" value="<?php echo $record->getId() ?>" />
    </div>
    <fieldset>
        <legend class="verbose"><?php echo __('profile_legend_changepassword') ?></legend>
        <div class="row">
            <label
                for="profile-password"
                class="<?php echo ($record->hasError('pw')) ? 'error' : ''; ?>">
                <?php echo __('profile_label_pw') ?>
            </label>
            <input
                type="password"
                id="profile-password"
                name="dialog[pw]"
                value=""
                required="required" />
        </div>
        <div class="row">
            <label
                for="profile-password-new"
                class="<?php echo ($record->hasError('pw_new')) ? 'error' : ''; ?>">
                <?php echo __('profile_label_pw_new') ?>
            </label>
            <input
                type="password"
                id="profile-password-new"
                name="dialog[pw_new]"
                value="" />
        </div>
        <div class="row">
            <label
                for="profile-password-repeated"
                class="<?php echo ($record->hasError('pw_repeated')) ? 'error' : ''; ?>">
                <?php echo __('profile_label_pw_repeated') ?>
            </label>
            <input
                type="password"
                id="profile-password-repeated"
                name="dialog[pw_repeated]"
                value="" />
        </div>
    </fieldset>
    <div class="toolbar">
        <input
            type="submit"
            name="submit"
            accesskey="s"
            value="<?php echo __('profile_submit_changepassword') ?>" />

    </div>
</form>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
