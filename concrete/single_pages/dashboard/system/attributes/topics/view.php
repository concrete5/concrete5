<?php defined('C5_EXECUTE') or die("Access Denied.");
?>

<div class="d-flex justify-content-between align-items-center mb-2">
    <div>
        <?php if($trees){ 
            ?>
            <select  name="topicTreeIDSelect" id="topicTreeIDSelect" class="form-select input-sm">
                <?php foreach ($trees as $stree) {
                    ?>
                    <option value="<?=$stree->getTreeID(); ?>" <?php if ($tree->getTreeID() == $stree->getTreeID()) {
                    ?>selected<?php
                    } ?> ><?=$stree->getTreeDisplayName(); ?></option>
                    <?php
                    } ?>
            </select>
            <?php 
        } ?>        
    </div>
    <div>
    <?php if (PermissionKey::getByHandle('add_topic_tree')->validate()) {
        ?>
        <button onclick="window.location.href='<?=$view->url('/dashboard/system/attributes/topics/add'); ?>'" class="btn btn-primary btn-sm"><?=t('Add Topic Tree'); ?></button>
        <?php
    } ?>
    <?php if (PermissionKey::getByHandle('edit_topic_tree')->validate() && is_object($tree)) {
        ?>
        <button type="button" data-dialog="edit-topic-tree" class="btn btn-secondary btn-sm"><?=t('Edit Topic Tree'); ?></button>
    <?php
    }
    ?>
    <?php if (PermissionKey::getByHandle('remove_topic_tree')->validate() && is_object($tree)) {
        ?>
        <button type="button" data-dialog="delete-topic-tree" class="btn btn-danger btn-sm"><?=t('Delete Topic Tree'); ?></button>
    <?php
        } ?>
    </div>
</div>

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
                width: 380,
                title: <?=json_encode(t("Delete Topic Tree")); ?>,
                height: 'auto'
            });
        });

        $('button[data-dialog=edit-topic-tree]').on('click', function() {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-edit-topic-tree',
                modal: true,
                width: 380,
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
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel'); ?></button>
                    <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-topic-tree form').submit()"><?=t('Delete Topic Tree'); ?></button>
                </div>
            </div>
        </div>
    </div>

    <div style="display: none">
        <div id="ccm-dialog-edit-topic-tree" class="ccm-ui">
            <form method="post" class="form-stacked" style="padding-left: 0px" action="<?=$view->action('tree_edit'); ?>">
                <?=Loader::helper("validation/token")->output('tree_edit'); ?>
                <input type="hidden" name="treeID" value="<?=$tree->getTreeID(); ?>" />
                <div class="form-group">
                    <label for="treeName" class="control-label form-label">Tree Name</label>
                    <input type="text" name="treeName" class="form-control ccm-input-text" value="<?=$tree->getTreeDisplayName(); ?>" id="treeName"/>
                </div>


            </form>
            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions ">
                    <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel'); ?></button>
                    <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-edit-topic-tree form').submit()"><?=t('Update Topic Tree'); ?></button>
                </div>
            </div>
        </div>
    </div>

<?php
                } ?>
