<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">

	<form method="post" data-form="delete-block" data-action-delete-all="<?=$deleteAllAction?>" data-action="<?=$deleteAction?>">

		<div class="dialog-buttons">
		<button class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
		<button type="button" data-submit="delete-block-form" class="btn btn-danger pull-right"><?=t('Delete')?></button>
		</div>

		<p><?=$message?></p>

		<?php if ($isMasterCollection) { ?>

			<div class="alert alert-danger"><?=$defaultsMessage?></div>

			<div class="form-group">
				<label class="control-label"><?=t('Instances on Child Pages')?></label>
				<div class="radio"><label><input type="radio" name="deleteAll" value="0" checked> <?=t('Delete only unforked instances on child pages.')?></label></div>
				<div class="radio"><label><input type="radio" name="deleteAll" value="1"> <?=t('Delete even forked instances on child pages.')?></label></div>
			</div>


			<div data-dialog-form-element="progress-bar"></div>

		<?php
} else {
    ?>

<?php } ?>

	</form>

	<script type="text/javascript">
	$(function() {
		var $form = $('form[data-form=delete-block]'),
			options = {};
		$('button[data-submit=delete-block-form]').on('click', function() {
			var mode = parseInt($form.find('input[name=deleteAll]:checked').val());
			if (mode == 1) {
				options = {
					url: $form.attr('data-action-delete-all'),
					data: $form.formToArray(true),
					progressiveOperation: true,
					progressiveOperationElement: 'div[data-dialog-form-element=progress-bar]'
				}
			} else {
				options = {
					url: $form.attr('data-action'),
					data: $form.formToArray(true)
				}
			}

			options.success = function(r) {
				var editor = Concrete.getEditMode();
				var area = editor.getAreaByID(parseInt(r.aID));
				var block = area.getBlockByID(parseInt(r.bID));

				ConcreteEvent.fire('EditModeBlockDeleteComplete', {
					block: block
				});
				jQuery.fn.dialog.closeTop();
				ConcreteAlert.notify({
					'message': r.message,
					'title': r.title
				});
			}
			$form.concreteAjaxForm(options);
			$form.trigger('submit');
		});
	});
	</script>

</div>
