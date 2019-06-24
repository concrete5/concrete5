<?php
defined('C5_EXECUTE') or die('Access Denied.');
$al = Loader::helper('concrete/asset_library');
?>
<div class="form-group">
	<?=$form->label('fID', t('File'))?>
	<?=$al->file('ccm-b-file', 'fID', t('Choose File'));?>
</div>
<div class="form-group">
	<?=$form->label('fileLinkText', t('Link Text'))?>
	<?=$form->text('fileLinkText')?>
</div>
<div class="form-group">
	<div class="checkbox">
		<label>
			<?=$form->checkbox('forceDownload', '1'); ?>
			<?=t('Force file to download')?>
		</label>
	</div>
</div>
