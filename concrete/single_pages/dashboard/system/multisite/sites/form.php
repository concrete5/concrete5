<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Sites $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Application\UserInterface\OptionsForm\OptionsForm $optionsForm
 * @var string $timezone
 * @var string[][] $timezones
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Site\Type $type
 */
?>
<form method="post" action="<?= $controller->action('submit') ?>">
    <?php $token->output('submit') ?>
    <input type="hidden" name="siteTypeID" value="<?= $type->getSiteTypeID() ?>" />
    <fieldset>
        <legend><?= t('Standard Details') ?></legend>
        <div class="form-group">
            <?= $form->label('handle', t('Handle')) ?>
            <?= $form->text('handle', '', ['required' => 'required']) ?>
        </div>
        <div class="form-group">
            <?= $form->label('name', t('Name')) ?>
            <?= $form->text('name', '', ['required' => 'required']) ?>
        </div>
        <div class="form-group">
            <?= $form->label('canonical_url', t('Canonical URL'), ['class' => 'launch-tooltip form-label', 'title' => t('The full URL at which this site will live. e.g. http://www.my-website.com')]) ?>
            <?= $form->text('canonical_url', '') ?>
        </div>
        <div class="form-group">
            <?= $form->label('timezone', t('Default Timezone'), ['class' => 'launch-tooltip form-label', 'data-bs-placement' => 'right', 'title' => t('This will control the default timezone that will be used to display date/times.')]) ?>
            <?= $form->select('timezone', $timezones, $timezone) ?>
        </div>
    </fieldset>

    <?php
    if ($optionsForm->formExists()) {
        ?>
        <fieldset>
            <legend><?= h(t('%s Options', $type->getSiteTypeName())) ?></legend>
            <?php $optionsForm->renderForm() ?>
        </fieldset>
        <?php
    }
    ?>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a class="btn btn-secondary" href="<?= $controller->action() ?>"><?= t('Cancel') ?></a>
                <button class="btn btn-primary" type="submit"><?= t('Add') ?></button>
            </div>
        </div>
    </div>
</form>
