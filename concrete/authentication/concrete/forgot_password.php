<?php defined('C5_EXECUTE') or die('Access denied.'); ?>

<div class="forgotPassword">
	<form method="post" action="<?= URL::to('/login', 'callback', $authType->getAuthenticationTypeHandle(), 'forgot_password') ?>">
		<?php $token->output(); ?>
		<h4><?= t('Forgot Your Password?') ?></h4>
		<div class="ccm-message"><?= isset($intro_msg) ? $intro_msg : '' ?></div>
		<div class='help-block'>
			<?= t('Enter your email address below. We will send you instructions to reset your password.') ?>
		</div>
		<div class="form-group">
			<label class="control-label form-label" for="uEmail"><?= t('Email Address') ?></label>
			<input name="uEmail" type="email" id="uEmail" class="form-control" />
		</div>
        <div class="d-grid">
            <button name="resetPassword" class="btn btn-primary"><?= t('Reset and Email Password') ?></button>
        </div>
	</form>
</div>
