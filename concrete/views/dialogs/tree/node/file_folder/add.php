<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="add-file-folder-node" class="form-horizontal" action="<?= $controller->action('add_file_folder_node'); ?>">
        <input type="hidden" name="treeNodeID" value="<?= $node->getTreeNodeID(); ?>">
        <?= $validation_token->output('add_file_folder_node'); ?>
        <div class="form-group">
            <?= $form->label('fileFolderName', t('Name')); ?>
            <?= $form->text('fileFolderName', ''); ?>
        </div>
        <div class="form-group">
            <?= $form->label('fileFolderFileStorageLocation', t('Storage Location')); ?>
            <?= $form->select('fileFolderFileStorageLocation', $locations); ?>
        </div>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-end" data-dialog-action="cancel"><?= t('Cancel'); ?></button>
            <button class="btn btn-primary float-end" data-dialog-action="submit" type="button"><?= t('Add'); ?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            _.defer(function() {
                $('input[name=fileFolderName]').focus();
            });
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addTreeNode');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addTreeNode', function(e, data) {
                if (data.form == 'add-file-folder-node') {
                    ConcreteEvent.publish('ConcreteTreeAddTreeNode', {'node': data.response});
                }
            });
        });
    </script>
</div>
