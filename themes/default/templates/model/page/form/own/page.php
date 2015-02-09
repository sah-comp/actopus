<?php
/**
 * ownPage of Page fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="page-<?php echo $n ?>" class="item page">    
	<a
		href="<?php echo $this->url(sprintf('/page/detach/own/page/%d', $n)) ?>"
		class="detach"
		data-target="page-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownPage][<?php echo $n ?>][type]" value="page" />
		<input type="hidden" name="dialog[ownPage][<?php echo $n ?>][id]" value="<?php echo $page->getId() ?>" />
	</div>
	<div class="row">
	    
        <div
            class="span10">
            <?php foreach ($languages as $_language_id => $_language):
                $pagei18n = $page->i18n($_language->iso);
            ?>
            <div
                class="i18n <?php echo $_language->iso ?>"
                style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
                <input
                    type="hidden"
                    name="dialog[ownPage][<?php echo $n ?>][ownPagei18n][<?php echo $_language->getId() ?>][type]"
                    value="<?php echo $pagei18n->getMeta('type') ?>" />
                <input
                    type="hidden"
                    name="dialog[ownPage][<?php echo $n ?>][ownPagei18n][<?php echo $_language->getId() ?>][id]"
                    value="<?php echo $pagei18n->getId() ?>" />
                <input
                    type="hidden"
                    name="dialog[ownPage][<?php echo $n ?>][ownPagei18n][<?php echo $_language->getId() ?>][iso]"
                    value="<?php echo $_language->iso ?>" />
                <input
                    type="text"
                    name="dialog[ownPage][<?php echo $n ?>][ownPagei18n][<?php echo $_language->getId() ?>][name]"
                    value="<?php echo htmlspecialchars($pagei18n->name) ?>" />
            </div>
        	<?php
    	    endforeach;
        	?>
    	</div>
        <div class="span2">	 
            <input
                type="hidden"
                name="dialog[ownPage][<?php echo $n ?>][invisible]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownPage][<?php echo $n ?>][invisible]"
                <?php echo ($page->invisible) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	</div>
</div>
