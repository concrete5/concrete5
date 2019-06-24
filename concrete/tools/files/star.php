<?php
    defined('C5_EXECUTE') or die('Access Denied.');

    $file_set = FileSet::createAndGetSet('Starred Files', FileSet::TYPE_STARRED);
    $f = File::getByID($_POST['file-id']);
    $fp = new Permissions($f);
    if (!$fp->canViewFile()) {
        die(t("Access Denied."));
    }

    switch ($_POST['action']) {
        case 'star':
            $file_set->AddFileToSet($_POST['file-id']);
            break;
        case 'unstar':
            $file_set->RemoveFileFromSet($_POST['file-id']);
            break;
        default:
            throw new Exception(t('INVALID ACTION'));
    }
