<?php
/**
 * Master global search partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<form
    id="gsearchnav"
    class="panel gsearch"
    action="<?php echo $this->url('/search/index/') ?>"
    method="get"
    accept-charset="utf-8"
    title="<?php echo __('gsearch_form_title') ?>">
    <fieldset>
        <legend class="verbose"><?php echo __('gsearch_legend') ?></legend>
        <div class="row">
            <label for="gsearch-q-nav"><?php echo __('gsearch_label_q') ?></label>
            <input
                type="search"
                id="gsearch-q-nav"
                name="q"
                class="no-autocomplete"
                data-source="<?php echo $this->url('/search/autocomplete/tag/title?callback=?') ?>"
                data-spread='<?php echo json_encode(array('gsearch-q' => 'label')) ?>'
                value="<?php echo isset($q) ? htmlspecialchars($q) : '' ?>"
                placeholder="<?php echo __('gsearch_placeholder_q') ?>"
                accesskey="f"
                required="required" />
        </div>
    </fieldset>
</form>
