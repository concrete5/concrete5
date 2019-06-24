<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">
    <?php
    $node->populateChildren();
    $count = 0;
    if ($node instanceof \Concrete\Core\Tree\Node\Type\ExpressEntryResults) {
        $count = $node->getTotalResultsInFolder();
    }
    $childCount = count($node->getChildNodes());
    if ($childCount > 0) { ?>
        <div class="alert alert-danger">
            <?=t('This results folder contains one or more results folders. You may not remove it until it is empty.')?>
        </div>
    <?php } else if ($count > 0) {?>
        <div class="alert alert-danger">
            <?=t2('This results folder currently contains one result. It must be empty before you can remove it.', 'This results folder currently contains %s results. It must be empty before you can remove it.', $count)?>
        </div>
    <?php } else { ?>

        <form method="post" data-dialog-form="remove-tree-node" class="form-horizontal" action="<?=$controller->action('remove_tree_node')?>">
            <?=Loader::helper('validation/token')->output('remove_tree_node')?>
            <input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
            <p><?=t('Are you sure you want to remove the Express entity results folder "%s"?', $node->getTreeNodeDisplayName())?></p>

            <div class="dialog-buttons">
                <button class="btn btn-default" data-dialog-action="cancel"><?=t('Cancel')?></button>
                <button class="btn btn-danger pull-right" data-dialog-action="submit" type="submit"><?=t('Remove')?></button>
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

    <?php } ?>

</div>
