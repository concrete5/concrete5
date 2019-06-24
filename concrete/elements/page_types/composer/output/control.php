<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<div class="ccm-page-type-composer-output-control" data-page-type-composer-output-control-id="<?=$control->getPageTypeComposerOutputControlID()?>">
<div class="ccm-page-type-composer-item-control-bar">
	<ul class="ccm-item-set-controls">
		<li><a href="#" data-command="move-output-control" style="cursor: move"><i class="fa fa-arrows"></i></a></li>
	</ul>
<div class="ccm-page-type-composer-output-control-inner">
	<?php
    echo $control->getPageTypeComposerControlOutputLabel();
    ?>
</div>
</div>
</div>
