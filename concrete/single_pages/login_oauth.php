<?php defined('C5_EXECUTE') or die('Access denied.');

$activeAuths = AuthenticationType::getList(true, true);
$form = Loader::helper('form');
?>
<style>
.authForm .row {
	margin-left:0;
}
.authForm .actions {
	margin-left:20px;
}
</style>
<div class='row'>
	<div class='span10 offset1'>
		<div class="page-header">
			<h1><?=t('Sign in to %s', tc('SiteName', \Core::make('site')->getSite()->getSiteName()))?></h1>
		</div>
		<?php
        if (count($activeAuths) > 1) {
            ?>
			<ul class="nav nav-tabs">
				<?php
                $first = true;
            foreach ($activeAuths as $auth) {
                ?>
					<li<?=$first ? " class='active'" : ''?>>
						<a data-authType='<?=$auth->getAuthenticationTypeHandle()?>' href='#<?=$auth->getAuthenticationTypeHandle()?>'><?=$auth->getAuthenticationTypeDisplayName()?></a>
					</li>
					<?php
                    $first = false;
            }
            ?>
			</ul>
			<?php

        }
        ?>
		<div class='authTypes row'>
			<?php
            $first = true;
            foreach ($activeAuths as $auth) {
                ?>
				<div data-authType='<?=$auth->getAuthenticationTypeHandle()?>' style='<?=$first ? "display:block" : "display:none"?>'>
					<fieldset>
						<form method='post' class='form-horizontal' action='<?=$view->action('authenticate', $auth->getAuthenticationTypeHandle())?>'>
							<div class='authForm'>
								<?php $auth->renderForm() ?>
							</div>
						</form>
					</fieldset>
				</div>
				<?php
                $first = false;
            }
            ?>
		</div>
		<div class='forgotPassword'>
			<h2><?=t('Forgot Your Password?')?></h2>
			<div class='help-block'>
				<?=t('Enter your email address below. We will send you instructions to reset your password.')?>
			</div>
			<form method="post" action="<?=$view->url('/login', 'forgot_password')?>" class="form-horizontal">
				<div class='control-group'>
					<label class='control-label form-label' for='uEmail'><?=t('Email Address')?></label>
					<div class='controls'>
						<?=$form->text('uEmail')?>
					</div>
				</div>
				<div class='actions'>
					<?=$form->button('resetPassword', 'Reset and Email Password')?>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
(function($){
	"use strict";
	$('ul.nav.nav-tabs > li > a').on('click',function(){
		var me = $(this);
		if (me.parent().hasClass('active')) return false;
		$('ul.nav.nav-tabs > li.active').removeClass('active');
		var at = me.attr('data-authType');
		me.parent().addClass('active');
		$('div.authTypes > div').hide().filter('[data-authType="'+at+'"]').show();
		return false;
	});
})(jQuery);
</script>
