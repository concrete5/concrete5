<?php
defined('C5_EXECUTE') or die('Access Denied.');
$templates = array();
$pagetype = $set->getPagetypeObject();
foreach ($pagetype->getPageTypePageTemplateObjects() as $template) {
    $templates[$template->getPageTemplateID()] = $template->getPageTemplateDisplayName();
}
$ptComposerPageTemplateID = $control->getPageTypeComposerControlDraftValue();
if (!$ptComposerPageTemplateID) {
    $ptComposerPageTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
}
?>

<div class="form-group">
	<label class="control-label"><?=$label?></label>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) : ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php endif; ?>
	<?php if ($description): ?>
	<i class="fa fa-question-circle launch-tooltip" title="" data-original-title="<?=$description?>"></i>
	<?php endif; ?>
	<div data-composer-field="page_template">
		<?=$form->select('ptComposerPageTemplateID', $templates, $ptComposerPageTemplateID)?>
	</div>
</div>