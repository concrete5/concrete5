<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<input type="hidden" name="tab[]" value="sources" />

<div class="form-inline">
<div class="control-group">
	<label class="control-label form-label"><?=t('Add Source')?></label>
	<div class="controls">
		<select name="addSource" class="span2">
			<?php foreach ($availableSources as $ags) {
    ?>
			<option value="<?=$ags->getGatheringDataSourceID()?>"><?=$ags->getGatheringDataSourceName()?></option>
			<?php 
} ?>
		</select>
		<button class="btn" type="button" data-submit="add-source"><?=t('Add')?></button>
	</div>
</div>
</div>

<div id="ccm-gathering-data-source-templates" style="display: none">

<?php foreach ($availableSources as $ags) {
    ?>

<fieldset data-gathering-data-source-options-form="<?=$ags->getGatheringDataSourceID()?>">
	<div style="margin-bottom: 8px">
		<input type="hidden" name="gasID[_gas_]" value="<?=$ags->getGatheringDataSourceID()?>" />
	<a href="#" style="float: right" data-delete="gathering-source"><i class="icon-minus-sign"></i></a>
		<?php $source = $ags;
    ?>
		<?php include($ags->getGatheringDataSourceOptionsForm())?>
		<hr />
	</div>
</fieldset>

<?php 
} ?>

</div>

<div id="ccm-gathering-data-source-templates-selected">

<?php if (count($activeSources) > 0) {
    ?>
<?php foreach ($activeSources as $key => $configuration) {
    ?>

	<fieldset data-gathering-data-source-selected="<?=$configuration->getGatheringDataSourceID()?>">
		<div style="margin-bottom: 8px">
			<input type="hidden" name="gasID[<?=$key?>]" value="<?=$configuration->getGatheringDataSourceID()?>" />
		<a href="#" style="float: right" data-delete="gathering-source"><i class="icon-minus-sign"></i></a>

			<?php
            $source = $configuration;
    $source->setOptionFormKey($key);
    include $configuration->getGatheringDataSourceOptionsForm();
    ?>
			<hr/>
		</div>
	</fieldset>

<?php 
}
    ?>

<?php 
} else {
    ?>
	<span data-message="no-sources"><?=t('You have not added any data sources.')?></span>
<?php 
} ?>
</div>

<script type="text/javascript">
$(function() {
	$('button[data-submit=add-source]').on('click', function() {
		var gasID = $('select[name=addSource]').val();
		$("span[data-message=no-sources]").remove();
		var $fds = $('fieldset[data-gathering-data-source-options-form=' + gasID + ']').clone();
		$fds.removeAttr('data-gathering-data-source-options-form').attr('data-gathering-data-source-selected', gasID).appendTo('#ccm-gathering-data-source-templates-selected');
		var totalsources = $('#ccm-gathering-data-source-templates-selected fieldset[data-gathering-data-source-selected]').length;
		var key = totalsources - 1;
		var html = $fds.html();
		$('#ccm-gathering-data-source-templates-selected').trigger('change');
		html = html.replace(/\[_gas_\]/gi, '[' + key + ']');
		$fds.html(html);
	});
	$('#ccm-gathering-data-source-templates-selected').on('click', 'a[data-delete=gathering-source]', function() {
		$(this).closest('fieldset[data-gathering-data-source-selected]').remove();
		var totalsources = $('#ccm-gathering-data-source-templates-selected fieldset[data-gathering-data-source-selected]').length;
		$('#ccm-gathering-data-source-templates-selected').trigger('change');
		if (!totalsources) {
			$('#ccm-gathering-data-source-templates-selected').html("<span data-message=\"no-sources\"><?=t('You have not added any data sources.')?></span>");
		}
		return false;
	});
});
</script>

<style type="text/css">
#ccm-gathering-data-source-templates-selected {
	margin-top: 18px;
}
</style>
