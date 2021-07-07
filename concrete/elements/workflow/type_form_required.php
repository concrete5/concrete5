<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$form = $app->make('helper/form');
$ih = $app->make('helper/concrete/ui');
$valt = $app->make('helper/validation/token');

$type = $workflow->getWorkflowTypeObject();

$valt = $app->make('helper/validation/token');
$ih = $app->make('helper/concrete/ui');
$delConfirmJS = t('Are you sure you want to remove this workflow?');
?>

<script type="text/javascript">
    deleteWorkflow = function() {
        if (confirm('<?=$delConfirmJS?>')) {
            location.href = "<?=$view->action('delete', $workflow->getWorkflowID(), $valt->generate('delete_workflow'))?>";
        }
    }
</script>


<div class="ccm-dashboard-header-buttons btn-group">
        <?php if (is_object($workflow)) {
    ?>

            <?php echo $ih->button_js(t('Delete Workflow'), "deleteWorkflow()", '', 'btn-danger');
    ?>
        <?php 
} ?>
        <?php
        if ($type->getPackageID() > 0) {
            View::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/type_form_buttons', array('type' => $type, 'workflow' => $workflow), $type->getPackageHandle());
        } ?>
        <a href="<?=$view->action('edit_details', $workflow->getWorkflowID())?>" class="btn btn-primary"><?=t('Edit Details')?></a>
</div>
<input type="hidden" name="wfID" value="<?=$workflow->getWorkflowID()?>" />

<h3><?=$workflow->getWorkflowDisplayName()?> <small><?=$type->getWorkflowTypeName()?></small></h3>

<?php
if ($type->getPackageID() > 0) {
    View::element('workflow/types/' . $type->getWorkflowTypeHandle()  . '/type_form', array('type' => $type, 'workflow' => $workflow), $type->getPackageHandle());
} else {
    View::element('workflow/types/' . $type->getWorkflowTypeHandle() . '/type_form', array('type' => $type, 'workflow' => $workflow));
}
?>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?=URL::to('/dashboard/system/permissions/workflows')?>" class="btn btn-secondary float-start"><?=t('Back to List')?></a>

    </div>
</div>
