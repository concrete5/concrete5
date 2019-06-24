<?php
defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\File\EditResponse as FileEditResponse;

$u = new User();
$form = Loader::helper('form');
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
    die(t("Unable to access the file manager."));
}

$token_validator = \Core::make('helper/validation/token');

if ($_POST['task'] == 'delete_files') {
    $fr = new FileEditResponse();
    if ($token_validator->validate('files/delete')) {
        $files = array();
        if (is_array($_POST['fID'])) {
            foreach ($_POST['fID'] as $fID) {
                $f = File::getByID($fID);
                $fp = new Permissions($f);
                if ($fp->canDeleteFile()) {
                    $files[] = $f;
                    $f->delete();
                } else {
                    throw new Exception(t('Unable to delete one or more files.'));
                }
            }
        }

        $fr->setMessage(t2('%s file deleted successfully.', '%s files deleted successfully.', count($files)));
    } else {
        $fr->setError(new \Exception('Invalid Token'));
    }
    $fr->outputJSON();
}

$form = Loader::helper('form');

$files = array();
if (is_array($_REQUEST['fID'])) {
    foreach ($_REQUEST['fID'] as $fID) {
        $files[] = File::getByID($fID);
    }
} else {
    $files[] = File::getByID($_REQUEST['fID']);
}

$fcnt = 0;
foreach ($files as $f) {
    $fp = new Permissions($f);
    if ($fp->canDeleteFile()) {
        ++$fcnt;
    }
}

?>

<div class="ccm-ui">
<br/>
<?php if ($fcnt == 0) {
    ?>
	<p><?=t("You do not have permission to delete any of the selected files.");
    ?><p>
<?php 
} else {
    ?>

	<div class="alert alert-warning"><?=t('Are you sure you want to delete the following files?')?></div>

	<form data-dialog-form="delete-file" method="post" action="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/files/delete">
        <?= $token_validator->output('files/delete') ?>
	<?=$form->hidden('task', 'delete_files')?>
	<table border="0" cellspacing="0" cellpadding="0" width="100%" class="table table-striped">

	<?php foreach ($files as $f) {
    $fp = new Permissions($f);
    if ($fp->canDeleteFile()) {
        $fv = $f->getApprovedVersion();
        if (is_object($fv)) {
            ?>

			<?=$form->hidden('fID[]', $f->getFileID())?>

			<tr>
				<td><?=$fv->getType()?></td>
				<td class="ccm-file-list-filename" width="100%"><div style="word-wrap: break-word; width: 150px"><?=h($fv->getTitle())?></div></td>
				<td><?=$dh->formatDateTime($f->getDateAdded()->getTimestamp())?></td>
				<td><?=$fv->getSize()?></td>
				<td><?=$fv->getAuthorName()?></td>
			</tr>

			<?php 
        }
    }
}
    ?>
	</table>
	</form>

	<div class="dialog-buttons">
	<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-danger pull-right"><?=t('Delete')?></button>
	</div>

</div>

	<script type="text/javascript">
	$(function() {
		ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
			if (data.form == 'delete-file') {
				ConcreteEvent.publish('FileManagerDeleteFilesComplete', {files: data.response.files});
			}
		});
	});
	</script>

	<?php

}
