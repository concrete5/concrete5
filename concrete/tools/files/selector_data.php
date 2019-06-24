<?php
defined('C5_EXECUTE') or die('Access Denied.');

if (!Loader::helper('validation/numbers')->integer($_REQUEST['fID'])) {
    die(t('Access Denied'));
}

$selectedField = Loader::helper('text')->entities($_REQUEST['ccm_file_selected_field']);

$u = new User();
$form = Loader::helper('form');
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
    die(t("Unable to access the file manager."));
}

$f = File::getByID($_REQUEST['fID']);
$fp = new Permissions($f);
if (!$fp->canViewFileInFileManager()) {
    die(t("Access Denied."));
}

$fv = $f->getApprovedVersion();

$canViewInline = $fv->canView() ? 1 : 0;
$canEdit = $fv->canEdit() ? 1 : 0;
?>

<div class="ccm-file-selected" fID="<?=$_REQUEST['fID']?>" ccm-file-manager-field="<?=$selectedField?>" ccm-file-manager-can-duplicate="<?=$fp->canCopyFile()?>" ccm-file-manager-can-admin="<?=($fp->canEditFilePermissions())?>" ccm-file-manager-can-delete="<?=$fp->canDeleteFile()?>" ccm-file-manager-can-view="<?=$canViewInline?>" ccm-file-manager-can-replace="<?=$fp->canEditFileContents()?>" ccm-file-manager-can-edit="<?=$canEdit?>"  >
<div class="ccm-file-selected-thumbnail"><?=$fv->getListingThumbnail()?></div>
<div class="ccm-file-selected-data"><div><?=h($fv->getTitle())?></div><div></div></div>
<div class="ccm-spacer">&nbsp;</div>
</div>