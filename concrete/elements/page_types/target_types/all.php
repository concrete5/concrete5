<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
$factors = array('' => t('Sitemap Pop-Up'), 'sitemap_in_page' => t('In-Page Sitemap'));
$pageSelector = Core::make('helper/form/page_selector');
if (is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $type->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();
    $selectorFormFactor = $configuredTarget->getSelectorFormFactor();
    $startingPointPageID = $configuredTarget->getStartingPointPageID();
} else {
    if (!isset($selectorFormFactor)) {
        $selectorFormFactor = null;
    }
    if (!isset($startingPointPageID)) {
        $startingPointPageID = null;
    }
}
?>

<div class="form-group">
	<?=$form->label('selectorFormFactorAll', t('Selector Form Factor'))?>
	<?=$form->select('selectorFormFactorAll', $factors, $selectorFormFactor)?>
</div>

<div class="form-group" data-all-form-factor-display="sitemap_in_page">
	<?=$form->label('startingPointPageIDAll', t('Display Pages Beneath Page'))?>
	<?=$pageSelector->selectPage('startingPointPageIDAll', $startingPointPageID)?>
</div>

<script type="text/javascript">
	$(function() {
		$('select[name=selectorFormFactorAll]').on('change', function() {
			$('div[data-all-form-factor-display]').hide();
			if ($(this).val()) {
				$('div[data-all-form-factor-display=' + $(this).val() + ']').show();
			}
		}).trigger('change');
	});
</script>