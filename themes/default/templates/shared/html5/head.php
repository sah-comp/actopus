<?php
/**
 * Cinnebar shared header template.
 *
 * This template is used by other templates as a partial. It does not make sense to use it
 * as an main template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html class="ie ie6 no-js" lang="<?php echo $this->language(); ?>"> <![endif]-->
<!--[if IE 7 ]>    <html class="ie ie7 no-js" lang="<?php echo $this->language(); ?>"> <![endif]-->
<!--[if IE 8 ]>    <html class="ie ie8 no-js" lang="<?php echo $this->language(); ?>"> <![endif]-->
<!--[if IE 9 ]>    <html class="ie ie9 no-js" lang="<?php echo $this->language(); ?>"> <![endif]-->
<!--[if gt IE 9]><!--><html class="no-js" lang="<?php echo $this->language(); ?>"><!--<![endif]-->
<!-- the "no-js" class is for Modernizr. -->
<head>
    
	<meta charset="utf-8">
	
    <title><?php echo htmlspecialchars($this->title()) ?></title>

    <!-- Mobile viewport optimized: h5bp.com/viewport -->
    <meta name="viewport" content="width=device-width">

    <link rel="stylesheet" href="<?php echo $this->url('style', 'css'); ?>">
    <?php foreach ($this->styles() as $_n=>$_stylesheet): ?>
    <link rel="stylesheet" href="<?php echo $this->url($_stylesheet, 'css'); ?>">
    <?php endforeach; ?>

    <!--[if lt IE 9]>
    <script src="<?php echo $this->url('libs/html5shiv', 'js'); ?>"></script>
    <![endif]-->
    <script type="text/javascript">
        var lasttab = "<?php echo isset($_SESSION['scaffold']['lasttab']) ? $_SESSION['scaffold']['lasttab'] : '' ?>";
    </script>
</head>
<body>
    <div id="wrapper" class="wrapper">
