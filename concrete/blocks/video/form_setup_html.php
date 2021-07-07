<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Block\Video\Controller;
use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var Controller $controller */

$app = Application::getFacadeApplication();
/** @var FileManager $fileManager */
$fileManager = $app->make(FileManager::class);
/** @var Form $form */
$form = $app->make(Form::class);

$webm = $controller->getWebmFileID() > 0 ? $controller->getWebMFileObject() : null;
$ogg = $controller->getOggFileID() > 0 ? $controller->getOggFileObject() : null;
$mp4 = $controller->getMp4FileID() > 0 ? $controller->getMp4FileObject() : null;
$poster = $controller->getPosterFileID() > 0 ? $controller->getPosterFileObject() : null;

if (!isset($videoSize)) {
    $videoSize = 0;
}

if (!isset($width)) {
    $width = null;
}
?>

<fieldset>
    <legend>
        <?php echo t('Video Files'); ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label("posterfID", t('Video Placeholder Image (Optional)')); ?>
        <?php echo $fileManager->image('ccm-b-poster-file', 'posterfID', t('Choose Video Placeholder Image'), $poster); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("webmfID", t('WebM')); ?>
        <?php echo $fileManager->video('ccm-b-webm-file', 'webmfID', t('Choose WebM Video File'), $webm); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("oggfID", t('Ogg')); ?>
        <?php echo $fileManager->video('ccm-b-ogg-file', 'oggfID', t('Choose Ogg Video File'), $ogg); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("mp4fID", t('MP4')); ?>
        <?php echo $fileManager->video('ccm-b-mp4-file', 'mp4fID', t('Choose MP4 Video File'), $mp4); ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Video Size'); ?>
    </legend>

    <div class="form-group">
        <div class="form-check">
            <label for="videoSizeDefault" class="form-check-label">
                <?php echo $form->radio("videoSize", "0", $videoSize, ["name" => "videoSize", "id" => "videoSizeDefault"]); ?>
                <?php echo t('Default Video Dimensions'); ?>
            </label>
        </div>

        <div class="form-check">
            <label for="videoSizeFullWidth" class="form-check-label">
                <?php echo $form->radio("videoSize", "1", $videoSize, ["name" => "videoSize", "id" => "videoSizeFullWidth"]); ?>
                <?php echo t('Full Width'); ?>
            </label>
        </div>

        <div class="form-check">
            <label for="videoSizeMaxWidth" class="form-check-label">
                <?php echo $form->radio("videoSize", "2", $videoSize, ["name" => "videoSize", "id" => "videoSizeMaxWidth"]); ?>
                <?php echo t('Set Max Width'); ?>
            </label>
        </div>
    </div>

    <div id="video-width" class="form-group" style="display: none;">
        <?php echo $form->label('width', t('Max Width')); ?>

        <div class="input-group">
            <?php echo $form->number('width', $width ?: '', ['min' => 1]); ?>

            <span class="input-group-text">
                <?php echo t('px'); ?>
            </span>
        </div>
    </div>
</fieldset>

<script>
    $(document).ready(function () {
        if ($('input[name=videoSize]:checked').val() === '2') {
            $('#video-width').show();
        }

        $('input[name=videoSize]').on('change', function () {
            if ($(this).val() === '2') {
                $('#video-width').show();
                $('#width').attr('min', 1);
            } else {
                $('#video-width').hide();
                $('#width').attr('min', 0);
            }
        });
    });
</script>
