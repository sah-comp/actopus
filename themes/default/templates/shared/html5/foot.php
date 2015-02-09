<?php
/**
 * Cinnebar shared footer template.
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
    </div>
    <!-- End of wrapper -->
    
    <!-- JavaScript at the bottom for fast page loading -->

    <!-- Grab Google CDN's jQuery, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="<?php echo $this->url('libs/jquery/jquery-1.8.0.min', 'js') ?>"><\/script>')</script>
    
    <!-- Grab Google CDN's jQuery-UI, with a protocol relative URL; fall back to local if offline -->
    <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.24/jquery-ui.min.js"></script>
    <script>window.jQuery.ui || document.write('<script src="<?php echo $this->url('libs/jquery/jquery-ui-1.8.22.min', 'js') ?>"><\/script>')</script>

    <!-- scripts concatenated and minified via build script and optional scripts -->
    <script src="<?php echo $this->url('script', 'js') ?>"></script>
    <script src="<?php echo $this->url('libs/jquery/jquery-scrolltofixed', 'js') ?>"></script>
    <?php foreach ($this->js() as $_n=>$_script): ?>
	<script src="<?php echo $this->url($_script, 'js'); ?>"></script>
    <?php endforeach; ?>
    <!-- end scripts -->
    
</body>
</html>