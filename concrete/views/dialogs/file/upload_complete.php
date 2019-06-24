<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui" id="ccm-file-manager-upload-complete">
	<div class="alert alert-success">
		<?=t2('%s file uploaded', '%s files uploaded', count($files))?>
		<button data-action="choose-file" style="display: none" type="button" class="pull-right btn btn-success btn-xs"><?=t2('Choose file', 'Choose files', count($files))?></button>

	</div>
	<fieldset>
		<legend><?=t('Properties')?></legend>
		<?php if (count($files) > 1) {
    ?>
			<p><?=t('Properties like name, description and tags are unavailable when uploading multiple files.')?></p>
		<?php 
} else {
    ?>
			<div data-container="editable-core-properties">
				<?php Loader::element('files/properties', array('fv' => $files[0]->getVersion(), 'mode' => 'bulk'))?>
			</div>
		<?php 
} ?>
	</fieldset>
	<fieldset>
		<legend><?=t('Sets')?>
			<button type="button" data-action="manage-file-sets" class="btn btn-xs pull-right btn-default"><?=t('Add/Remove Sets')?></button>
		</legend>

		<section data-container="file-set-list"></section>
	</fieldset>

	<fieldset data-container="editable-attributes">
		<legend><?=t('Custom Attributes')?></legend>
		<section>
			<?php
            Loader::element('attribute/editable_list', array(
                'attributes' => $attributes,
                'objects' => $files,
                'saveAction' => $bulkPropertiesController->action('update_attribute'),
                'clearAction' => $bulkPropertiesController->action('clear_attribute'),
                'permissionsCallback' => function ($ak, $permissionsArguments) use ($canEditFiles) {
                    return $canEditFiles; // this is fine because you can't even access this interface without being able to edit every file.
                },
            ));?>
		</section>

	</fieldset>
</div>

<script type="text/template" class="upload-complete-file-sets">
	<% if (filesets.length > 0) { %>
		<% _.each(filesets, function(fileset) { %>
			<div><%-fileset.fsDisplayName%></div>
		<% }) %>
	<% } else { %>
		<p><?=t('None')?></p>
	<% } %>
</script>

<script type="text/javascript">
$(function() {

	var _sets = _.template($('script.upload-complete-file-sets').html());
	var filesets = <?=json_encode($filesets)?>;
	var fID = <?=json_encode($fileIDs)?>;

	<?php if (count($files) == 1) {
    ?>
		$('[data-container=editable-core-properties]').concreteEditableFieldContainer({
			data: [
				<?php foreach ($files as $f) {
    ?>
				{'name': 'fID[]', 'value': '<?=$f->getFileID()?>'},
				<?php 
}
    ?>
			],
			url: '<?=$propertiesController->action('save')?>'
		});
	<?php 
} ?>
	$('[data-container=editable-attributes]').concreteEditableFieldContainer({
		data: [
			<?php foreach ($files as $f) {
    ?>
			{'name': 'fID[]', 'value': '<?=$f->getFileID()?>'},
			<?php 
} ?>
		]
	});

	$('button[data-action=manage-file-sets]').on('click', function() {
		<?php
        $data = '';
        for ($i = 0; $i < count($files); ++$i) {
            $f = $files[$i];
            $data .= 'fID[]=' . $f->getFileID();
            if ($i + 1 < count($files)) {
                $data .= '&';
            }
        }
        ?>
		$.fn.dialog.open({
			width: '500',
			height: '400',
			href: CCM_DISPATCHER_FILENAME + '/ccm/system/dialogs/file/bulk/sets',
			modal: true,
			data: '<?=$data?>',
			title: ccmi18n_filemanager.sets
		});
	});
	$("[data-container=file-set-list]").html(_sets({filesets: filesets}));

	ConcreteEvent.subscribe('FileSetBulkUpdateRequestComplete', function(e, data) {
		$("[data-container=file-set-list]").html(_sets({filesets: data.filesets}));
	});

	ConcreteEvent.subscribe('FileManagerUploadCompleteDialogOpen', function(e, data) {
		if (data.filemanager && data.filemanager.options.mode == 'choose') {
			$('button[data-action=choose-file]').show();
		}
	});

	ConcreteEvent.subscribe('FileManagerUploadCompleteDialogClose', function(e, data) {
		if (data.filemanager) {
			data.filemanager.refreshResults();
		}
	});

	$('button[data-action=choose-file]').on('click', function() {
		ConcreteEvent.publish('FileManagerSelectFile', {
			fID: fID
		});
		jQuery.fn.dialog.closeTop();
	});

});
</script>