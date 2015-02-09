<?php echo __('newsletter_linktext_view_in_browser'), ': ', $this->url(sprintf('/newsletter/view/%d/', $record->getId())), "\n\n" ?>

<?php echo $this->textonly($record->name) ?>

<?php echo $this->textonly($record->teaser) ?>

<?php echo $this->textonly($record->content), "\n\n" ?>

<?php if ( ! empty($articles)): ?>
<?php foreach ($articles as $_id => $_article): ?>

<?php echo $this->textonly($_article->name) ?>

<?php echo $this->textonly($_article->teaser) ?>

<?php echo $this->textonly($_article->content), "\n\n" ?>

<?php endforeach ?>
<?php endif ?>

<?php echo __('newsletter_linktext_optout'), ': ', $this->url(sprintf('/newsletter/optout/%s', $queue->emailhash)) ?>