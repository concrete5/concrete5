<?php
use Concrete\Core\User\UserInfoRepository;

defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var bool $emailEnabled */
/* @var string $mailRecipient [optional] */
/* @var int $numEmails [optional] */

if (!isset($mailRecipient)) {
    $mailRecipient = '';
    $me = Core::make(Concrete\Core\User\User::class);
    if ($me->isRegistered()) {
        $myInfo = Core::make(UserInfoRepository::class)->getByID($me->getUserID());
        if ($myInfo !== null) {
            $mailRecipient = $myInfo->getUserEmail();
        }
    }
}
if (!isset($numEmails)) {
    $numEmails = 1;
}
?>

<form method="post" action="<?= $view->action('do_test') ?>" id="mail-settings-test-form">
    <?php
    if (!$emailEnabled) {
        ?>
        <div class="alert alert-info">
            <?= t(/*i18n: %1$s is a configuration name, %2$s is a configuration value*/'It\'s not possible to test the settings since the mail system is disabled (the setting %1$s is set to %2$s in the configuration).', '<b>concrete.email.enabled</b>', '<b>false</b>') ?>
        </div>
        <?php
    } else {
        ?>
        <?php $token->output('test') ?>
        <div class="form-group">
            <?= $form->label('mailRecipient', t('Recipient email address')) ?>
            <?= $form->email('mailRecipient', $mailRecipient, ['required' => 'required']) ?>
        </div>
        <div class="form-group">
            <?= $form->label('numEmails', t('Number of messages to send')) ?>
            <?= $form->number('numEmails', $numEmails, ['required' => 'required', 'min' => 1]) ?>
        </div>
        <?php
    }
    ?>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= URL::to('/dashboard/system/mail/method') ?>" class="btn btn-secondary float-start"><?= t('Change Settings') ?></a>
            <?php
            if ($emailEnabled) {
                echo $interface->submit(t('Send'), 'mail-settings-test-form', 'right', 'btn-primary');
            }
            ?>
        </div>
    </div>
</form>
