<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Asset\Output\JavascriptFormatter;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Http\ResponseAssetGroup;

/** @var Version $fileVersion */

$group = ResponseAssetGroup::get();
$formatter = new JavascriptFormatter();
$output = $group->getAssetsToOutput();
foreach ($output as $position => $assets) {
    foreach ($assets as $asset) {
        if ($asset instanceof Concrete\Core\Asset\Asset) {
            print $formatter->output($asset);
        }
    }
}
?>

<div id="tui-image-editor-container"></div>

<style>
    .tui-image-editor-download-btn, .tui-image-editor-header-buttons {
        display: none !important;
    }
</style>

<div class="dialog-buttons">
    <button class="tui-image-editor-close-btn btn btn-default float-start">
        <?php echo t('Cancel') ?>
    </button>

    <button class="tui-image-editor-save-btn btn btn-primary">
        <?php echo t("Save"); ?>
    </button>
</div>

<script>
$(document).ready(function() {
    var imageEditor = new window.tuiImageEditor('#tui-image-editor-container', {
        includeUI: {
            loadImage: {
                path: '<?php echo h($fileVersion->getURL()); ?>',
                name: '<?php echo h($fileVersion->getFileName()); ?>'
            },
            menuBarPosition: 'bottom'
        }
    });

    $(".tui-image-editor-close-btn").on('click', function () {
        $.fn.dialog.closeTop();
    });

    $('.tui-image-editor-save-btn').on('click', function () {
        var url = CCM_DISPATCHER_FILENAME + '/ccm/system/file/edit/save/<?php echo h($fileVersion->getFileID()); ?>';

        $.concreteAjax({
            dataType: 'json',
            data: {
                token: CCM_SECURITY_TOKEN,
                imageData: imageEditor.toDataURL()
            },
            type: 'POST',
            url: url,
            success: function (r) {
                $.fn.dialog.closeTop();
            }
        });
    });
});
</script>
