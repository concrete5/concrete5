<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="delete-file" class="form-horizontal" action="<?=$controller->action('submit')?>">
        <p><?=t('Are you sure you want to remove "%s"?', $file->getFileName())?></p>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-end" data-dialog-action="cancel"><?=t('Cancel')?></button>
            <button class="btn btn-danger float-end" data-dialog-action="submit" type="submit"><?=t('Remove')?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.deleteFile');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.deleteFile', function(e, data) {
                if (data.form == 'delete-file') {
                    ConcreteEvent.publish('ConcreteDeleteFile', {'file': data.response});
                }
            });
        });
    </script>

</div>
