<!DOCTYPE html>
<html lang="de">
<head>
	<meta charset="utf-8" />
    <style type="text/css">
	</style>
	<title><?php echo __('invitation_head_title'), ' ', htmlspecialchars($record->name) ?></title>
</head>
<body>
    <h1><?php echo __('invitation_h1'), ' ', htmlspecialchars($record->name) ?></h1>
    <?php echo $this->textile(__('invitation_copy', null, null, 'textile')) ?>
    <p><a href="<?php echo $this->url('/login') ?>"><?php echo __('invitation_linktext_login') ?></a></p>
    <p>
    <?php echo __('user_label_email') ?>: <em><?php echo htmlspecialchars($record->email) ?></em><br />
    <?php echo __('user_label_name') ?>: <em><?php echo htmlspecialchars($record->name) ?></em><br />
    <?php echo __('user_label_pw') ?>: <em><?php echo htmlspecialchars($_SESSION['user']['pw_once']) ?></em><br />
    </p>
    <?php echo $this->textile(__('invitation_footer', null, null, 'textile')) ?>
</body>
</html>