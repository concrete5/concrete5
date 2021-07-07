<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\GlobalPasswordReset $controller
 * @var string $resetText
 * @var string $resetMessage
 * @var bool $disableForm
 */
$readonlyAttr = $disableForm ? ['readonly' => 'readonly'] : [];
?>
<form id="global-password-reset-form" method="POST" action="<?= $controller->action('reset_passwords') ?>">
    <?php $token->output('global_password_reset_token') ?>

    <div class="alert alert-info">
        <p>
            <?= t('Global Password Reset allows Administrators to force reset all user passwords.') ?>
            <?= t('The system signs out all users, resets their passwords and forces them to choose a new one.') ?>
        </p>
        <strong><?= t('Note:') ?></strong> <?= t('If you have overridden the standard concrete5 authentication system or you have created your own, you may not need to reset all passwords.') ?>
    </div>

    <div class="form-group">
        <?= $form->label('resetMessage', t('Edit message')) ?>
        <?= $form->textarea('resetMessage', $resetMessage, ['rows' => '4', 'required' => 'required'] + $readonlyAttr) ?>
        <small class="text-muted"><?= t('This message will be shown to users the next time they log in.') ?></small>
    </div>

    <div class="form-group">
        <?= $form->label('confirmation', t('Confirmation')) ?>
        <?= $form->text('confirmation', '', $readonlyAttr) ?>
        <small class="text-muted"><?= t('Type "%s" in the above box to proceed.', '<strong>' . h($resetText) . '</strong>') ?></small>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button type="submit" class="btn btn-danger float-end"><?= t('Reset all passwords') ?></button>
        </div>
    </div>

</form>

<script>
$(document).ready(function() {
    var $form = $('#global-password-reset-form');
    <?php
    if ($disableForm) {
        ?>
        $form.on('submit', function(e) {
            ConcreteAlert.error({
                message: <?= json_encode(t('Only the Super User is allowed to reset all passwords.')) ?>,
            });
            e.preventDefault();
        });
        <?php
    } else {
        ?>
        var $confirmation = $('input[name="confirmation"]');

        function isConfirmed() {
            return $confirmation.val().toLowerCase() === <?= json_encode($resetText)?>.toLowerCase();
        }

        $confirmation.on('input focus', function() {
            var ok = isConfirmed();
            $confirmation.toggleClass('is-valid', ok).toggleClass('is-invalid', !ok);
        });

        $form.on('submit', function(e) {
            e.preventDefault();
            if (!isConfirmed()) {
                $confirmation.select().focus();
                return;
            }
            ConcreteAlert.confirm(
                <?= json_encode('<strong>' . t('Warning:') . '</strong> ' . t('This action will automatically log out all users (including yourself) and reset their passwords.')) ?>,
                function() {
                    $form.off('submit').submit();
                },
                'btn-danger',
                <?= json_encode(t('Reset all passwords')) ?>
            );
            <?php
        }
        ?>
    });
});
</script>
