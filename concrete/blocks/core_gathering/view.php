<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>


<?php
if ($enablePostingFromGathering && is_object($composer)) {
    ?>

	<div data-gathering-block-id="<?=$b->getBlockID()?>">

	<div style="display: none">
		<div data-form="gathering-post">
			<form data-form="composer">
			<?=Loader::helper('concrete/composer')->display($composer)?>
			<div class="dialog-buttons">
			<button type="button" data-composer-btn="exit" class="btn btn-default pull-left"><?=t('Cancel')?></button>
			<button type="button" data-composer-btn="publish" class="btn btn-primary pull-right"><?=t('Post')?></button>
			</div>
			</form>
		</div>
	</div>

	<button class="btn" data-action="post-to-gathering" type="button"><?=t('Post')?></button><br/><br/>

	</div>

	<script type="text/javascript">
	$(function() {

		var $db = $('div[data-gathering-block-id=<?=$b->getBlockID()?>]'),
			$dialog = $db.find('div[data-form=gathering-post]'),
			$postToGathering = $db.find('button[data-action=post-to-gathering]');

		$db.find('form[data-form=composer]').ccmcomposer({
			onExit: function() {
				$dialog.dialog('close');
			},
			autoSaveEnabled: false,
			publishReturnMethod: 'ajax',
			onPublish: function(r) {
				jQuery.fn.dialog.closeAll();
 				$('div[data-gathering-id=<?=$gathering->getGatheringID()?>]').ccmgathering('getNew');
			}
		});

		$postToGathering.on('click', function() {
			$dialog.dialog({
				modal: true,
				width: 400,
				height: 540,
				title: '<?=t("Post to Gathering")?>',
				open: function() {
					var $buttons = $dialog.find('.dialog-buttons').hide().clone(true,true);
					$(this).dialog('option', 'buttons', [{}]);
					$(this).closest('.ui-dialog').find('.ui-dialog-buttonset').html('').append($buttons.show());
				}
			});
		});
	});
	</script>

<?php 
} ?>

<?php
  Loader::element('gathering/display', array(
    'gathering' => $gathering,
    'list' => $itemList,
    'itemsPerPage' => $itemsPerPage,
  ));
?>

