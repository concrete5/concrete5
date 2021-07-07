<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Registration\Profiles $controller
 * @var bool $publicProfiles
 * @var bool $gravatarFallback
 * @var bool $displayAccountMenu
 * @var string $gravatarMaxLevel
 * @var array $gravatarMaxLevels
 * @var string $gravatarImageSet
 * @var array $gravatarImageSets
 */
?>

<form method="POST" action="<?= $controller->action('update_profiles') ?>">
    <?php $token->output('update_profile') ?>

    <div class="form-group">
        <?= $form->label('public_profiles', t('Profile Options')) ?>
        <div class="form-check">
            <?= $form->checkbox('public_profiles', '1', $publicProfiles) ?>
            <label class="form-check-label" for="public_profiles"><?= t('Enable public profiles.') ?></label>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('display_account_menu', t('Account Menu')) ?>
        <div class="form-check">
            <?= $form->checkbox('display_account_menu', '1', $displayAccountMenu) ?>
            <label class="form-check-label" for="display_account_menu">
                <?= t('Show the account menu when logged in.') ?><br />
                <small class="text-muted"><?= t('Site themes may override this value.') ?></small>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?= $form->label('gravatar_fallback', t('Fall Back To Gravatar')) ?>
        <div class="form-check">
            <?= $form->checkbox('gravatar_fallback', 1, $gravatarFallback) ?>
            <label class="form-check-label" for="gravatar_fallback"><?= t('Use image from <a href="https://gravatar.com" target="_blank">gravatar.com</a> if the user has not uploaded one.') ?></label>
        </div>
    </div>

    <div class="form-group gravatar-options">
        <?= $form->label('gravatar_max_level', t('Maximum Gravatar Rating')) ?>
        <?= $form->select('gravatar_max_level', $gravatarMaxLevels, $gravatarMaxLevel) ?>
    </div>

    <div class="form-group gravatar-options">
    	<?= $form->label('gravatarImageSet', t('Gravatar Image Set')) ?>
        <?= $form->select('gravatar_image_set', $gravatarImageSets, $gravatarImageSet) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?=t('Save')?></button>
        </div>
    </div>
</form>

<script>
$(document).ready(function() {
    function gravatarUpdated() {
        $('.gravatar-options').toggleClass('d-none', $('#gravatar_fallback').is(':checked') ? false : true);
    }
    $('#gravatar_fallback').on('change', function() {
        gravatarUpdated();
    });

    gravatarUpdated();
});
</script>
