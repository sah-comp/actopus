<?php
/**
 * ownSlice of Article fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php $slice_ident = sprintf('slice-%s-%s', $region_id, $n) ?>
<div id="<?php echo $slice_ident ?>" class="item slice">
    <div id="sliceid-<?php echo $n ?>">
	<a
		href="<?php echo $this->url(sprintf('/article/detach/own/slice/%s', $slice_ident)) ?>"
		class="detach"
		data-target="<?php echo $slice_ident ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
	<div>
		<input type="hidden" name="dialog[ownSlice][<?php echo $slice_ident ?>][type]" value="slice" />
		<input type="hidden" name="dialog[ownSlice][<?php echo $slice_ident ?>][id]" value="<?php echo $slice->getId() ?>" />
		<input type="hidden" name="dialog[ownSlice][<?php echo $slice_ident ?>][iso]" value="<?php echo $iso ?>" />
		<input type="hidden" name="dialog[ownSlice][<?php echo $slice_ident ?>][region]" value="<?php echo $region_id ?>" />
	</div>
	<div class="row">
        <div class="span3">
            <select name="dialog[ownSlice][<?php echo $slice_ident ?>][mode]">
                <option value=""><?php echo __('select_a_mode') ?></option>
                <?php foreach ($slice->modes() as $_mode): ?>
                <option
                    value="<?php echo $_mode ?>"
                    <?php echo ($slice->mode == $_mode) ? self::SELECTED : '' ?>><?php echo __('slice_mode_'.$_mode) ?></option>
                <?php endforeach ?>
            </select>
        </div>
	    <div class="span9">
    		<textarea
    		    class="scaleable"
    			name="dialog[ownSlice][<?php echo $slice_ident ?>][content]"
    			placeholder="<?php echo __('slice_placeholder_content', $iso) ?>"
    			rows="3"><?php echo htmlspecialchars($slice->content); ?></textarea>
	    </div>
	</div>
    </div>
</div>