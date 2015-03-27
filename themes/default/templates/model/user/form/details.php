<?php
/**
 * User fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('user_legend') ?></legend>
    <div class="row">
        <label
            for="user-email"
            class="<?php echo ($record->hasError('email')) ? 'error' : ''; ?>">
            <?php echo __('user_label_email') ?>
        </label>
        <input
            id="user-email"
            type="email"
            name="dialog[email]"
            value="<?php echo htmlspecialchars($record->email) ?>"
            required="required"
            style="background-image: url(<?php echo $this->gravatar($record->email, 24) ?>); background-position: center right; background-repeat: no-repeat;" />
    </div>
    <div class="row">
        <label
            for="user-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('user_label_name') ?>
        </label>
        <input
            id="user-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="user-shortname"
            class="<?php echo ($record->hasError('shortname')) ? 'error' : ''; ?>">
            <?php echo __('user_label_shortname') ?>
        </label>
        <input
            id="user-shortname"
            type="text"
            name="dialog[shortname]"
            value="<?php echo htmlspecialchars($record->shortname) ?>"
            required="required" />
    </div>
    <div class="row">
        <label
            for="user-num"
            class="<?php echo ($record->hasError('num')) ? 'error' : ''; ?>">
            <?php echo __('user_label_num') ?>
        </label>
        <input
            id="user-num"
            type="text"
            name="dialog[num]"
            value="<?php echo htmlspecialchars($record->num) ?>" />
    </div>
    <div class="row">
        <label
            for="user-startpage"
            class="<?php echo ($record->hasError('home')) ? 'error' : ''; ?>">
            <?php echo __('user_label_startpage') ?>
        </label>
        <select
            id="user-startpage"
            name="dialog[home]">
            <?php foreach ($domains as $_id => $_domain): ?>
            <option
                value="<?php echo $_domain->url ?>"
                <?php echo ($record->home == $_domain->url) ? self::SELECTED : '' ?>><?php echo __('domain_'.$_domain->name) ?></option>   
            <?php endforeach ?>
        </select>
    </div>
    <div class="row">
        <label
            for="user-pw"
            class="<?php echo ($record->hasError('pw')) ? 'error' : ''; ?>">
            <?php echo __('user_label_pw') ?>
        </label>
        <input
            type="password"
            id="user-pw"
            name="dialog[pw]"
            value=""
            required="required"
            <?php echo ( ! $record->getId()) ? '' : self::DISABLED ?> />
    </div>
    <div class="row">
        <input
            type="hidden"
            name="dialog[admin]"
            value="0" />
        <label
            for="user-admin"
            class="cb <?php echo ($record->hasError('admin')) ? 'error' : ''; ?>">
            <?php echo __('user_label_admin') ?>
        </label>
        <input
            id="user-admin"
            type="checkbox"
            name="dialog[admin]"
            <?php echo ($record->admin) ? self::CHECKED : '' ?>
            value="1" />
    </div>
</fieldset>
<div id="user-tabs" class="bar tabbed">
    <?php echo $this->tabbed('user-tabs', array(
        'user-team' => __('user_tab_team'),
        'user-role' => __('user_tab_role'),
        'user-status' => __('user_tab_status')
    )) ?>
</div>
<div class="tab-container">
    <fieldset
        id="user-team"
        class="tab">
        <legend class="verbose"><?php echo __('user_legend_team') ?></legend>
        <?php foreach ($teams as $_id => $_team): ?>
        <div class="row">
            <input
                type="hidden"
                name="dialog[sharedTeam][<?php echo $_team->getId() ?>][type]"
                value="team" />
            <input
                type="hidden"
                name="dialog[sharedTeam][<?php echo $_team->getId() ?>][id]"
                value="0" />
            <label
                for="user-team-<?php echo $_team->getId() ?>"
                class="cb"><?php echo __($_team->name) ?></label>
            <input
                type="checkbox"
                id="user-team-<?php echo $_team->getId() ?>"
                name="dialog[sharedTeam][<?php echo $_team->getId() ?>][id]"
                value="<?php echo $_team->getId() ?>"
                <?php echo (isset($record->sharedTeam[$_team->getId()])) ? self::CHECKED : '' ?> />
        </div>
        <?php endforeach ?>
    </fieldset>
    <fieldset
        id="user-role"
        class="tab">
        <legend class="verbose"><?php echo __('user_legend_role') ?></legend>
        <?php foreach ($roles as $_id => $_role): ?>
        <div class="row">
            <input
                type="hidden"
                name="dialog[sharedRole][<?php echo $_role->getId() ?>][type]"
                value="role" />
            <input
                type="hidden"
                name="dialog[sharedRole][<?php echo $_role->getId() ?>][id]"
                value="0" />
            <label
                for="user-role-<?php echo $_role->getId() ?>"
                class="cb"><?php echo __('role_'.$_role->name) ?></label>
            <input
                type="checkbox"
                id="user-role-<?php echo $_role->getId() ?>"
                name="dialog[sharedRole][<?php echo $_role->getId() ?>][id]"
                value="<?php echo $_role->getId() ?>"
                <?php echo (isset($record->sharedRole[$_role->getId()])) ? self::CHECKED : '' ?> />
        </div>
        <?php endforeach ?>
    </fieldset>
    <fieldset
        id="user-status"
        class="tab">
        <legend class="verbose"><?php echo __('user_legend_status') ?></legend>

        <div class="row">
            <input
                type="hidden"
                name="dialog[deleted]"
                value="0" />
            <label
                for="user-deleted"
                class="cb <?php echo ($record->hasError('deleted')) ? 'error' : ''; ?>">
                <?php echo __('user_label_deleted') ?>
            </label>
            <input
                id="user-deleted"
                type="checkbox"
                name="dialog[deleted]"
                <?php echo ($record->deleted) ? self::CHECKED : '' ?>
                value="1" />
        </div>
        
        <div class="row">
            <input
                type="hidden"
                name="dialog[banned]"
                value="0" />
            <label
                for="user-banned"
                class="cb <?php echo ($record->hasError('banned')) ? 'error' : ''; ?>">
                <?php echo __('user_label_banned') ?>
            </label>
            <input
                id="user-banned"
                type="checkbox"
                name="dialog[banned]"
                <?php echo ($record->banned) ? self::CHECKED : '' ?>
                value="1" />
        </div>

    </fieldset>
</div>