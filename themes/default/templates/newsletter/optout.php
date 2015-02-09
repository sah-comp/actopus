<?php
/**
 * Newsletter opt-out page template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('shared/html5/head') ?>

<header class="master clearfix">
    <hgroup>
        <h1><?php echo __('app_name') ?></h1>
        <h2><?php echo __('app_slogan') ?></h2>
    </hgroup>
</header>
<?php echo $this->partial('shared/master/notification') ?>
<!-- End of the master header -->


<div id="main" class="main optout">
    <article class="copy">
        <?php echo $this->textile(__('newsletter_optout_copy', null, null, 'textile')) ?>
    </article>
    <div id="content">
        <form
            id="dialog"
            class="panel optout"
            action="<?php echo $this->url(sprintf('/newsletter/optout/%s', $hash)) ?>"
            method="post"
            accept-charset="utf-8">
            <fieldset>
                <legend class="verbose"><?php echo __('newsletter_legend_optout') ?></legend>
                <div class="row">
                    <label for="name">
                        <?php echo __('newsletter_optout_label_email') ?>
                    </label>
                    <input
                        id="name"
                        type="email"
                        name="email"
                        value="<?php echo htmlspecialchars($record->email) ?>"
                        required="required"
                        disabled="disabled" />
                </div>
            </fieldset>
            
            <div class="toolbar">
                <input type="submit" name="submit" value="<?php echo __('newsletter_submit_optout') ?>" />
            </div>
        </form>
    </div>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
