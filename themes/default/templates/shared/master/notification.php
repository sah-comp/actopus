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
<?php if ( ! isset($user) || ! is_a($user, 'RedBean_OODBBean')) return ?>
<?php if ( ! $_notifications = $user->notifications()) return ?>
<!-- Start of notifications -->
<section
    id="notifications"
    class="notifications">
<?php foreach ($_notifications as $_id => $_notification): ?>
    <div class="alert alert-<?php echo $_notification->template ?>">
        <?php echo $this->textile($_notification->payload) ?>
    </div>
<?php endforeach ?>
</section>
<!-- End of notifications -->
