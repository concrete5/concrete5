<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
    ?>

	<div class="ccm-ui">
		<form method="post" data-dialog-form="edit-topic-category-node" class="form-horizontal" action="<?=$controller->action('update_category_node')?>">
			<?=Loader::helper('validation/token')->output('update_category_node')?>
			<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
			<div class="form-group">
				<?=$form->label('treeNodeCategoryName', t('Name'))?>
				<?=$form->text('treeNodeCategoryName', $node->getTreeNodeName(), array('class' => 'span4'))?>
			</div>
			<div class="dialog-buttons">
				<button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
				<button class="btn btn-primary float-end" data-dialog-action="submit" type="submit"><?=t('Update')?></button>
			</div>
		</form>

		<script type="text/javascript">
			$(function() {
				_.defer(function() {
					$('input[name=treeNodeCategoryName]').focus();
				});
				ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateTreeNode');
				ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateTreeNode', function(e, data) {
					if (data.form == 'edit-topic-category-node') {
						ConcreteEvent.publish('ConcreteTreeUpdateTreeNode', {'node': data.response});
					}
				});
			});
		</script>

	</div>
