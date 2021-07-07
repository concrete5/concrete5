<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;

/**
 * @var DestinationPicker $destinationPicker
 * @var array $imageLinkPickers
 * @var string $imageLinkHandle
 * @var mixed $imageLinkValue
 * @var int $constrainImage
 * @var File|null $bfo
 */

$app = Application::getFacadeApplication();
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);
?>

<fieldset>
    <legend>
        <?php echo t('Files'); ?>
    </legend>

    <div class="form-group">
        <?php
        echo $form->label('ccm-b-image', t('Image'));
        echo $fileManager->image('ccm-b-image', 'fID', t('Choose Image'), $bf);
        ?>
    </div>

    <div class="form-group">
        <label class="control-label form-label">
            <?php echo t('Image Hover') ?>

            <small style="color: #999999; font-weight: 200;">
                <?php echo t('(Optional)'); ?>
            </small>
        </label>

        <i class="fa fa-question-circle launch-tooltip" title=""
           data-original-title="<?php echo t('The image hover effect requires constraining the image size.'); ?>"></i>

        <?php echo $fileManager->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('HTML'); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('imageLink', t('Image Link')) ?>
        <?php echo $destinationPicker->generate(
            'imageLink',
            $imageLinkPickers,
            $imageLinkHandle,
            $imageLinkValue
        )
        ?>
    </div>

    <div id="imageLinkOpenInNewWindow" style="display: none;" class="form-group">
        <div class="form-check">
            <?php
            echo $form->checkbox('openLinkInNewWindow', 'openLinkInNewWindow', isset($openLinkInNewWindow) ? $openLinkInNewWindow : false);
            echo $form->label("openLinkInNewWindow", t('Open link in new window'), ["class" => "form-check-label"]);
            ?>
        </div>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('altText', t('Alt Text'));
        echo $form->text('altText', isset($altText) ? $altText : '', ['maxlength' => 255]);
        ?>
    </div>

    <div class="form-group">
        <?php
        echo $form->label('title', t('Title'));
        echo $form->text('title', isset($title) ? $title : '', ['maxlength' => 255]);
        ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Resize Image'); ?>
    </legend>

    <div class="form-group">
        <div class="form-check" data-checkbox-wrapper="constrain-image">
            <?php
            echo $form->checkbox('constrainImage', 1, $constrainImage);
            echo $form->label('constrainImage', t("Constrain Image Size"), ["class" => "form-check-label"]);
            ?>
        </div>
    </div>

    <div data-fields="constrain-image" style="display: none">
        <div class="well">
            <div class="form-group">
                <div class="form-check">
                    <?php echo $form->checkbox('cropImage', 1, isset($cropImage) ? $cropImage : false); ?>
                    <?php echo $form->label('cropImage', t("Crop Image"), ["class" => "form-check-label"]); ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('maxWidth', t('Max Width')); ?>

                <div class="input-group">
                    <?php echo $form->number('maxWidth', isset($maxWidth) ? $maxWidth : '', ['min' => 0]); ?>

                    <span class="input-group-text">
                        <?php echo t('px'); ?>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('maxHeight', t('Max Height')); ?>

                <div class="input-group">
                    <?php echo $form->number('maxHeight', isset($maxHeight) ? $maxHeight : '', ['min' => 0]); ?>

                    <span class="input-group-text">
                        <?php echo t('px'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</fieldset>

<script>
    $(function () {
        $('#imageLink__which').change(function () {
            $('#imageLinkOpenInNewWindow').toggle($('#imageLink__which').val() !== 'none');
        }).trigger('change');

        $('#constrainImage').on('change', function () {
            $('div[data-fields=constrain-image]').toggle($(this).is(':checked'));

            if (!$(this).is(':checked')) {
                $('#cropImage').prop('checked', false);
                $('#maxWidth').val('');
                $('#maxHeight').val('');
            }
        }).trigger('change');
    });
</script>
