<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?php
$ctArray = PageType::getList();
$types = array();
foreach ($ctArray as $ct) {
    $types[$ct->getPageTypeID()] = $ct->getPageTypeDisplayName();
}

if (is_object($configuration)) {
    $ptID = $configuration->getPageTypeID();
}

?>
<div class="control-group">
	<label class="control-label form-label"><?=t('Limit By Page Type')?></label>
	<div class="controls" data-select="page">
		<?=$form->select($source->optionFormKey('ptID'), $types, $ptID)?>
	</div>
</div>
