<?php
defined('C5_EXECUTE') or die("Access Denied.");
if ($controller->getTask() == 'add') {
    $itemsPerPage = 20;
}

?>

<input type="hidden" name="tab[]" value="output" />

<div class="form-horizontal">
	<div class="control-group" data-row="itemsPerPage">
		<label class="control-label form-label"><?=t('Items Per Page')?></label>
		<div class="controls">
			<?=$form->text('itemsPerPage', $itemsPerPage, array('class' => 'span1'))?>
		</div>
	</div>
</div>
