<?php
/**
 * Partial family details for a card.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<div id="card-family-container">
    <?php echo $this->partial('model/card/form/family/members') ?>
</div>
<div class="row">
    <label for="card-sibling">
        &nbsp;
    </label>
    <input
        id="card-sibling-id"
        type="hidden"
        name="siblingid"
        value="" />
    <input
        id="card-sibling-name"
        type="text"
        name="siblingname"
        class="autocomplete"
        data-source="<?php echo $this->url('/search/autocomplete/card/name?callback=?') ?>"
        data-spread='<?php echo json_encode(array('card-sibling-id' => 'id', 'card-sibling-name' => 'cardname')) ?>'
        placeholder="<?php echo __('card_hint_sibling') ?>"
        value="" />
    <a
        href="<?php echo $this->url(sprintf('/card/marry/%d/', $record->getId())) ?>"
        data-fragments='<?php echo json_encode(array('card-sibling-id' => 'on')) ?>'
        data-remote="card-sibling-name"
        data-target="card-family-container"
        class="attachremote"
        title="<?php echo __('card_action_addsibling') ?>">
        <?php echo __('card_action_addsibling') ?>
    </a>
</div>
