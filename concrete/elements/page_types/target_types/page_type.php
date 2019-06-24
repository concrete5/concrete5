<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
$ctArray = PageType::getList(false, $sitetype);
$pageSelector = Core::make('helper/form/page_selector');
$types = array('' => t('** Choose a page type'));
foreach ($ctArray as $cta) {
    $types[$cta->getPageTypeID()] = $cta->getPageTypeDisplayName();
}
$ptID = 0;
$factors = array('' => t('Select Menu'), 'sitemap_in_page' => t('In-Page Sitemap'));

if (isset($pagetype) && is_object($pagetype) && $pagetype->getPageTypePublishTargetTypeID() == $type->getPageTypePublishTargetTypeID()) {
    $configuredTarget = $pagetype->getPageTypePublishTargetObject();
    $ptID = $configuredTarget->getPageTypeID();
    $startingPointPageID = $configuredTarget->getStartingPointPageID();
    $selectorFormFactor = $configuredTarget->getSelectorFormFactor();
} else {
    if (!isset($startingPointPageID)) {
        $startingPointPageID = null;
    }
    if (!isset($selectorFormFactor)) {
        $selectorFormFactor = null;
    }
}
?>
<div class="form-group">
	<?=$form->label('ptID', t('Publish Beneath Pages of Type'))?>
	<?=$form->select('ptID', $types, $ptID)?>
</div>

<div class="form-group">
	<?=$form->label('selectorFormFactorPageType', t('Selector Form Factor'))?>
	<?=$form->select('selectorFormFactorPageType', $factors, $selectorFormFactor)?>
</div>

<div class="form-group" data-page-type-form-factor-display="sitemap_in_page">
	<?=$form->label('startingPointPageIDPageType', t('Display Pages Beneath Page'))?>
	<?=$pageSelector->selectPage('startingPointPageIDPageType', $startingPointPageID)?>
</div>

<script type="text/javascript">
	$(function() {
		$('select[name=selectorFormFactorPageType]').on('change', function() {
			$('div[data-page-type-form-factor-display]').hide();
			if ($(this).val()) {
				$('div[data-page-type-form-factor-display=' + $(this).val() + ']').show();
			}
		}).trigger('change');
	});
</script>