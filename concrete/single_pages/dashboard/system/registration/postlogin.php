<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Form\Service\Widget\PageSelector $pageSelector
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Postlogin $controller
 * @var string $loginRedirect
 * @var int|null $loginRedirectCID
 */
?>
<form method="POST" action="<?= $controller->action('update_login_redirect') ?>">
    <?php $token->output('update_login_redirect') ?>

    <div class="form-group">
        <?= $form->label('', t('After login')) ?>
        <div class="form-check">
            <?= $form->radio('login_redirect', 'HOMEPAGE', $loginRedirect, ['id' => 'login_redirect_HOMEPAGE']) ?>
            <label class="form-check-label" for="login_redirect_HOMEPAGE"><?= t('Redirect to Home') ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('login_redirect', 'DESKTOP', $loginRedirect, ['id' => 'login_redirect_DESKTOP']) ?>
            <label class="form-check-label" for="login_redirect_DESKTOP"><?= t("Redirect to user's Desktop") ?></label>
        </div>
        <div class="form-check">
            <?= $form->radio('login_redirect', 'CUSTOM', $loginRedirect, ['id' => 'login_redirect_CUSTOM']) ?>
            <label class="form-check-label" for="login_redirect_CUSTOM"><?= t('Redirect to a specific page') ?></label>
            <div id="login_redirect_cid_wrap"<?= $loginRedirect === 'CUSTOM' ? '' : ' class="d-none"' ?>>
                <?= $pageSelector->selectPage('login_redirect_cid', $loginRedirectCID, ['askIncludeSystemPages' => true]) ?>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>

</form>
<script>
$(document).ready(function() {
    function loginRedirectUpdated() {
        var loginRedirect = $('input[name=login_redirect]:checked').val();
        $('#login_redirect_cid_wrap').toggleClass('d-none', loginRedirect !== 'CUSTOM');
    }

    $('input[name=login_redirect]').on('change', function() {
        loginRedirectUpdated();
    });

    loginRedirectUpdated();
});
</script>
