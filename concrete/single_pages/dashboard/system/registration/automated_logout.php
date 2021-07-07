<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\AutomatedLogout $controller
 * @var Concrete\Core\Url\UrlImmutable $trustedProxyUrl
 * @var bool $invalidateOnIPMismatch
 * @var bool $invalidateOnUserAgentMismatch
 * @var bool $invalidateInactiveUsers
 * @var int|null $inactiveTime
 * @var string $confirmInvalidateString
 */
?>
<form method="POST" action="<?= $controller->action('save') ?>">
    <?php $token->output('save_automated_logout') ?>

    <div class="form-group">
        <?= $form->label('', t('Session Security')) ?>
        <div class="alert alert-info">
            <?= t('These settings help prevent a user from stealing other logged in user sessions. You may want to configure %s"Trusted Proxies"%s instead', '<a href="' . $trustedProxyUrl . '">', '</a>') ?>
        </div>
        <div class="form-check">
            <?= $form->checkbox('invalidateOnIPMismatch', '1', $invalidateOnIPMismatch) ?>
            <label class="form-check-label" for="invalidateOnIPMismatch"><?= t('Log users out if their IP changes') ?></label>
        </div>
        <div class="form-check">
            <?= $form->checkbox('invalidateOnUserAgentMismatch', '1', $invalidateOnUserAgentMismatch) ?>
            <label class="form-check-label" for="invalidateOnUserAgentMismatch"><?= t("Log users out if their browser's user agent changes") ?></label>
        </div>
        <div class="form-check">
            <?= $form->checkbox('invalidateInactiveUsers', '1', $invalidateInactiveUsers) ?>
            <label class="form-check-label" for="invalidateInactiveUsers">
                <span class="row row-cols-auto g-0 align-items-center">
                    <?= t(
    'Automatically log out users who are inactive for %s seconds or more.',
    $form->number('inactiveTime', $inactiveTime, ['style' => 'width: 5rem', 'min' => '15', 'class' => 'form-control-sm ms-1 me-1'] + ($invalidateInactiveUsers ? [] : ['disabled' => 'disabled']))
) ?>
                </span>
            </label>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a href="javascript:void(0)" class="btn btn-danger" id="invalidate-sessions-button"><?= t('Log out all active users') ?></a>
                <button class="btn btn-primary" type="submit"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>

<form method="POST" id="invalidate-sessions-form" action="<?= $controller->action('invalidate_sessions') ?>">
    <?php $token->output('invalidate_sessions') ?>

    <div class="form-group">
        <?= $form->label('', t('Invalidate Active Sessions')) ?>
        <?= $form->text('confirmation', '', ['autocomplete' => 'off']) ?>
        <small class="text-muted">
            <?= t('Type %s in the above box to proceed.', "<code>{$confirmInvalidateString}</code>") ?>
        </small>
    </div>

</form>


<script>
$(document).ready(function() {

    $('#invalidateInactiveUsers')
        .on('change', function() {
            $('#inactiveTime').attr('disabled', $(this).is(':checked') ? null : 'disabled');
        })
        .trigger('change')
    ;
    var $confirmation = $('#confirmation')
        $form = $('#invalidate-sessions-form');

    $form.on('submit', function(e) {
        e.preventDefault();
        return false;
    });
    function isConfirmed() {
        return $confirmation.val().toLowerCase() === <?= json_encode($confirmInvalidateString)?>.toLowerCase();
    }

    $confirmation.on('focus input', function() {
        var ok = isConfirmed();
        $confirmation.toggleClass('is-valid', ok).toggleClass('is-invalid', !ok);
        
    });

    $('#invalidate-sessions-button').on('click', function(e) {
        e.preventDefault();
        if (!isConfirmed()) {
            $confirmation.select().focus();
            return;
        }
        ConcreteAlert.confirm(
            <?= json_encode('<strong>' . t('Warning:') . '</strong> ' . t('This action will automatically log out all users (including yourself!)')) ?>,
            function() {
                $form.off('submit').submit();
            },
            'btn-danger',
            <?= json_encode(t('Log out')) ?>
        );
    });
});
</script>
