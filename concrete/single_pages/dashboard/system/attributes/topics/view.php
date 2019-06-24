<?php defined('C5_EXECUTE') or die('Access Denied.');
?>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav">
            <li class="navbar-form" style="padding-left: 0px">
                <select  name="topicTreeIDSelect" id="topicTreeIDSelect" class="form-control input-sm">
                    <?php foreach ($trees as $stree) {
    ?>
                        <option value="<?=$stree->getTreeID(); ?>" <?php if ($tree->getTreeID() == $stree->getTreeID()) {
        ?>selected<?php
    } ?>><?=$stree->getTreeDisplayName(); ?></option>
                    <?php
} ?>
                </select>
            </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
            <li class="navbar-form">
                <?php if (PermissionKey::getByHandle('add_topic_tree')->validate()) {
        ?>
                    <button onclick="window.location.href='<?=$view->url('/dashboard/system/attributes/topics/add'); ?>'" class="btn btn-primary btn-sm"><?=t('Add Topic Tree'); ?></button>
                    <?php
    } ?>
                <?php if (PermissionKey::getByHandle('edit_topic_tree')->validate() && is_object($tree)) {
        ?>
                    <button type="button" data-dialog="edit-topic-tree" class="btn btn-default btn-sm"><?=t('Edit Topic Tree'); ?></button>
                    <?php
    }
                ?>
                <?php if (PermissionKey::getByHandle('remove_topic_tree')->validate() && is_object($tree)) {
                    ?>
                    <button type="button" data-dialog="delete-topic-tree" class="btn btn-danger btn-sm"><?=t('Delete Topic Tree'); ?></button>
                    <?php
                } ?>
            </li>
        </ul>
    </div>
</nav>

<?php if (is_object($tree)) {
                    ?>
	<div data-tree="<?=$tree->getTreeID(); ?>">
	</div>

	<script type="text/javascript">
	$(function() {
		$('select[name=topicTreeIDSelect]').on('change', function() {
			window.location.href = '<?=$view->url('/dashboard/system/attributes/topics', 'view'); ?>' + $(this).val();
		});
		
		$('[data-tree]').concreteTree({
			'treeID': '<?=$tree->getTreeID(); ?>'
		});

        $('button[data-dialog=delete-topic-tree]').on('click', function() {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-delete-topic-tree',
                modal: true,
                width: 320,
                title: <?=json_encode(t("Delete Topic Tree")); ?>,
                height: 'auto'
            });
        });

        $('button[data-dialog=edit-topic-tree]').on('click', function() {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-edit-topic-tree',
                modal: true,
                width: 320,
                title: <?=json_encode(t("Edit Topic Tree")); ?>,
                height: 'auto'
            });
        });

	});
	</script>


    <div style="display: none">
        <div id="ccm-dialog-delete-topic-tree" class="ccm-ui">
            <form method="post" class="form-stacked" style="padding-left: 0px" action="<?=$view->action('remove_tree'); ?>">
                <?=Loader::helper("validation/token")->output('remove_tree'); ?>
                <input type="hidden" name="treeID" value="<?=$tree->getTreeID(); ?>" />
                <p><?=t('Are you sure? This action cannot be undone.'); ?></p>
            </form>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel'); ?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-delete-topic-tree form').submit()"><?=t('Delete Topic Tree'); ?></button>
            </div>
        </div>
    </div>

    <div style="display: none">
        <div id="ccm-dialog-edit-topic-tree" class="ccm-ui">
            <form method="post" class="form-stacked" style="padding-left: 0px" action="<?=$view->action('tree_edit'); ?>">
                <?=Loader::helper("validation/token")->output('tree_edit'); ?>
                <input type="hidden" name="treeID" value="<?=$tree->getTreeID(); ?>" />
                <div class="form-group">
                    <label for="treeName" class="control-label">Tree Name</label>
                    <input type="text" name="treeName" class="form-control ccm-input-text" value="<?=$tree->getTreeDisplayName(); ?>" id="treeName"/>
                </div>


            </form>
            <div class="dialog-buttons">
                <button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel'); ?></button>
                <button class="btn btn-danger pull-right" onclick="$('#ccm-dialog-edit-topic-tree form').submit()"><?=t('Update Topic Tree'); ?></button>
            </div>
        </div>
    </div>

<?php
                } ?>