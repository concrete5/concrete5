<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = \Core::make('helper/form');
?>

<div class="ccm-ui">
	<form method="post" data-dialog-form="add-category-node" class="form-horizontal" action="<?=$controller->action('add_category_node')?>">
		<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>">
		<?php if (isset($treeNodeTypeHandle)) { ?>
			<input type="hidden" name="treeNodeTypeHandle" value="<?=$treeNodeTypeHandle?>">
		<?php } ?>
		<?=Loader::helper('validation/token')->output('add_category_node')?>
		<div class="form-group">
			<?=$form->label('treeNodeCategoryName', t('Name'))?>
			<?=$form->text('treeNodeCategoryName', '', array('class' => 'span4'))?>
		</div>


		<div class="dialog-buttons">
			<button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
			<button class="btn btn-primary float-end" data-dialog-action="submit" type="button"><?=t('Add')?></button>
		</div>
	</form>

	<script type="text/javascript">
		$(function() {
			_.defer(function() {
				$('input[name=treeNodeCategoryName]').focus();
			});
			ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addTreeNode');
			ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addTreeNode', function(e, data) {
				if (data.form == 'add-category-node') {
					ConcreteEvent.publish('ConcreteTreeAddTreeNode', {'node': data.response});
				}
			});
		});
	</script>

</div>
