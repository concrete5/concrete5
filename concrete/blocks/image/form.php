<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Form\Service\DestinationPicker\DestinationPicker;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Theme\Concrete\PageTheme;

/**
 * @var DestinationPicker $destinationPicker
 * @var array $imageLinkPickers
 * @var string $imageLinkHandle
 * @var mixed $imageLinkValue
 * @var int $constrainImage
 * @var File|null $bfo
 * @var bool $openLinkInLightbox
 * @var bool $openLinkInNewWindow
 * @var array $thumbnails
 */

$app = Application::getFacadeApplication();
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);
/** @var Type $thumbnailTypeService */
$thumbnailTypeService = $app->make(Type::class);

$thumbnailTypes = [];

foreach ($thumbnailTypeService::getList() as $thumbnailTypeListItem) {
    if ($thumbnailTypeListItem->isAvailableInBlocks()) {
        $thumbnailTypes[$thumbnailTypeListItem->getID()] = $thumbnailTypeListItem->getDisplayName('text');
    }
}

$siteTheme = PageTheme::getSiteTheme();
$responsiveImageMap = $siteTheme->getThemeResponsiveImageMap();

?>

<fieldset>
    <legend>
        <?php echo t('Image'); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('ccm-b-image', t('Image')); ?>
        <?php echo $fileManager->image('ccm-b-image', 'fID', t('Choose Image'), $bf); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Behaviours'); ?>
    </legend>

    <div class="form-group">
        <div class="form-check">
            <?php
            echo $form->checkbox('changeImageOnHover', 'changeImageOnHover', isset($openLinkInNewWindow) ? $openLinkInNewWindow : false);
            echo $form->label("changeImageOnHover", t('Change Image on Hover'), ["class" => "form-check-label"]);
            ?>
        </div>
    </div>

    <div id="changeImageOnHoverContainer">
        <div class="form-group">
            <label class="control-label">
                <?php echo t('Image Hover') ?>
            </label>

            <i class="fa fa-question-circle launch-tooltip" title=""
               data-original-title="<?php echo t('The image hover effect requires constraining the image size.'); ?>"></i>

            <?php echo $fileManager->image('ccm-b-image-onstate', 'fOnstateID', t('Choose Image On-State'), $bfo); ?>
        </div>
    </div>

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

    <div id="openLinkContainer" class="form-group">
        <div class="form-check">
            <?php
            echo $form->checkbox('openLinkInLightbox', 'openLinkInLightbox', isset($openLinkInLightbox) ? $openLinkInLightbox : false);
            echo $form->label("openLinkInLightbox", t('Open link in Lightbox'), ["class" => "form-check-label"]);
            ?>
        </div>

        <div class="form-check">
            <?php
            echo $form->checkbox('openLinkInNewWindow', 'openLinkInNewWindow', isset($openLinkInNewWindow) ? $openLinkInNewWindow : false);
            echo $form->label("openLinkInNewWindow", t('Open link in new window'), ["class" => "form-check-label"]);
            ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Sizing'); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('constrainImageSize', t('Constrain Image Size')); ?>
        <?php echo $form->select('constrainImageSize', [
            "full-size" => t("Show full-sized image"),
            "thumbnails" => t("Thumbnails"),
            "custom-size" => t("Custom Size")
        ]); ?>
    </div>

    <div id="fullSizeNotice" class="form-group">
        <p class="help-block">
            <?php echo t("Note: themes may determine how large images can display, even if the image is not constrained."); ?>
        </p>
    </div>

    <?php echo $form->hidden('constrainImage'); ?>

    <div id="thumbnails">
        <?php if (count($responsiveImageMap) === 0) { ?>
            <div class="alert alert-warning">
                <?php echo t("Responsive Breakpoints are not defined in your theme. To use Thumbnails please define Breakpoints in your theme settings."); ?>
            </div>
        <?php } else if (count($thumbnailTypes) === 0) { ?>
            <div class="alert alert-warning">
                <?php echo t("You don't have any thumbnail types. Thumbnail types can be managed in Dashboard > System > Files > Thumbnails."); ?>
            </div>
        <?php } else { ?>
            <?php foreach ($responsiveImageMap as $breakpointHandle => $breakpointSize) { ?>
                <div class="form-group">
                    <?php echo $form->label('thumbnails_' . $breakpointHandle, t('Thumbnail type for Breakpoint %s', $breakpointHandle)); ?>
                    <?php echo $form->select(
                        'thumbnails[]',
                        $thumbnailTypes,
                        isset($thumbnails[$breakpointHandle]) ? $thumbnails[$breakpointHandle] : null,
                        [
                            'name' => 'thumbnails[' . $breakpointHandle . ']',
                            'id' => 'thumbnails_' . $breakpointHandle,
                            'class' => 'ccm-thumbnail-selector'
                        ]
                    ); ?>
                </div>
            <?php } ?>
        <?php } ?>
    </div>

    <div id="customImageSize">
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

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox('cropImage', 1, isset($cropImage) ? $cropImage : false); ?>
                <?php echo $form->label('cropImage', t("Crop Image"), ["class" => "form-check-label"]); ?>
            </div>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend data-toggle="collapse" data-target="#advancedContainer" aria-expanded="false"
            aria-controls="advancedContainer" style="cursor: pointer;" class="launch-tooltip"
            title="<?php echo h(t("Click to toggle the advanced settings.")); ?>">
        <?php echo t("Advanced"); ?>
    </legend>

    <div class="collapse" id="advancedContainer">
        <div class="form-group">
            <?php
            echo $form->label('title', t('HTML Tag Title'));
            echo $form->text('title', isset($title) ? $title : '', ['maxlength' => 255]);
            ?>
        </div>

        <div class="form-group">
            <?php
            echo $form->label('altText', t('Alt Text'));
            echo $form->text('altText', isset($altText) ? $altText : '', ['maxlength' => 255]);
            ?>
        </div>
    </div>
</fieldset>

<!--suppress JSJQueryEfficiency, ES6ConvertVarToLetConst -->
<script>
    $(function () {
        var $constrainImageSize = $("#constrainImageSize");
        var $thumbnails = $("#thumbnails");
        var $customImageSize = $("#customImageSize");
        var $constrainImage = $("#constrainImage");
        var $fullSizeNotice = $("#fullSizeNotice");
        var $maxHeight = $("#maxHeight");
        var $maxWidth = $("#maxWidth");
        var $cropImage = $("#cropImage");
        var $openLinkContainer = $("#openLinkContainer");
        var $openLinkInLightbox = $("#openLinkInLightbox");
        var $openLinkInNewWindow = $("#openLinkInNewWindow");
        var hasThumbnailItems = <?php /** @noinspection PhpComposerExtensionStubsInspection */echo json_encode(count($thumbnails) > 0) ?>;
        var $thumbnailSelectors = $(".ccm-thumbnail-selector");

        if ($maxHeight.val() > 0 || $maxWidth.val() > 0) {
            $constrainImageSize.val("custom-size")
        } else if (hasThumbnailItems) {
            $constrainImageSize.val("thumbnails")
        }

        if ($("input[name='fOnstateID']").val() > 0) {
            $("#changeImageOnHover").prop("checked", true);
        }

        $constrainImageSize.change(function () {
            $thumbnails.addClass("d-none");
            $customImageSize.addClass("d-none");
            $fullSizeNotice.addClass("d-none");
            $thumbnailSelectors.prop('disabled', true);
            $constrainImage.val(0);

            switch ($(this).val()) {
                case "custom-size":
                    $customImageSize.removeClass("d-none");
                    $constrainImage.val(1);
                    break;
                case "full-size":
                    $fullSizeNotice.removeClass("d-none");
                    $maxHeight.val(0);
                    $maxWidth.val(0);
                    $cropImage.prop("checked", false);
                    break;
                default:
                    $thumbnails.removeClass("d-none");
                    $thumbnailSelectors.prop('disabled', false);
                    $maxHeight.val(0);
                    $maxWidth.val(0);
                    $cropImage.prop("checked", false);
                    break;
            }
        }).trigger('change');

        $("#changeImageOnHover").change(function () {
            if ($(this).is(':checked')) {
                $("#changeImageOnHoverContainer").removeClass("d-none");
            } else {
                $("#changeImageOnHoverContainer").addClass("d-none");
            }
        }).trigger('change');

        $("#imageLink__which").change(function () {
            $openLinkContainer.addClass("d-none");

            if ($(this).val() !== "none") {
                $openLinkContainer.removeClass("d-none");
            }
        }).trigger("change");

        $openLinkInLightbox.change(function () {
            if ($(this).is(":checked")) {
                $openLinkInNewWindow.prop("disabled", true);
                $openLinkInNewWindow.prop("checked", false);
            } else {
                $openLinkInNewWindow.prop("disabled", false);
            }
        }).trigger("change");

        $openLinkInNewWindow.change(function () {
            if ($(this).is(":checked")) {
                $openLinkInLightbox.prop("disabled", true);
                $openLinkInLightbox.prop("checked", false);
            } else {
                $openLinkInLightbox.prop("disabled", false);
            }
        }).trigger("change");
    });
</script>
