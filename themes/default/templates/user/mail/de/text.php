<?php echo __('invitation_h1'), ' ', htmlspecialchars($record->name) ?>


<?php echo $this->textonly(__('invitation_copy', null, null, 'textile')) ?>


<?php echo __('invitation_linktext_login') ?>: <?php echo $this->url('/login') ?>


<?php echo __('user_label_email') ?>: <?php echo htmlspecialchars($record->email) ?>

<?php echo __('user_label_name') ?>: <?php echo htmlspecialchars($record->name) ?>

<?php echo __('user_label_pw') ?>: <?php echo htmlspecialchars($_SESSION['user']['pw_once']) ?>


<?php echo $this->textonly(__('invitation_footer', null, null, 'textile')) ?>