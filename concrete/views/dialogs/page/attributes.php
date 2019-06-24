<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>


<div class="ccm-ui">
<div id="ccm-dialog-attributes-menu"><?=$menu->render()?></div>
<div id="ccm-dialog-attributes-detail">
	<p class="lead"><?=t('Selected Attributes')?></p>
	<?=$detail->render()?></div>
</div>