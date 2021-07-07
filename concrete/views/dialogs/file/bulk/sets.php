<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\File\File;
use Concrete\Core\View\View;
use Concrete\Controller\Dialog\File\Bulk\Sets;

/** @var Sets $controller */
/** @var File[] $files */
?>

<form method="post" data-dialog-form="save-file-set" action="<?php echo $controller->action('submit') ?>">
    <?php foreach ($files as $f): ?>
        <input type="hidden" name="fID[]" value="<?php echo $f->getFileID() ?>"/>
    <?php endforeach;?>

    <div class="ccm-ui">
        <?php
            /** @noinspection PhpUnhandledExceptionInspection */
            View::element('files/bulk/add_to_sets', ['files' => $files])
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

<!--suppress JSUnresolvedVariable -->
<script type="text/javascript">
    $(function () {
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateFileSets');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateFileSets', function (e, data) {
            if (data.form === 'save-file-set') {
                ConcreteEvent.publish('FileSetBulkUpdateRequestComplete', {
                    'filesets': data.response.sets
                });
            }
        });
    });
</script>
