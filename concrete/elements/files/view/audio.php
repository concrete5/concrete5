<?php defined('C5_EXECUTE') or die('Access Denied.');
$path = $fv->getURL();
?>
<audio src="<?=$path?>" controls="controls" preload="auto">
    <object width="500" height="42">
        <param name="src" value="<?=$path?>">
        <embed src="<?=$path?>" width="500" height="42" ></embed>
    </object>
</audio>