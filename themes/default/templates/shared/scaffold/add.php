<?php
/**
 * Scaffold add page template.
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
    <section>
        <header>
            <h1 class="visuallyhidden"><?php echo __('scaffold_h1_add') ?></h1>
        </header>
        
        <form
            id="index-<?php echo $record->getMeta('type') ?>"
            class="panel <?php echo $record->getMeta('type') ?>"
            method="post"
            action=""
            accept-charset="utf-8"
            enctype="multipart/form-data">
            <div>
                <input type="hidden" id="lasttab" name="lasttab" value="<?php echo isset($_SESSION['scaffold']['lasttab']) ? $_SESSION['scaffold']['lasttab'] : '' ?>" />
                <input type="hidden" name="dialog[type]" value="<?php echo $record->getMeta('type') ?>" />
                <input type="hidden" name="dialog[id]" value="0" />
            </div>
            <?php echo $this->partial('shared/scaffold/languagechooser') ?>
            <?php echo $this->partial(sprintf('model/%s/form/details', $record->getMeta('type'))) ?>
            <div class="toolbar">

                <select name="action">
                    <?php foreach ($actions[$action] as $_i => $_action): ?>
                    <option
                        value="<?php echo $_action ?>"
                        <?php echo ($followup == $_action) ? self::SELECTED : '' ?>><?php echo __('action_'.$_action) ?></option>
                    <?php endforeach ?>
                </select>
                <input
                    type="submit"
                    name="submit"
                    accesskey="s"
                    value="<?php echo __('scaffold_submit_apply_action') ?>" />

            </div>
        </form>
        
    </section>
    <?php echo $this->partial('shared/scaffold/info') ?>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
