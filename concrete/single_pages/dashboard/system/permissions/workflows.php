<?php defined('C5_EXECUTE') or die("Access Denied."); 
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
?>
<?php $c = Page::getCurrentPage(); ?>

<?php if (isset($wf)) {
    ?>

<?php if ($this->controller->getTask() == 'edit_details') {
    ?>

<form method="post" action="<?=$view->action('save_workflow_details')?>" method="post">
<input type="hidden" name="wfID" value="<?=$wf->getWorkflowID()?>" />
<?=$app->make('helper/validation/token')->output('save_workflow_details')?>

<?php View::element("workflow/edit_type_form_required", array('workflow' => $wf));
    ?>

<div class="ccm-dashboard-form-actions-wrapper">
<div class="ccm-dashboard-form-actions">
	<a href="<?=URL::page($c, 'view_detail', $wf->getWorkflowID())?>" class="btn btn-secondary float-start"><?=t("Cancel")?></a>
	<input type="submit" name="submit" value="<?=t('Save')?>" class="btn btn-primary float-end" />
</div>
</div>
</form>

<?php 
} else {
    ?>

	<?php View::element("workflow/type_form_required", array('workflow' => $wf));
    ?>

<?php 
}
    ?>



<?php 
} elseif ($this->controller->getTask() == 'add' || $this->controller->getTask() == 'submit_add') {
    ?>

	<form method="post" action="<?=$view->action('submit_add')?>">
	<?=$app->make('helper/validation/token')->output('add_workflow')?>
		<fieldset>
		
			<legend><?=t('Add Workflow')?></legend>
			
			<div class="form-group">
				<div class="input-group">
                    <?=$form->label('wfName', t('Name'), ['class'=>'input-group-text'])?>
					<?=$form->text('wfName', $wfName)?>
                    <div class="input-group-text"><i class="fa fa-asterisk"></i></div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="input-group">
                <?=$form->label('wftID', t('Type'), ['class'=>'input-group-text'])?></div>
                <?=$form->select('wftID', $types, ['class'=>'form-select'])?>
			</div>

			<?php foreach ($typeObjects as $type) {
    ?>
				
				<div style="display: none" class="form-group ccm-workflow-type-form" id="ccm-workflow-type-<?=$type->getWorkflowTypeID()?>">
					<?php
                    if ($type->getPackageID() > 0) {
                        @View::element('workflow/types/' . $type->getWorkflowTypeHandle()  . '/add_type_form', $type->getPackageHandle(), array('type' => $type));
                    } else {
                        @View::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/add_type_form', array('type' => $type));
                    }
    ?>
				</div>
			<?php 
}
    ?>
		</fieldset>
		
		<div class="ccm-dashboard-form-actions-wrapper">
		<div class="ccm-dashboard-form-actions">
			<a href="<?=URL::page($c)?>" class="btn btn-secondary float-start"><?=t('Cancel')?></a>
			<button type="submit" class="btn btn-primary float-end"><?=t('Add')?></button>
		</div>
		</div>
			
	</form>
	
	<script type="text/javascript">
	$(function() {
		$('select[name=wftID]').change(function() {
			$('.ccm-workflow-type-form').hide();
			$('#ccm-workflow-type-' + $(this).val()).show();
		})
		$('#ccm-workflow-type-' + $('select[name=wftID]').val()).show();
	});
	</script>

<?php 
} else {
    ?>

	<div class="ccm-dashboard-header-buttons">
		<a href="<?=URL::to('/dashboard/system/permissions/workflows', 'add')?>" class="btn btn-primary"><?=t('Add Workflow')?></a>
	</div>
	
	<h4><?=t2('%d Workflow', '%d Workflows', count($workflows))?></h4>
	
	<ul class="item-select-list">
	<?php foreach ($workflows as $workflow) {
    ?>
		<li><a href="<?=$view->url('/dashboard/system/permissions/workflows', 'view_detail', $workflow->getWorkflowID())?>"><i class="fa fa-exchange"></i> <?=$workflow->getWorkflowDisplayName()?></a></li>
	<?php 
}
    ?>
	</ul>

<?php 
} ?>
