<?php
/**
 * Notifications partial template.
 *
 * @package Cinnebar
 * @subpackage Template
 * @author $Author$
 * @version $Id$
 */
?>
<?php if (! isset($user) || ! is_a($user, 'RedBean_OODBBean')) {
    return;
} ?>
<?php if (! $_notifications = $user->notifications()) {
    return;
} ?>
<!-- Start of notifications -->
<section
    id="notifications"
    class="notifications">
<?php foreach ($_notifications as $_id => $_notification): ?>
    <div class="alert alert-<?php echo $_notification->template ?>">
        <?php echo $this->textile($_notification->payload) ?>
    </div>
<?php endforeach ?>

<?php if (isset($record) && $record->hasErrors()): ?>
<div class="alert alert-error validation-errors">
    <?php foreach ($record->getErrors() as $_attribute => $_errors): ?>
        <h1><?php echo __($record->getMeta('type') . '_label_' . $_attribute) ?></h1>
        <ul>
        <?php foreach ($_errors as $_error_id => $_error): ?>
            <li><?php echo $_error ?></li>
        <?php endforeach ?>
        </ul>
    <?php endforeach ?>
</div>
<?php endif ?>

</section>
<!-- End of notifications -->
