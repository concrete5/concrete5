<?php defined('C5_EXECUTE') or die('Access denied.');
$form = Loader::helper('form');
?>
<div class='forgotPassword'>
    <h2><?= t('Forgot Your Password?') ?></h2>

    <div class="ccm-message"><?= $intro_msg ?></div>
    <div class='help-block'>
        <?= t(
            'If there is an account associated with this email, instructions for resetting your password have been sent.') ?>
    </div>
    <a href="<?= \URL::to('/login') ?>" class="btn btn-block btn-primary">
        <?= t('Go Back') ?>
    </a>
</div>
