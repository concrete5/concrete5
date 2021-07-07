<?php
defined('C5_EXECUTE') or die("Access Denied.");

$pagetypes = PageType::getList();
$types = array();
foreach ($pagetypes as $pt) {
    $types[$pt->getPageTypeID()] = $pt->getPageTypeDisplayName();
}

?>

<input type="hidden" name="tab[]" value="posting" />

<div class="form-horizontal">
	<div class="control-group">
		<label class="control-label form-label"><?=t('Enable New Topics')?></label>
		<div class="controls">
			<div class="form-check">
                <?=$form->radio('enablePostingFromGathering', 0, $enablePostingFromGathering)?>
                <?=$form->label('enablePostingFromGathering1', ('No, posting is disabled.'))?>
			</div>
			<div class="form-check">
                <?=$form->radio('enablePostingFromGathering', 1, $enablePostingFromGathering)?>
                <?=$form->label('enablePostingFromGathering2', ('Yes, this gathering can be posted to the front-end.'))?>
			</div>
		</div>
	</div>
	<div class="control-group" data-row="enablePostingFromGathering">
		<label class="control-label form-label"><?=t('Create pages using')?></label>
		<div class="controls" data-select="page">
			<?=$form->select('ptID', $types, $ptID)?>
		</div>
	</div>
</div>

<script type="text/javascript">
$(function() {
	$('input[name=enablePostingFromGathering]').on('change', function() {
		var pg = $('input[name=enablePostingFromGathering]:checked');
		if (pg.val() == 1) {
			$('div[data-row=enablePostingFromGathering]').show();
		} else {
			$('div[data-row=enablePostingFromGathering]').hide();
		}
	}).trigger('change');
});
</script>
</div>
