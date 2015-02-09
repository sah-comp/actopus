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
            
            <section id="webdav">
                <h1>DAV</h1>
                <pre><?php print_r($dav) ?></pre>
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
