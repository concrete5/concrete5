<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Authentication $controller
 * @var Concrete\Core\Authentication\AuthenticationType $authenticationType
 */
?>
<form method="POST" action="<?= $controller->action('save', $authenticationType->getAuthenticationTypeID()) ?>">
    <?php $token->output("auth_type_save.{$authenticationType->getAuthenticationTypeID()}") ?>
    <?= $form->getAutocompletionDisabler() ?>
    <div class="form-group">
        <?= $form->label('', t('General options'))?>
        <div class="form-check">
            <?= $form->checkbox('authentication_type_enabled', '1', $authenticationType->isEnabled()) ?>
            <label class="form-check-label" for="authentication_type_enabled"><?= t('Enable authentication type') ?></label>
        </div>
    </div>
    <div id="authentication_type_form" class="mt-3<?= $authenticationType->isEnabled() ? '' : ' d-none' ?>">
        <?= $authenticationType->renderTypeForm() ?>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a class="btn btn-secondary pull-left" href="<?= $controller->action('') ?>"><?= t('Cancel') ?></a>
                <button type="submit" class="btn btn-primary"><?= t('Save') ?></button>
            </div>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    $('#authentication_type_enabled')
        .on('change', function() {
            $('#authentication_type_form').toggleClass('d-none', !$(this).is(':checked'));
        })
        .trigger('change')
    ;
});
</script>
