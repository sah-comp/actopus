<?php
/**
 * Product fieldset for editing partial.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<fieldset>
    <legend class="verbose"><?php echo __('product_legend') ?></legend>
    <div class="row">
        <label
            for="product-name"
            class="<?php echo ($record->hasError('name')) ? 'error' : ''; ?>">
            <?php echo __('product_label_name') ?>
        </label>
        <input
            id="product-name"
            type="text"
            name="dialog[name]"
            value="<?php echo htmlspecialchars($record->name) ?>"
            required="required" />
    </div>
</fieldset>

