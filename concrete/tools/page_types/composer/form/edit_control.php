<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
use \Concrete\Core\Page\Type\Composer\FormLayoutSetControl as PageTypeComposerFormLayoutSetControl;

$c = Page::getByPath('/dashboard/pages/types/form');
$cp = new Permissions($c);
$ih = Loader::helper('concrete/ui');
$control = PageTypeComposerFormLayoutSetControl::getByID($_REQUEST['ptComposerFormLayoutSetControlID']);
if (!is_object($control)) {
    die(t('Invalid control'));
}
$form = Loader::helper('form');

$object = $control->getPageTypeComposerControlObject();
$customTemplates = $object->getPageTypeComposerControlCustomTemplates();
$templates = array('' => t('** None'));
foreach ($customTemplates as $template) {
    $templates[(string) $template->getPageTypeComposerControlCustomTemplateFilename()] = $template->getPageTypeComposerControlCustomTemplateName();
}

if ($cp->canViewPage()) {
    if (isset($_POST['task']) && $_POST['task'] == 'edit' && Loader::helper('validation/token')->validate('update_set_control')) {
        $sec = Loader::helper('security');
        $label = $sec->sanitizeString($_POST['ptComposerFormLayoutSetControlCustomLabel']);
        $template = $sec->sanitizeString($_POST['ptComposerFormLayoutSetControlCustomTemplate']);
        $description = $sec->sanitizeString($_POST['ptComposerFormLayoutSetControlDescription']);
        $required = $sec->sanitizeInt(isset($_POST['ptComposerFormLayoutSetControlRequired']) ? $_POST['ptComposerFormLayoutSetControlRequired'] : null);
        $control->updateFormLayoutSetControlCustomLabel($label);
        $control->updateFormLayoutSetControlCustomTemplate($template);
        $control->updateFormLayoutSetControlDescription($description);
        if ($object->pageTypeComposerFormControlSupportsValidation()) {
            $control->updateFormLayoutSetControlRequired($required);
        }
        Loader::element('page_types/composer/form/layout_set/control', array('control' => $control));
        exit;
    }

    ?>

	<div class="ccm-ui">
		<form data-edit-set-form-control="<?=$control->getPageTypeComposerFormLayoutSetControlID()?>" action="#" method="post">
		<div class="form-group">
			<?=$form->label('ptComposerFormLayoutSetControlCustomLabel', t('Custom Label'))?>
			<?=$form->text('ptComposerFormLayoutSetControlCustomLabel', $control->getPageTypeComposerFormLayoutSetControlCustomLabel())?>
		</div>
		<div class="form-group">
			<?=$form->label('ptComposerFormLayoutSetControlCustomTemplate', t('Custom Template'))?>
			<?=$form->select('ptComposerFormLayoutSetControlCustomTemplate', $templates, $control->getPageTypeComposerFormLayoutSetControlCustomTemplate())?>
		</div>
		<div class="form-group">
			<?=$form->label('ptComposerFormLayoutSetControlDescription', t('Description'))?>
			<?=$form->text('ptComposerFormLayoutSetControlDescription', $control->getPageTypeComposerFormLayoutSetControlDescription())?>
		</div>

		<?php if ($object->pageTypeComposerFormControlSupportsValidation()) {
    ?>
		<div class="form-group">
			<?=$form->label('ptComposerFormLayoutSetControlRequired', t('Required'))?>
			<div class="checkbox">
			<label><?=$form->checkbox('ptComposerFormLayoutSetControlRequired', 1, $control->isPageTypeComposerFormLayoutSetControlRequired())?> <?=t('Yes, require this form element')?></label>
			</div>
		</div>
		<?php 
}
    ?>

		<?=Loader::helper('validation/token')->output('update_set_control')?>
		</form>
		<div class="dialog-buttons">
			<button class="btn btn-default" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
			<button class="btn btn-primary pull-right" data-submit-set-form="<?=$control->getPageTypeComposerFormLayoutSetControlID()?>"><?=t('Save')?></button>
		</div>

	</div>


<script type="text/javascript">
$(function() {
	$('form[data-edit-set-form-control]').on('submit', function() {
		var ptComposerFormLayoutSetControlID = $(this).attr('data-edit-set-form-control');
		var formData = $('form[data-edit-set-form-control=' + ptComposerFormLayoutSetControlID + ']').serializeArray();
		formData.push({
			'name': 'ptComposerFormLayoutSetControlID',
			'value': ptComposerFormLayoutSetControlID
		}, {
			'name': 'task',
			'value': 'edit'
		});
		jQuery.fn.dialog.showLoader();
		$.ajax({
			type: 'post',
			data: formData,
			url: '<?=REL_DIR_FILES_TOOLS_REQUIRED?>/page_types/composer/form/edit_control',
			success: function(html) {
				jQuery.fn.dialog.hideLoader();
				jQuery.fn.dialog.closeTop();
				var data = $(html).html();
				$('tr[data-page-type-composer-form-layout-control-set-control-id=<?=$control->getPageTypeComposerFormLayoutSetControlID()?>]').html(data);
				$('a[data-command=edit-form-set-control]').dialog();
			}
		});		
		return false;
	});
	$('button[data-submit-set-form]').on('click', function() {
		var ptComposerFormLayoutSetControlID = $(this).attr('data-submit-set-form');
		$('form[data-edit-set-form-control=' + ptComposerFormLayoutSetControlID + ']').trigger('submit');
	});
});
</script>


<?php

}
