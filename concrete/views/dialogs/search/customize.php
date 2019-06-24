<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="search-customize">

	<?php
		print $customizeElement->render();
	?>

	<div class="dialog-buttons">
	<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-primary pull-right"><?=t('Save')?></button>
	</div>

</form>
</div>
