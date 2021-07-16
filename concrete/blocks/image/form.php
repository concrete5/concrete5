<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;

/**
 * @var DestinationPicker $destinationPicker
 * @var string $sizingOption
 * @var array $themeResponsiveImageMap
 * @var array $thumbnailTypes
 * @var array $selectedThumbnailTypes
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
        <label class="control-label">
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
        <?php echo t('Sizing'); ?>
    </legend>

    <div class="form-group">
        <?php
        echo $form->label('sizingOption', t("Sizing Option"));
        echo $form->select('sizingOption', [
            "thumbnails_default" => t("Thumbnails - Default"),
            "thumbnails_configurable" => t("Thumbnails - Configurable"),
            "full_size" => t("Full Size"),
            "constrain_size" => t("Constrain Size")
        ], $sizingOption);
        ?>
    </div>

    <div data-fields="thumbnails-configurable" class="d-none">
        <?php if (count($thumbnailTypes) === 0) { ?>
            <div class="alert alert-warning">
                <?php echo t("Responsive breakpoints are not defined in your theme. To use Thumbnails please define Breakpoints in your theme settings."); ?>
            </div>
        <?php } ?>

        <?php foreach($themeResponsiveImageMap as $breakpointHandle => $minScreenWidth) { ?>
            <div class="form-group">
                <?php echo $form->label('selectedThumbnailTypes['  . $breakpointHandle . ']', t("%s Screen Width", ucfirst(camelcase(str_replace("_", " ", $breakpointHandle))))); ?>

                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">
                            <?php echo sprintf('> %s', $minScreenWidth); ?>
                        </span>
                    </div>

                    <?php echo $form->select('selectedThumbnailTypes['  . $breakpointHandle . ']', $thumbnailTypes, $selectedThumbnailTypes[$breakpointHandle]); ?>
                </div>
            </div>
        <?php } ?>

        <div class="form-group">
            <?php if (is_array($selectedThumbnailTypes)) { ?>
                <?php foreach(array_keys($selectedThumbnailTypes) as $breakpointHandle ) { ?>
                    <?php if (!in_array($breakpointHandle, array_keys($themeResponsiveImageMap))) { ?>
                        <div class="alert alert-info">
                            <?php echo t("This block contains %s breakpoint that is not included in your theme.", ucfirst(camelcase(str_replace("_", " ", $breakpointHandle)))); ?>
                        </div>
                    <?php } ?>
                <?php }?>
            <?php }?>

            <div class="alert alert-info">
                <?php echo t("Thumbnail types can be managed in Dashboard > System > Files > Thumbnails."); ?>
            </div>
        </div>
    </div>

    <div data-fields="constrain-image" class="d-none">
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

                    <div class="input-group-append">
                        <span class="input-group-text">
                            <?php echo t('px'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('maxHeight', t('Max Height')); ?>

                <div class="input-group">
                    <?php echo $form->number('maxHeight', isset($maxHeight) ? $maxHeight : '', ['min' => 0]); ?>

                    <div class="input-group-append">
                        <span class="input-group-text">
                            <?php echo t('px'); ?>
                        </span>
                    </div>
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

        $('#sizingOption').on('change', function () {
            $('div[data-fields=thumbnails-configurable]').addClass("d-none");
            $('div[data-fields=constrain-image]').addClass("d-none");

            switch($('#sizingOption option:selected').val()) {
                case "thumbnails_default":
                    break;

                case "thumbnails_configurable":
                    $('div[data-fields=thumbnails-configurable]').removeClass("d-none");
                    break;

                case "full_size":
                    break;

                case "constrain_size":
                    $('div[data-fields=constrain-image]').removeClass("d-none");
                    break;
            }

        }).trigger('change');
    });
</script>
