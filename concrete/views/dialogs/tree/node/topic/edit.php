<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Loader::helper('form');
    ?>

<div class="ccm-ui">
	<form method="post" data-dialog-form="edit-topic-node" class="form-horizontal" action="<?=$controller->action('update_topic_node')?>">
		<?=Loader::helper('validation/token')->output('update_topic_node')?>
		<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>" />
		<div class="form-group">
			<?=$form->label('treeNodeTopicName', t('Topic'))?>
			<?=$form->text('treeNodeTopicName', $node->getTreeNodeName(), array('class' => 'span4'))?>
		</div>
		<div class="dialog-buttons">
			<button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
			<button class="btn btn-primary float-end" data-dialog-action="submit" type="submit"><?=t('Update')?></button>
		</div>
	</form>

	<script type="text/javascript">
		$(function() {
			_.defer(function() {
				$('input[name=treeNodeTopicName]').focus();
			});
			ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.updateTreeNode');
			ConcreteEvent.subscribe('AjaxFormSubmitSuccess.updateTreeNode', function(e, data) {
				if (data.form == 'edit-topic-node') {
					ConcreteEvent.publish('ConcreteTreeUpdateTreeNode', {'node': data.response});
				}
			});
		});
	</script>

</div>

