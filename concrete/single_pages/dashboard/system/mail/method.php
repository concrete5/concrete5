<?php
defined('C5_EXECUTE') or die('Access Denied.');

// Arguments
/* @var Concrete\Core\Config\Repository\Repository $config */
/* @var \Concrete\Core\Form\Service\Form $form */

$enabledVals = ['0' => t('No'), '1' => t('Yes')];
$secureVals = ['' => t('None'), 'SSL' => tc('Encryption', 'SSL'), 'TLS' => tc('Encryption', 'TLS')];
?>

<form method="post" action="<?= $view->action('save_settings') ?>" id="mail-settings-form">
    <?= $form->getAutocompletionDisabler() ?>
    <?php $token->output('save_settings') ?>

    <fieldset>
        <div class="form-group">
            <?= $form->label('EMAIL_ENABLED', t('Email Sending')) ?>
            <div class="form-check">
                <?= $form->radio('EMAIL_ENABLED', false, $config->get('concrete.email.enabled'), ["class" => "form-check-input", "id" => "EMAIL_ENABLED1"]) ?>
                <?= $form->label('EMAIL_ENABLED1', t('Disabled'), ["form-check-label"]);?>
            </div>
            <div class="form-check">
                <?= $form->radio('EMAIL_ENABLED', true, $config->get('concrete.email.enabled'), ["class" => "form-check-input", "id" => "EMAIL_ENABLED2"]) ?>
                <?= $form->label('EMAIL_ENABLED2', t('Enabled'), ["form-check-label"]);?>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD', t('Email Sending Method')) ?>
            <div class="form-check">
                <?= $form->radio('MAIL_SEND_METHOD', 'PHP_MAIL', strtoupper($config->get('concrete.mail.method')), ["class" => "form-check-input", "id" => "MAIL_SEND_METHOD1"]) ?>
                <?= $form->label('MAIL_SEND_METHOD1', t('Default PHP Mail Function'), ["form-check-label"]);?>
            </div>
            <div class="form-check">
                <?= $form->radio('MAIL_SEND_METHOD', 'SMTP', strtoupper($config->get('concrete.mail.method')), ["class" => "form-check-input", "id" => "MAIL_SEND_METHOD2"]) ?>
                <?= $form->label('MAIL_SEND_METHOD2', t('External SMTP Server'), ["form-check-label"]);?>
            </div>
        </div>
    </fieldset>

    <fieldset id="ccm-settings-mail-smtp"<?= strtoupper($config->get('concrete.mail.method')) === 'SMTP' ? '' : ' style="display:none"' ?>>
        <legend><?= t('SMTP Settings') ?></legend>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_SERVER', t('Mail Server')) ?>
            <?= $form->text('MAIL_SEND_METHOD_SMTP_SERVER', $config->get('concrete.mail.methods.smtp.server')) ?>
        </div>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_USERNAME', t('Username')) ?>
            <?= $form->text('MAIL_SEND_METHOD_SMTP_USERNAME', $config->get('concrete.mail.methods.smtp.username')) ?>
        </div>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_PASSWORD', t('Password')) ?>
            <?= $form->password('MAIL_SEND_METHOD_SMTP_PASSWORD', $config->get('concrete.mail.methods.smtp.password'), ['autocomplete' => 'off']) ?>
        </div>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_ENCRYPTION', t('Encryption')) ?>
            <?= $form->select('MAIL_SEND_METHOD_SMTP_ENCRYPTION', $secureVals, $config->get('concrete.mail.methods.smtp.encryption')) ?>
        </div>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_PORT', t('Port (Leave blank for default)')) ?>
            <?= $form->number('MAIL_SEND_METHOD_SMTP_PORT', $config->get('concrete.mail.methods.smtp.port'), ['min' => 1, 'max' => 65535]) ?>
        </div>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_HELO_DOMAIN', t(/*i18n: %1$s is HELO, %2$s is localhost*/'%1$s domain (Leave blank for %2$s)', 'HELO', 'localhost')) ?>
            <?= $form->text('MAIL_SEND_METHOD_SMTP_HELO_DOMAIN', $config->get('concrete.mail.methods.smtp.helo_domain')) ?>
        </div>

        <div class="form-group">
            <?= $form->label('MAIL_SEND_METHOD_SMTP_MESSAGES_PER_CONNECTION', t('Messages per connection'), ['class' => 'launch-tooltip form-label', 'title' => t('Sending multiple messages per connection can speed up sending many emails at once, but this feature must be supported by the SMTP server')]) ?>
            <?= $form->number('MAIL_SEND_METHOD_SMTP_MESSAGES_PER_CONNECTION', $config->get('concrete.mail.methods.smtp.messages_per_connection') ?: '', ['min' => 1, 'placeholder' => t('Leave empty for unlimited messages per connection')]) ?>
        </div>

    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= $this->action('test') ?>" class="btn btn-secondary float-start"><?= t('Test Settings') ?></a>
            <?= $interface->submit(t('Save'), 'mail-settings-form', 'right', 'btn-primary') ?>
        </div>
    </div>
</form>

<script type="text/javascript">
$('input[name=MAIL_SEND_METHOD]')
    .change(function() {
        if ($('input[name="MAIL_SEND_METHOD"]:checked').val() === 'SMTP') {
            $('#ccm-settings-mail-smtp').show();
        } else {
            $('#ccm-settings-mail-smtp').hide();
        }
    });
</script>
