<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<?php
$_error = array();
if (isset($error)) {
    if ($error instanceof Exception) {
        $_error[] = $error->getMessage();
    } elseif ($error instanceof \Concrete\Core\Error\Error) {
        if ($error->has()) {
            $_error = $error->getList();
        }
    } else {
        $_error = $error;
    }
}
if (!empty($_error)) {
    ?>
	<div class="ccm-ui"  id="ccm-dashboard-result-message">
		<?php View::element('system_errors', array('format' => 'block', 'error' => $_error)); ?>
	</div>
	<?php
}
?>

<h4><?= t('Reset Password') ?></h4>
<div class="help-block"><?= t('Enter your new password below.') ?></div>
<div class="change-password">
	<form method="post" action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'change_password', $uHash) ?>">
		<div class="form-group">
			<label class="control-label form-label" for="uPassword"><?= t('New Password') ?></label>
			<input type="password" name="uPassword" id="uPassword" class="form-control" autocomplete="off"/>
		</div>
		<div class="form-group">
			<label class="control-label form-label" for="uPassword"><?= t('Confirm New Password') ?></label>
			<input type="password" name="uPasswordConfirm" id="uPasswordConfirm" class="form-control" autocomplete="off"/>
		</div>
		<div class="form-group">
			<button class="btn btn-primary"><?= t('Change password and sign in') ?></button>
		</div>
	</form>
</div>
