<?php
/**
 * Install page template.
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
    <article class="copy">
        <?php echo $this->textile(__('install_copy', null, null, 'textile')) ?>
    </article>
    <div id="content">
        <form
            id="dialog"
            class="panel install"
            action="<?php echo $this->url('/install/index/') ?>"
            method="post"
            accept-charset="utf-8">
            <fieldset>
                <legend class="verbose"><?php echo __('install_legend') ?></legend>
                <div class="row">
                    <label for="install-pw">
                        <?php echo __('install_label_password') ?>
                    </label>
                    <input
                        id="name"
                        type="password"
                        name="dialog[pw]"
                        value=""
                        required="required"
                        autofocus="autofocus" />
                </div>
            </fieldset>
            
            <div class="toolbar">
                <input type="submit" name="submit" value="<?php echo __('install_submit') ?>" />
            </div>
        </form>
    </div>
</div>


<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
