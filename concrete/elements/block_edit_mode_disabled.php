<?php
defined('C5_EXECUTE') or die('Access Denied.');
if ((!$width) || (!$height)) {
    $height = 200;
}
?>
<div class="ccm-edit-mode-disabled-item" style="<?php if ($width && $height) {
    ?>width:<?=$width?>px;<?php 
} ?>height:<?=$height?>px;">
	<div style="padding:8px 0px; padding-top: <?=round($height / 2) - 10?>px;"><?=t('Content disabled in edit mode.')?></div>
</div>
