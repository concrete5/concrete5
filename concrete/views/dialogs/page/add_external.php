<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Dialog\Page\AddExternal|Concrete\Controller\Dialog\Page\EditExternal $controller */
/* @var Concrete\Core\View\DialogView $view */

/* @var Concrete\Core\Form\Service\Form $form */
/* @var string $name */
/* @var string $link */
/* @var bool $openInNewWindow */
/* @var bool $isEditingExisting */
?>

<div class="ccm-ui">

    <form class="form-stacked" data-dialog-form="add-external-link" method="post"
          action="<?php echo $controller->action('submit') ?>">

        <div class="form-group">
            <?php echo $form->label('name', t('Name')) ?>
            <?php echo $form->text('name', $name, ['autofocus' => 'autofocus']) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('link', t('URL')) ?>
            <?php echo $form->text('link', $link) ?>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox('openInNewWindow', '1', $openInNewWindow) ?>
                <?php echo $form->label('openInNewWindow', t('Open Link in New Window'), ["class" => "form-check-label"]) ?>
            </div>
        </div>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel"><?php echo t('Cancel') ?></button>
            <button type="button" data-dialog-action="submit"
                    class="btn btn-primary float-end"><?php echo $isEditingExisting ? t('Save') : t('Add') ?></button>
        </div>
    </form>

</div>
