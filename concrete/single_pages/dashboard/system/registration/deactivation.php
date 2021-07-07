<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Deactivation $controller
 * @var string $inactiveMessage
 * @var bool $enableAutomaticUserDeactivation
 * @var int $userDeactivationDays
 * @var bool $enableLogoutDeactivation
 * @var int $userLoginAmount
 * @var int $userLoginDuration
 */
?>
<form method="POST" action="<?= $controller->action('update') ?>">
    <?php $token->output('update') ?>

    <div class="form-group">
        <?= $form->label('inactiveMessage', t('Inactive User Error Message')) ?>
        <?= $form->textarea('inactiveMessage', $inactiveMessage, ['rows' => 4, 'cols' => 10]) ?>
        <small class="text-muted"><?= t('This message will be shown to inactive users when they attempt to login.') ?></small>
    </div>

    <div class="form-group">
        <?= $form->label('', t('Automatic User Deactivation')) ?>
        <div class="form-check">
            <?= $form->checkbox('enableAutomaticUserDeactivation', '1', $enableAutomaticUserDeactivation) ?>
            <label class="form-check-label" for="enableAutomaticUserDeactivation"><?= t('Automatically deactivate users when they have not logged in for awhile. Users will need to be manually reactivated.') ?></label>
        </div>
        <div class="card card-body bg-light pb-0" data-group="user-deactivation-days">
            <div class="form-group">
                <?= $form->label('userDeactivationDays', t('Threshold')) ?>
                <div class="row row-cols-auto g-0 align-items-center">
                    <?= t(
                        'Deactivate users when they have not logged in for %s days',
                        $form->number('userDeactivationDays', $userDeactivationDays, ['style' => 'width: 5rem', 'min' => '1', 'class' => 'form-control-sm ms-1 me-1'])
                    ) ?>
                </div>
                <small class="text-muted">
                    <?= t('Note: You will need to run the "Deactivate Users" job in order for these deactivations to actually occur.') ?>
                </small>
            </div>
        </div>
        <div class="form-check">
            <?= $form->checkbox('enableLogoutDeactivation', '1', $enableLogoutDeactivation) ?>
            <label class="form-check-label" for="enableLogoutDeactivation"><?= t('Automatically deactivate users after failed login attempts.') ?></label>
        </div>
        <div class="card card-body bg-light pb-0" data-group="user-deactivation-logins">
            <div class="form-group">
                <?= $form->label('userLoginAmount', t('Threshold')) ?>
                <div class="row row-cols-auto g-0 align-items-center">
                    <?= t(
                        'Deactivate users once they have failed %s login(s) within %s seconds',
                        $form->number('userLoginAmount', $userLoginAmount, ['style' => 'width: 5rem', 'min' => '1', 'class' => 'form-control-sm ms-1 me-1']),
                        $form->number('userLoginDuration', $userLoginDuration, ['style' => 'width: 5rem', 'min' => '1', 'class' => 'form-control-sm ms-1 me-1'])
                    ) ?>
                </div>
                <small class="text-muted">
                    <?= t('Note: Any person can attempt to log into any account, causing account deactivation') ?>
                </small>
            </div>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button type="submit" class="btn btn-primary float-end"><?= t('Save') ?></button>
            </div>
        </div>

    </div>
</form>

<script type="text/javascript">
    $('input[name=enableAutomaticUserDeactivation]').on('change', function() {
        if ($(this).is(':checked')) {
            $('div[data-group=user-deactivation-days]').show();
        } else {
            $('div[data-group=user-deactivation-days]').hide();
        }
    }).trigger('change');
    $('input[name=enableLogoutDeactivation]').on('change', function() {
        if ($(this).is(':checked')) {
            $('div[data-group=user-deactivation-logins]').show();
        } else {
            $('div[data-group=user-deactivation-logins]').hide();
        }
    }).trigger('change');
</script>
