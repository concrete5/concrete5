<?php
defined('C5_EXECUTE') or die("Access Denied.");
$form = Core::make("helper/form");
?>

	<div class="ccm-ui">
		<form method="post" data-dialog-form="add-topic-node" class="form-horizontal" action="<?=$controller->action('add_topic_node')?>">
			<input type="hidden" name="treeNodeID" value="<?=$node->getTreeNodeID()?>">
				<?=Loader::helper('validation/token')->output('add_topic_node')?>
			<div class="form-group">
				<?=$form->label('treeNodeTopicName', t('Topic'))?>
				<?=$form->text('treeNodeTopicName', '', array('class' => 'span4'))?>
			</div>

			<div class="dialog-buttons">
				<button class="btn btn-secondary" data-dialog-action="cancel"><?=t('Cancel')?></button>
				<button class="btn btn-primary float-end" data-dialog-action="submit" type="submit"><?=t('Add')?></button>
			</div>

		</form>

		<script type="text/javascript">
			$(function() {
				_.defer(function() {
					$('input[name=treeNodeTopicName]').focus();
				});
				ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addTreeNode');
				ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addTreeNode', function(e, data) {
					if (data.form == 'add-topic-node') {
						ConcreteEvent.publish('ConcreteTreeAddTreeNode', {'node': data.response});
					}
				});
			});
		</script>

	</div>
