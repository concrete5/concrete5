<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    $al = Loader::helper('concrete/asset_library');
    $bf = null;
    if ($controller->getFileID() > 0) {
        $bf = $controller->getFileObject();
    }
?>
<div class="form-group">
	<?=$form->label('fID', t('File'))?>
	<?=$al->file('ccm-b-file', 'fID', t('Choose File'), $bf);?>
</div>
<div class="form-group">
	<?=$form->label('fileLinkText', t('Link Text'))?>
	<?=$form->text('fileLinkText', $controller->getLinkText())?>
</div>

<div class="form-group">
    <div class="checkbox">
        <label>
            <?=$form->checkbox('forceDownload', '1', $forceDownload); ?>
            <?=t('Force file to download')?>
        </label>
    </div>
</div>
