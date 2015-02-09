<?php
/**
 * Article-Slice-Container partial for CMS template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php echo $this->partial('cms/article-container') ?>
<?php echo $this->partial('cms/slice-container') ?>
<!-- JS to re-init so ajax loaded tabbed lists -->
<script>
    tabbed();
    sortables();
</script>
<!-- End of re-call tabbed() to init tabs -->
