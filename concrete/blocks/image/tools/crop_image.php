<?php defined('C5_EXECUTE') or die('Access Denied.');
$u = new User();
$fp = FilePermissions::getGlobal();
if (!$fp->canAddFiles()) {
    die(t("Unable to add files."));
}

$f = File::getByID($_REQUEST['fID']);
if (is_object($f) && (!$f->isError())) {
    $fID = $f->getFileID();
}

$bt = BlockType::getByHandle('image');
$url = Loader::helper('concrete/urls');
$save_url = $url->getBlockTypeToolsUrl($bt)."/composer_save?bID=" . $_REQUEST['bID'] . "&fID=" . $fID;

?>
<object type="application/x-shockwave-flash" data="<?php echo ASSETS_URL_JAVASCRIPT?>/thumbnail_editor_3.swf" width="100%" height="500" id="ccm-image-composer-thumbnail-crop">
<param name="wmode" value="transparent">
<param name="quality" value="high">
<param name="flashvars" value="tint=0&amp;backgroundColor=#FFFFFF&amp;upload=true&amp;webcam=false&amp;width=<?=htmlspecialchars($_GET['width'])?>&amp;height=<?=htmlspecialchars($_GET['height'])?>&amp;image=<?=$f->getRelativePath()?>&amp;save=<?=urlencode($save_url)?>">
</object>