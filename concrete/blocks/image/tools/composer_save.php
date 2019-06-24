<?php

defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
    die(t("Unable to add files."));
}
$cf = Loader::helper("file");
$valt = Loader::helper('validation/token');

$json = Loader::helper('json');
$id = Loader::helper('validation/identifier');

if ($_SERVER['REQUEST_METHOD'] == "GET" && $_GET['mode'] == "get_path") {
    if (is_numeric($_REQUEST['fID'])) {
        $path = File::getRelativePathFromID($_REQUEST['fID']);
        echo $json->encode($path);
        exit;
    }
} elseif (isset($_POST['thumbnail']) && strlen($_POST['thumbnail'])) {
    $thumb = base64_decode($_POST['thumbnail']);

    $file_path = Config::get('concrete.cache.directory') . "/composer_".$id->getString().".jpg";

    $fp = fopen($file_path, "w");
    if ($fp) {
        fwrite($fp, base64_decode($_POST['thumbnail']));
        fclose($fp);

        $fi = new FileImporter();
        // if we're working with an existing image, grab the name from it for the new one
        if (is_numeric($_REQUEST['fID']) && $_REQUEST['fID'] > 0) {
            $f = File::getByID($_REQUEST['fID']);
            $fv = $f->getVersion();
            $fileName = $fv->getFileName();
            $fileName = $cf->replaceExtension($fileName, "jpg");
        } else {
            $fileName = false;
        }

        $resp = $fi->import($file_path, $fileName);
        unlink($file_path);

        $obj = new stdClass();
        $obj->fID = $resp->getFileID();
        $obj->bID = $_REQUEST['bID'];
        echo Loader::helper('json')->encode($obj);
        exit;
    }
}
