<?php
/**
 * Sandbox template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language() ?>">
<head>
	<meta charset="utf-8" />
	<style>
	    body {
	        font: 1.2em/1 'Georgia, serif';
	    }
	    section {
	        padding: 1em;
	        margin: 1em 0;
	        background-color: rgb(222, 222, 222);
	    }
    </style>
	<title><?php echo __('sandbox_title') ?></title>	
</head>
<body id="sandbox">
    <header>
        <hgroup>
            <h1><?php echo __('app_name') ?></h1>
            <h2><?php echo __('app_slogan') ?></h2>
            <nav id="nav">
                <?php echo $this->partial('sandbox/nav') ?>
            </nav>
        </hgroup>
    </header>
    <div id="main" class="main">
        <div id="content" class="content">
            <?php echo $this->textile(__('sandbox_content', null, null, 'textile')) ?>
            
            <section id="form">
                <h1>Form</h1>
                <form
                    id="form1"
                    action=""
                    method="post"accept-charset="utf-8">
                    <label for="name">
                        Name:
                    </label>
                    <input
                        id="name"
                        type="text"
                        name="name"
                        value="<?php echo htmlspecialchars($name) ?>" />
                </form>
            </section>
            
            <p><a href="#top">Go to top</a></p>
            
            <section id="url">
                <h1>URL Parameters</h1>
                <p><?php echo sprintf('Switch is %s', $switch) ?></p>
                <p>Add a <em>?switch=1</em> to the URL to turn it on or <a href="<?php echo $this->url('/sandbox/index/?switch=1') ?>">click here to call this page with the switch set to on</a>.</p>
            </section>

            <p><a href="#top">Go to top</a></p>
            
            <section id="session">
                <h1>Session</h1>
                <p>Counter-(Session): <?php echo $counter ?></p>
            </section>

            <p><a href="#top">Go to top</a></p>
            
            <section id="records">
                <h1>Records</h1>
                <p>List of records of type <?php echo $record->getMeta('type') ?></p>
            <?php
            /**
             * A typical loop to render records into a record template.
             *
             * @see templates/model/token/cover.php as an example.
             */
            ?>
            <?php foreach ($records as $_id => $_record): ?>
                <?php echo $_record->render('cover', $this); ?>
            <?php endforeach ?>
            
            </section>
            
            <p><a href="#top">Go to top</a></p>
            
            <section id="pagination">
                <h1>Pagination</h1>
                <ul>
                    <li><a href="<?php echo sprintf($this->url('/sandbox/index/%s/%d/%d/'), $record->getMeta('type'), ($offset-$limit), $limit) ?>">prev page</a></li>
                    <li><a href="<?php echo sprintf($this->url('/sandbox/index/%s/%d/%d/'), $record->getMeta('type'), ($offset+$limit), $limit) ?>">next page</a></li>
                </ul>
            </section>
            
            <p><a href="#top">Go to top</a></p>
            
        </div>
        <div id="sidebar" class="sidebar">
            <?php echo $this->textile(__('sandbox_sidebar', null, null, 'textile')) ?>
            <p><a href="<?php echo $this->url('/welcome/index/') ?>">Willkommen!</a></p>
        </div>
    </div>
    <footer>
        <?php echo $this->textile(__('sandbox_footer', null, null, 'textile')) ?>
        <p class="credit"><?php echo __('app_credit') ?></p>
        <p class="info">{{memory_usage}}MB - {{execution_time}}s - IP: {{remote_addr}}</p>
    </footer>
</body>
</html>
