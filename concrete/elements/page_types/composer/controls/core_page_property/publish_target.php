<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $label
 * @var string $description
 */

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
    <?= $form->label('', $label) ?>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) { ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php } ?>

	<?php if ($description) { ?>
	    <i class="fas fa-question-circle launch-tooltip" title="" data-original-title="<?= $description ?>"></i>
	<?php } ?>

	<div data-composer-field="name">
		<?= $target->includeChooseTargetForm($control, $pagetype, $parent ?? null) ?>
	</div>
</div>
