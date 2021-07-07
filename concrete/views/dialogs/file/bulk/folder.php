<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Dialog\File\Bulk\Folder;
use Concrete\Core\View\View;

/** @var Folder $controller */

?>

<form method="post" data-dialog-form="move-to-folder" action="<?php echo $controller->action('submit') ?>">
    <?php foreach ($files as $f): ?>
        <input type="hidden" name="fID[]" value="<?php echo $f->getFileID() ?>"/>
    <?php endforeach; ?>

    <div class="ccm-ui">
        <?php
            /** @noinspection PhpUnhandledExceptionInspection */
            View::element('files/bulk/move_to_folder', ['files' => $files])
        ?>
    </div>

    <div class="dialog-buttons">
        <button class="btn btn-secondary float-start" data-dialog-action="cancel">
            <?php echo t('Cancel') ?>
        </button>

        <button type="button" data-dialog-action="submit" class="btn btn-primary float-end">
            <?php echo t('Save') ?>
        </button>
    </div>
</form>

<script type="text/javascript">
    $(function () {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateFolder');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateFolder', function (e, data) {
            if (data.form === 'move-to-folder') {
                ConcreteEvent.publish('FolderUpdateRequestComplete', {
                    'folder': data.response.folder
                });
            }
        });
    });
</script>
