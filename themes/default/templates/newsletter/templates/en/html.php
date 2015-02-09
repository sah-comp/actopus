<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	
	<style type="text/css">
		body {
			font-family: 'Arial';
			margin: 1em;
			font-size: 133%;
		}
	</style>
	<title><?php echo htmlspecialchars($record->name) ?></title>
</head>
<body>
	<article>
        <h1><?php echo htmlspecialchars($record->name) ?></h1>
        <?php echo $this->textile($record->teaser) ?>
        <?php echo $this->textile($record->content) ?>
        <?php $_articles = $record->sharedArticle; ?>
        <?php if ( ! empty($_articles)): ?>
            <?php foreach ($_articles as $_id => $_article): ?>
                <section>
                    <h1><?php echo htmlspecialchars($_article->name) ?></h1>
                    <?php echo $this->textile($_article->teaser) ?>
                    <?php echo $this->textile($_article->content) ?>
                </section>
            <?php endforeach ?>
        <?php endif ?>
        
    </article>
</body>
</html>