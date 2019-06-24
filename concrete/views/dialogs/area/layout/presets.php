<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-ui">

    <p><?=t('You can add saved layouts to other areas in your site. Note: these are different than any layout presets that might be included with your theme.')?></p>

<form method="post" action="<?=$controller->action('submit')?>" data-dialog-form="save-area-layout-presets" >
	<input type="hidden" value="<?=Loader::helper('security')->sanitizeInt($_REQUEST['arLayoutID'])?>" name="arLayoutID" />

	<div class="form-group">
		<label class="control-label" for="arLayoutPresetID"><?=t('Preset')?></label>
        <?=Loader::helper('form')->select('arLayoutPresetID', $presets, array('class' => 'span3'))?>
	</div>

	<div class="form-group" id="ccm-layout-save-preset-name">
		<label class="control-label" for="arLayoutPresetName"><?=t('Name')?></label>
        <input type="text" name="arLayoutPresetName" id="arLayoutPresetName" class="form-control" />
	</div>

  	<div class="alert alert-warning" id="ccm-layout-save-preset-override"><?=t('Note: this will override the selected preset with the new preset. It will not update any layouts already in use.')?></div>

	<div class="dialog-buttons">
	<button class="btn btn-default pull-left" data-dialog-action="cancel"><?=t('Cancel')?></button>
	<button type="button" data-dialog-action="submit" class="btn btn-success pull-right"><?=t('Save Preset')?></button>
	</div>


</form>

</div>

<script type="text/javascript">
    $(function() {
        var $input = $('input[name=arLayoutPresetName]', 'form[data-dialog-form=save-area-layout-presets]');
        $('select[name=arLayoutPresetID]', 'form[data-dialog-form=save-area-layout-presets]').on('change', function() {
           if ($(this).val() == '-1') {
               $('#ccm-layout-save-preset-override').hide();
               $input.val('');
           } else {
               $('#ccm-layout-save-preset-override').show();
               $input.val($(this).find('option:selected').text());
           }
        }).trigger('change');
    });
</script>