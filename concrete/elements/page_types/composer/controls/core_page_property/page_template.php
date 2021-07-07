<?php
defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var Concrete\Core\Page\Type\Composer\Control\Control $control
 * @var Concrete\Core\Form\Service\Form $form
 * @var string $label
 * @var string $description
 */

$templates = [];
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
    <?= $form->label('', $label) ?>
    <?php if ($control->isPageTypeComposerControlRequiredByDefault() || $control->isPageTypeComposerFormControlRequiredOnThisRequest()) { ?>
        <span class="label label-info"><?= t('Required') ?></span>
    <?php } ?>

	<?php if ($description) { ?>
	    <i class="fas fa-question-circle launch-tooltip" title="" data-original-title="<?= $description ?>"></i>
	<?php } ?>

	<div data-composer-field="page_template">
		<?= $form->select('ptComposerPageTemplateID', $templates, $ptComposerPageTemplateID) ?>
	</div>
</div>
