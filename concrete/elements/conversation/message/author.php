<?php
defined('C5_EXECUTE') or die("Access Denied.");
$u = Core::make(Concrete\Core\User\User::class);
?>
<?php if ($u->isRegistered()) {
    $ui = $u->getUserInfoObject();
    ?>
	<div class="ccm-conversation-avatar"><?=$ui->getUserAvatar()->output()?></div>
<?php 
} else {
    // non-logged-in posting. ?>
	<div class="form-group">
		<label class="control-label form-label" for="cnvMessageAuthorName"><?=t('Full Name')?></label>
		<input type="text" class="form-control" name="cnvMessageAuthorName" />
	</div>
	<div class="form-group">
		<label class="control-label form-label" for="cnvMessageAuthorEmail"><?=t('Email Address')?></label>
		<input type="text" class="form-control" name="cnvMessageAuthorEmail" />
	</div>
    <div class="form-group">
        <label class="control-label form-label" for="cnvMessageAuthorWebsite"><?=t('Website')?></label>
        <input type="text" class="form-control" name="cnvMessageAuthorWebsite" />
    </div>
	<?php
    $captcha = Core::make('captcha');
    ?>
	<div class="form-group">
		<?php $captcha->label()?>
		<?php $captcha->showInput();
    ?>
		<?php $captcha->display();
    ?>
	</div>
<?php 
} ?>
