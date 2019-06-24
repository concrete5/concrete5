<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="row">
<div class="span12">
<div class="ccm-pane">


	<div class="ccm-pane-header"><h3><?=$c->getCollectionName()?></h3></div>
	<div class="ccm-pane-body clearfix" id="ccm-stack-container">
	<?php $a = new Area(STACKS_AREA_NAME); $a->display($c); ?>
	</div>

</div>
</div>
</div>