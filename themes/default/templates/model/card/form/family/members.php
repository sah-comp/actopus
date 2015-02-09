<?php
/**
 * Partial family members to show all related cards.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php foreach ($members as $_card_id => $_card): ?>
    <?php if ($_card->getId() == $record->getId()) continue; ?>
<div
    id="card-family-member-<?php echo $_card->getId() ?>"
    class="item card member">
    <a
		href="<?php echo $this->url(sprintf('/card/divorce/%d/%d', $record->getId(), $_card->getId())) ?>"
		class="detach"
		data-target="card-family-member-<?php echo $_card->getId() ?>">
			<span><?php echo __('scaffold_detach') ?></span>
	</a>
    <div class="row">
        <div class="span3">&nbsp;</div>
        <div class="span2">
            <?php echo htmlspecialchars($_card->name) ?>
            <?php echo $this->beanlink($_card, 'name') ?>
        </div>
        <div class="span2">
            <?php echo $_card->countryIso() ?>
        </div>
        <div class="span1">
            <?php echo htmlspecialchars($_card->cardtypeName()) ?>
        </div>
        <div class="span1">
            <?php echo htmlspecialchars($_card->cardstatusName()) ?>
        </div>
        <div class="span1">
            <?php echo htmlspecialchars($_card->attorneyName()) ?>
        </div>
        <div class="span2">
            &nbsp;
        </div>
    </div>
</div>
<?php endforeach ?>
