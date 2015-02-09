<?php echo $this->textonly($record->name) ?>

<?php echo $this->textonly($record->teaser) ?>

<?php echo $this->textonly($record->content), "\n\n" ?>

<?php $_articles = $record->sharedArticle; ?>
<?php if ( ! empty($_articles)): ?>
<?php foreach ($_articles as $_id => $_article): ?>

<?php echo $this->textonly($_article->name) ?>

<?php echo $this->textonly($_article->teaser) ?>

<?php echo $this->textonly($_article->content), "\n\n" ?>

<?php endforeach ?>
<?php endif ?>
