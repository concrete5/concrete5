<?php
defined('C5_EXECUTE') or die('Access Denied.');
$pagetype = $set->getPageTypeObject();
$target = $pagetype->getPageTypePublishTargetObject();
$c = $control->getPageObject();
$cParentID = $control->getPageTypeComposerControlDraftValue();
$parent = Page::getByID($cParentID);
if (is_object($parent) && $parent->isError()) {
    unset($parent);
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
	<div data-composer-field="name">
		<?=$target->includeChooseTargetForm($control, $pagetype, isset($parent) ? $parent : null)?>
	</div>
</div>