<?php defined('C5_EXECUTE') or die('Access Denied.');
$path = $fv->getURL();
?>
<video src="<?=$path?>" width="70%" height="70%" preload="auto" controls="controls">
    <object id="MediaPlayer" width="70%" height="70%">
        <param name="FileName" value="<?=$path?>">
        <embed src="<?=$path?>" name="MediaPlayer" width="70%" height="70%"></embed>
    </object>
</video>
