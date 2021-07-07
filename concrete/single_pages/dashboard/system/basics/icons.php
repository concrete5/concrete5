<?php

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */

/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Core\Application\Service\FileManager $concrete_asset_library */
/* @var Concrete\Core\Validation\CSRF\Token $validation_token */
/* @var Concrete\Core\Form\Service\Widget\Color $form_color */

/* @var Concrete\Core\Entity\File\File|null $favicon */
/* @var Concrete\Core\Entity\File\File|null $iosHome */
/* @var Concrete\Core\Entity\File\File|null $modernThumb */
/* @var string $modernThumbBG */
/* @var string $browserToolbarColor */
?>
<form method="POST" class="form-horizontal" id="favicon-form" action="<?= $view->action('update_icons') ?>" >
    <?= $validation_token->output('update_icons') ?>
    <fieldset>
        <legend><?= t('Favicon') ?></legend>
            <div class="help-block"><?= t('Your image should be 16x16 pixels, and should be a gif or a png with a .ico file extension.') ?></div>
            <div class="form-group">
                <?= $concrete_asset_library->image('ccm-favicon-file', 'faviconFID', t('Choose File'), $favicon, ['filters' => [['field' => 'extension', 'extension' => 'ico']]]) ?>
            </div>
    </fieldset>

    <fieldset>
        <legend><?= t('iPhone Thumbnail') ?></legend>
        <div class="help-block"><?= t('iPhone home screen icons should be 57x57 and be in the .png format.') ?></div>
        <div class="form-group">
            <?= $concrete_asset_library->image('ccm-iphone-file', 'iosHomeFID', t('Choose File'), $iosHome, ['filters' => [['field' => 'extension', 'extension' => 'png']]]) ?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Windows 8 Thumbnail') ?></legend>
        <div class="help-block"><?= t('Windows 8 start screen tiles should be 144x144 and be in the .png format.') ?></div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('File') ?></label>
            <?= $concrete_asset_library->image('ccm-modern-file', 'modernThumbFID', t('Choose File'), $modernThumb, ['filters' => [['field' => 'extension', 'extension' => 'png']]]) ?>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Background Color') ?></label>
            <div><?= $form_color->output('modernThumbBG', $modernThumbBG) ?></div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?= t('Browser Toolbar Color') ?></legend>
        <div class="help-block"><?= t('This value may be used by some browsers (for example Chrome and Opera on Android) to set the toolbar color.') ?></div>
        <div class="form-group">
            <label class="control-label form-label"><?= t('Color') ?></label>
            <div><?= $form_color->output('browserToolbarColor', $browserToolbarColor) ?></div>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-end btn btn-primary" type="submit" ><?= t('Save') ?></button>
        </div>
    </div>

</form>
