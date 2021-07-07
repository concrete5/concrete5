<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\System\Multisite\Types $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Site\Type $type
 * @var Concrete\Core\Filesystem\Element|null $typeMenu
 * @var array $templates
 * @var array $themes
 */

if ($typeMenu !== null) {
    $typeMenu->render();
}
?>
<form method="post" action="<?= $controller->action($type->getSiteTypeID() === null ? 'create' : 'update') ?>">
    <?php
    $token->output('submit');
    if ($type->getSiteTypeID() !== null) {
        ?><input type="hidden" name="id" value="<?= $type->getSiteTypeID() ?>" />
        <?php
    }
    ?>
    <div class="form-group">
        <?= $form->label('handle', t('Handle')) ?>
        <?= $form->text('handle', $type->getSiteTypeHandle(), ['required' => 'required']) ?>
    </div>
    <div class="form-group">
        <?= $form->label('name', t('Name')) ?>
        <?= $form->text('name', $type->getSiteTypeName(), ['required' => 'required']) ?>
    </div>
    <div class="form-group">
        <?= $form->label('theme', t('Theme')) ?>
        <?= $form->select('theme', $themes, $type->getSiteTypeThemeID() ?: '', ['required' => 'required']) ?>
    </div>
    <div class="form-group">
        <?= $form->label('template', t('Template for Home Page')) ?>
        <?= $form->select('template', $templates, $type->getSiteTypeHomePageTemplateID() ?: '', ['required' => 'required']) ?>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <div class="float-end">
                <a class="btn btn-secondary" href="<?= $type->getSiteTypeID() === null ? $controller->action() : $controller->action('view_type', $type->getSiteTypeID()) ?>"><?= t('Cancel') ?></a>
                <button class="btn btn-primary" type="submit"><?= $type->getSiteTypeID() === null ? t('Create Site Type') : t('Save') ?></button>
            </div>
        </div>
    </div>
</form>
