<?php
/**
 * ownArticle of Page fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="article-<?php echo $n ?>" class="item article">    
	<a
		href="<?php echo $this->url(sprintf('/page/detach/own/article/%d', $n)) ?>"
		class="detach"
		data-target="article-<?php echo $n ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownArticle][<?php echo $n ?>][type]" value="article" />
		<input type="hidden" name="dialog[ownArticle][<?php echo $n ?>][id]" value="<?php echo $article->getId() ?>" />
	</div>
	<div class="row">
	    
        <div
            class="span5">
            <?php foreach ($languages as $_language_id => $_language):
                $articlei18n = $article->i18n($_language->iso);
            ?>
            <div
                class="i18n <?php echo $_language->iso ?>"
                style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
                <input
                    type="hidden"
                    name="dialog[ownArticle][<?php echo $n ?>][ownArticlei18n][<?php echo $_language->getId() ?>][type]"
                    value="<?php echo $articlei18n->getMeta('type') ?>" />
                <input
                    type="hidden"
                    name="dialog[ownArticle][<?php echo $n ?>][ownArticlei18n][<?php echo $_language->getId() ?>][id]"
                    value="<?php echo $articlei18n->getId() ?>" />
                <input
                    type="hidden"
                    name="dialog[ownArticle][<?php echo $n ?>][ownArticlei18n][<?php echo $_language->getId() ?>][iso]"
                    value="<?php echo $_language->iso ?>" />
                <input
                    type="text"
                    name="dialog[ownArticle][<?php echo $n ?>][ownArticlei18n][<?php echo $_language->getId() ?>][name]"
                    value="<?php echo htmlspecialchars($articlei18n->name) ?>" />
            </div>
        	<?php
    	    endforeach;
        	?>
    	</div>
        <div
            class="span5">
            <?php foreach ($languages as $_language_id => $_language):
                $articlei18n = $article->i18n($_language->iso);
            ?>
            <div
                class="i18n <?php echo $_language->iso ?>"
                style="display: <?php echo ($_language->iso == $this->user()->language()) ? self::DISPLAY_BLOCK : self::DISPLAY_NONE ?>;">
                <select
                    name="dialog[ownArticle][<?php echo $n ?>][ownArticlei18n][<?php echo $_language->getId() ?>][template_id]">
                    <option value="0"><?php echo __('template_none') ?></option>
                    <?php foreach ($templates as $_id => $_template): ?>
                    <option
                        value="<?php echo $_template->getId() ?>"
                        <?php echo ($articlei18n->template_id == $_template->getId()) ? self::SELECTED : '' ?>><?php echo htmlspecialchars($_template->name) ?></option>   
                    <?php endforeach ?>
                </select>
            </div>
        	<?php
    	    endforeach;
        	?>
    	</div>
        <div class="span2">	 
            <input
                type="hidden"
                name="dialog[ownArticle][<?php echo $n ?>][invisible]"
                value="0" />
            <input
                type="checkbox"
                name="dialog[ownArticle][<?php echo $n ?>][invisible]"
                <?php echo ($article->invisible) ? self::CHECKED : '' ?>
                value="1" />
    	</div>
	</div>
</div>
