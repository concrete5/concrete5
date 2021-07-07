<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="ccm-ui">
    <form method="post" data-dialog-form="remove-tree-node" class="form-horizontal" action="<?=$controller->action('remove_tree_node')?>">
        <?=Loader::helper('validation/token')->output('remove_tree_node')?>
        <input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
        <p><?=t('Are you sure you want to remove "%s"?', $node->getTreeNodeDisplayName())?></p>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-end" data-dialog-action="cancel"><?=t('Cancel')?></button>
            <button class="btn btn-danger float-end" data-dialog-action="submit" type="submit"><?=t('Remove')?></button>
        </div>
    </form>

    <script type="text/javascript">
        $(function() {
            ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.deleteTreeNode');
            ConcreteEvent.subscribe('AjaxFormSubmitSuccess.deleteTreeNode', function(e, data) {
                if (data.form == 'remove-tree-node') {
                    ConcreteEvent.publish('ConcreteTreeDeleteTreeNode', {'node': data.response});
                }
            });
        });
    </script>

</div>
