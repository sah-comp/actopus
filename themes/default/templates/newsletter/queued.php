<?php
/**
 * Newsletter queue page template.
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
    <div id="content">
        <form
            id="dialog"
            class="panel send"
            action="<?php echo $this->url(sprintf('/newsletter/send/%d/%d/%d/%s/%d/%d', $record->getId(), $page, $limit, $layout, $order, $dir)) ?>"
            method="post"
            accept-charset="utf-8">
            <div>
                <input
                    type="hidden"
                    name="dialog[type]"
                    value="newsletter" />
                <input
                    type="hidden"
                    name="dialog[id]"
                    value="<?php echo $record->getId() ?>" />
            </div>
            <fieldset>
                <legend class="verbose"><?php echo __('newsletter_legend_send') ?></legend>
                <div class="row">
                    <label
                        for="newsletter-name"
                        class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
                        <?php echo __('newsletter_label_name') ?>
                    </label>
                    <input
                        id="newsletter-name"
                        type="text"
                        name="dialog[name]"
                        value="<?php echo htmlspecialchars($record->name) ?>"
                        disabled="disabled" />
                </div>
                <div class="row">
                    <label
                        for="newsletter-queued"
                        class="<?php echo ($record->hasError('queued')) ? 'error' : ''; ?>">
                        <?php echo __('newsletter_label_queued') ?>
                    </label>
                    <input
                        id="newsletter-queued"
                        type="text"
                        name="dialog[queued]"
                        value="<?php echo $this->timestamp($record->queued) ?>"
                        disabled="disabled" />
                </div>
                <div class="row">
                    <input
                        id="newsletter-queued-reset"
                        type="checkbox"
                        name="dialog[queued]"
                        value="" />
                    <label
                        for="newsletter-queued-reset"
                        class="cb <?php echo ($record->hasError('queued')) ? 'error' : ''; ?>">
                        <?php echo __('newsletter_label_unqueue') ?>
                    </label>
                </div>
            </fieldset>
            
            <div id="newsletter-tabs" class="bar tabbed">
                <?php echo $this->tabbed('domain-tabs', array(
                    'newsletter-queue' => __('newsletter_tab_queue')
                )) ?>
            </div>
            <div class="tab-container">
                <fieldset
                    id="newsletter-queue"
                    class="tab">
                    <legend class="verbose"><?php echo __('newsletter_legend_queue') ?></legend>

                	<div class="row">
                	    <div class="span6"><?php echo __('queue_label_email') ?></div>
                    	<div class="span3"><?php echo __('queue_label_sent') ?></div>
                    	<div class="span3"><?php echo __('queue_label_open') ?></div>
                	</div>

                    <div
                        id="queue-container"
                        class="container queue">
                    <?php foreach ($record->own('queue') as $_n => $_record): ?>
                        <?php echo $this->partial(sprintf('model/%s/form/own/%s', $record->getMeta('type'), 'queue'), array('n' => $_n, 'queue' => $_record)) ?>
                    <?php endforeach ?>
            		</div>
                </fieldset>
            </div>


            <div class="toolbar">
                <input type="submit" name="submit" value="<?php echo __('newsletter_submit_queued') ?>" />  <?php echo __('or') ?> <a href="<?php echo $this->url(sprintf('/newsletter/edit/%d/%d/%d/%s/%d/%d', $record->getId(), $page, $limit, $layout, $order, $dir)) ?>"><?php echo __('go_back_to_newsletter') ?></a>
            </div>
        </form>
    </div>
</div>

<?php echo $this->partial('shared/master/footer') ?>
<?php echo $this->partial('shared/html5/foot') ?>
