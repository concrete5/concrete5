<?php
defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();

$form = Loader::helper('form');
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

$fp = FilePermissions::getGlobal();
if (!$fp->canAccessFileManager()) {
    die(t("Unable to access the file manager."));
}

$items = Request::request('fID');
if (Request::request('task') == 'duplicate_multiple_files') {
    $json['error'] = false;

    if (is_array($items)) {
        foreach ($items as $fID) {
            $f = File::getByID($fID);
            $fp = new Permissions($f);
            if ($fp->canCopyFile()) {
                $nf = $f->duplicate();
                $json['fID'][] = $nf->getFileID();
            } else {
                $json['errors'] = array(t('Unable to copy one or more files.'));
            }
        }
    }
    echo json_encode($json);
    exit;
}

if (!is_array($items)) {
    $obj = new stdClass();
    $obj->message = '';
    $obj->error = 0;

    $f = File::getByID($_REQUEST['fID']);
    $fp = new Permissions($f);
    if (!is_object($f) || $f->isError()) {
        $obj->error = 1;
        $obj->message = t('Invalid file.');
    } else {
        if (!$fp->canCopyFile()) {
            $obj->error = 1;
            $obj->message = t('You do not have the ability to copy this file.');
        }
    }

    if (!$obj->error) {
        $nf = $f->duplicate();
        if (is_object($nf)) {
            $obj->fID = $nf->getFileID();
        }
    }

    echo $js->encode($obj);
    exit;
} else {
    $files = array();

    foreach ($items as $fID) {
        $files[] = File::getByID($fID);
    }

    $fcnt = 0;
    foreach ($files as $f) {
        $fp = new Permissions($f);
        if ($fp->canCopyFile()) {
            ++$fcnt;
        }
    }

    $searchInstance = Loader::helper('text')->entities($_REQUEST['searchInstance']);

    ?>

<div class="ccm-ui ccm-copy-form">

    <?php if ($fcnt == 0) {
    ?>
        <?= t("You do not have permission to copy any of the selected files.");
    ?>
    <?php 
} else {
    ?>
        <?= t('Are you sure you want to copy the following files?') ?><br/><br/>

        <form id="ccm-<?= $searchInstance ?>-duplicate-form" method="post"
              action="<?= REL_DIR_FILES_TOOLS_REQUIRED ?>/files/duplicate">
            <?= $form->hidden('task', 'duplicate_multiple_files') ?>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" class="table table-bordered">

                <?php foreach ($files as $f) {
    $fp = new Permissions($f);
    if ($fp->canCopyFile()) {
        $fv = $f->getApprovedVersion();
        if (is_object($fv)) {
            ?>

                            <?= $form->hidden('item[]', $f->getFileID()) ?>

                            <tr>
                                <td><?= $fv->getType() ?></td>
                                <td class="ccm-file-list-filename" width="100%">
                                    <div style="width: 150px; word-wrap: break-word"><?=h($fv->getTitle()) ?></div>
                                </td>
                                <td><?= $dh->formatDateTime($f->getDateAdded()->getTimestamp()) ?></td>
                                <td><?= $fv->getSize() ?></td>
                                <td><?= $fv->getAuthorName() ?></td>
                            </tr>

                        <?php

        }
    }
}
    ?>
            </table>
            <?php $ih = Loader::helper('concrete/ui') ?>
            <div class="dialog-buttons">
                <button class="btn btn-default cancel"><?= t('Cancel') ?></button>
                <button class="btn btn-primary pull-right submit"><?= t('Copy') ?></button>
            </div>
        </form>
    <?php

}
}?>
</div>

<script>
    (function () {
        var container = $('div.ccm-copy-form'),
            copy = $('button.submit', container),
            cancel = $('button.cancel', container),
            form = $('form', container);

        cancel.click(function (e) {
            e.preventDefault();

            $.fn.dialog.closeTop();

            return false;
        });

        copy.click(function (e) {
            e.preventDefault();

            $.getJSON(form.attr('action'), form.serialize(), function (data) {
                cancel.click();
                Window.location.reload();
            }).fail(function (data) {
                if (data.responseJSON && data.responseJSON.errors) {
                    alert(data.responseJSON.errors.join("\n"));
                } else {
                    alert(data.responseText);
                }
            });

        });
    }());
</script>
