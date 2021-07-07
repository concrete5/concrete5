<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<div class='forgotPassword'>
    <?php if($validated || $workflowPending) : ?>
        <h4><?= t('Email Validated') ?></h4>

        <div class='help-block'>
            <?= $workflowPending ? t('The email address <b>%s</b> has been verified. Your account is currently pending for activation. After your account has been activated, you become a member of this website and are able to login.', $uEmail) : t('This email address has been validated! You may now access the features of this site.')?>
        </div>
        <div class="d-grid">
            <a href="<?= \URL::to('/') ?>" class="btn btn-primary">
                <?= t('Continue') ?>
            </a>
        </div>
    <?php endif; ?>
</div>
