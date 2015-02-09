<?php
/**
 * Profile index page template.
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
    <?php echo $this->partial('shared/scaffold/languagechooser') ?>
    <fieldset>
        <legend class="verbose"><?php echo __('profile_legend') ?></legend>
        <div class="row">
            <label
                for="profile-email"
                class="<?php echo ($record->hasError('email')) ? 'error' : ''; ?>">
                <?php echo __('profile_label_email') ?>
            </label>
            <input
                id="profile-email"
                type="email"
                name="dialog[email]"
                value="<?php echo htmlspecialchars($record->email) ?>"
                required="required" />
        </div>
        <div class="row">
            <label
                for="profile-name"
                class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
                <?php echo __('profile_label_name') ?>
            </label>
            <input
                id="profile-name"
                type="text"
                name="dialog[name]"
                value="<?php echo htmlspecialchars($record->name) ?>"
                required="required" />
        </div>
    </fieldset>
    <div id="profile-tabs" class="bar tabbed">
        <?php echo $this->tabbed('profile-tabs', array(
            'profile-about' => __('profile_tab_about')
        )) ?>
    </div>
    <div class="tab-container">
        <div
            id="profile-about"
            class="tab">
        <?php foreach ($languages as $_language_id => $_language):
            $recordi18n = $record->i18n($_language->iso);
        ?>
        <fieldset
            class="i18n <?php echo $_language->iso ?>"
            style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
            <legend class="verbose"><?php echo __('profile_legend_about') ?></legend>
            <div>
                <input
                    type="hidden"
                    name="dialog[ownUseri18n][<?php echo $_language->getId() ?>][type]"
                    value="<?php echo $recordi18n->getMeta('type') ?>" />
                <input
                    type="hidden"
                    name="dialog[ownUseri18n][<?php echo $_language->getId() ?>][id]"
                    value="<?php echo $recordi18n->getId() ?>" />
                <input
                    type="hidden"
                    name="dialog[ownUseri18n][<?php echo $_language->getId() ?>][iso]"
                    value="<?php echo $_language->iso ?>" />
            </div>
            <div class="row">
                <label
                    for="profile-about"
                    class="<?php echo ($recordi18n->hasError('about')) ? 'error' : ''; ?>">
                    <?php echo __('profile_label_about') ?>
                </label>
                <textarea
                    id="profile-about"
                    name="dialog[ownUseri18n][<?php echo $_language->getId() ?>][about]"
                    placeholder="<?php echo __('profile_placeholder_about', $_language->iso) ?>"
                    rows="12"><?php echo htmlspecialchars($recordi18n->about) ?></textarea>
            </div>
        </fieldset>
        <?php
        endforeach;
        ?>
        </div>
    </div>
    <div class="toolbar">
        <input
            type="submit"
            name="submit"
            accesskey="s"
            value="<?php echo __('profile_submit') ?>" />

    </div>
</form>

</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
