<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php

$sets = FileSet::getMySets();
?>

<div class="form-group" id="ccm-file-set-search">
	<form class="form-inline">
		<input type="search" class="form-control" data-field="file-set-search" autocomplete="off" placeholder="<?=t('Filter Sets')?>" />
	</form>
</div>


<div class="form-group" id="ccm-file-set-list">
	<?php if (count($sets)) {
    ?>
		<?php foreach ($sets as $fs) {
    if ($displayFileSet($fs)) {
        ?>
			<div class="checkbox li">
				<label>
				<?php echo $getCheckbox($fs);
        ?>
				<span data-label="file-set-name"><?=$fs->getFileSetDisplayName()?></span>
				</label>
			</div>
			<?php
    }
    ?>
		<?php
}
    ?>
	<?php
} ?>
</div>

<button type="button" class="btn-sm btn btn-default" data-action="add-file-set"><?=t('Add Set')?> <i class="fa fa-plus-circle"></i></button>

<script type="text/template" class="ccm-template-file-set-checkbox">
	<div class="input-group">
		<input type="text" placeholder="<?=t('Set Name')?>" class="form-control" name="fsNew[]">
		<div class="input-group-addon">
			<label class="checkbox-inline" ><input type="checkbox" name="fsNewShare[]" value="1" checked /> <span class="small"><?=t('Public Set.')?></span></label>
			&nbsp;
			<a href="#" class="icon-link"><i class="fa fa-minus-circle"></i></a>
		</div>
	</div>
</script>

<script type="text/javascript">
	$(function() {
		var _checkbox = _.template($('script.ccm-template-file-set-checkbox').html());
		$('button[data-action=add-file-set]').on('click', function() {
			$('#ccm-file-set-list').append(_checkbox)
		});
		$('#ccm-file-set-list').on('click', 'a', function(e) {
			e.preventDefault();
			var $row = $(this).parents('.input-group');
			$row.remove();
		});
		$('input[data-field=file-set-search]').liveUpdate('ccm-file-set-list', 'fileset').closest('form').unbind('submit.liveupdate');
	});
</script>

<style type="text/css">
	div.form-group-file-set-checkbox {
		position: relative;
		margin-left: 20px;
	}

	div.form-group-file-set-checkbox a {
		position: absolute;
		left: -20px;
		top: 7px;
	}
</style>
