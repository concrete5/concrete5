<?php
defined('C5_EXECUTE') or die("Access Denied.");
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;

$c = Page::getCurrentPage();

$form = Loader::helper('form');
$ih = Loader::helper("concrete/ui");
$valt = Loader::helper('validation/token');
$akName = '';
$akIsSearchable = 1;
$asID = 0;

if (is_object($key)) {
    if (!isset($akHandle)) {
        $akHandle = $key->getAttributeKeyHandle();
    }
    $akName = $key->getAttributeKeyName();
    $akIsSearchable = $key->isAttributeKeySearchable();
    $akIsSearchableIndexed = $key->isAttributeKeyContentIndexed();
    $sets = $key->getAttributeSets();
    if (count($sets) == 1) {
        $asID = $sets[0]->getAttributeSetID();
    }
    echo $form->hidden('akID', $key->getAttributeKeyID());
}
?>

<?php if (is_object($key)) {
    ?>
	<?php
    $valt = Loader::helper('validation/token');
    $ih = Loader::helper('concrete/ui');
    $delConfirmJS = t('Are you sure you want to remove this attribute?');
    ?>
	<script type="text/javascript">
	deleteAttribute = function() {
		if (confirm('<?=$delConfirmJS?>')) {
			location.href = "<?=$view->action('delete', $key->getAttributeKeyID(), $valt->generate('delete_attribute'))?>";
		}
	}
	</script>

<div class="ccm-dashboard-header-buttons">
	<button type="button" class="btn btn-danger" onclick="deleteAttribute()"><?=t('Delete Attribute')?></button>
</div>

<?php 
} ?>


<fieldset>
<legend><?=t('%s: Basic Details', $type->getAttributeTypeDisplayName())?></legend>

<div class="form-group">
	<?=$form->label('akHandle', t('Handle'))?>
	<div class="input-group">
	<?=$form->text('akHandle', $akHandle)?>
	<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
	</div>
</div>


<div class="form-group">
	<?=$form->label('akName', t('Name'))?>
	<div class="input-group">
		<?=$form->text('akName', $akName)?>
		<span class="input-group-addon"><i class="fa fa-asterisk"></i></span>
	</div>
</div>

<?php if ($category && $category->getController()->getSetManager()->allowAttributeSets() == AttributeKeyCategory::ASET_ALLOW_SINGLE) {
    ?>
<div class="form-group">
<?=$form->label('asID', t('Set'))?>
<div class="controls">
	<?php
        $sel = array('0' => t('** None'));
    $sets = $category->getAttributeSets();
    foreach ($sets as $as) {
        $sel[$as->getAttributeSetID()] = $as->getAttributeSetDisplayName();
    }
    echo $form->select('asID', $sel, $asID);
    ?>
</div>
</div>
<?php 
} ?>

<div class="form-group">
<label class="control-label form-label"><?=t('Searchable')?></label>

<?php
$keyword_label = t('Content included in search index.');
$advanced_label = t('Field available in advanced search.');

if (is_object($category)) {
    $category_handle = $category->getAttributeKeyCategoryHandle();
    $keyword_label = t('Content included in "Keyword Search".');
    $advanced_label = t('Field available in "Advanced Search".');
    switch ($category_handle) {
        case 'collection':
            $keyword_label = t('Content included in sitewide page search index.');
            $advanced_label = t('Field available in Dashboard Page Search.');
            break;
        case 'file':
            $keyword_label = t('Content included in file search index.');
            $advanced_label = t('Field available in File Manager Search.');
            break;
        case 'user':
            $keyword_label = t('Content included in user keyword search.');
            $advanced_label = t('Field available in Dashboard User Search.');
            break;
    }
}
    ?>
	<div class="form-check"><?=$form->checkbox('akIsSearchableIndexed', 1, $akIsSearchableIndexed)?> <?=$form->label('akIsSearchableIndexed',$keyword_label, ['class'=>'form-check-label'])?></div>
	<div class="form-check"><?=$form->checkbox('akIsSearchable', 1, $akIsSearchable)?> <?=$form->label('akIsSearchable',$advanced_label, ['class'=>'form-check-label'])?></div>
</div>

</fieldset>

<?=$form->hidden('atID', $type->getAttributeTypeID())?>
<?php if ($category) {
    ?>
	<?=$form->hidden('akCategoryID', $category->getAttributeKeyCategoryID());
    ?>

<?php

    if ($category->getPackageID() > 0) {
        @Loader::packageElement('attribute/categories/' . $category->getAttributeKeyCategoryHandle(), $category->getPackageHandle(), array('key' => $key));
    } else {
        @Loader::element('attribute/categories/' . $category->getAttributeKeyCategoryHandle(), array('key' => $key));
    }
    ?>

<?php 
} ?>

<?=$valt->output('add_or_update_attribute')?>
<?php $type->render(new \Concrete\Core\Attribute\Context\AttributeTypeSettingsContext(), $key); ?>

<?php if (!isset($back)) {
    $back = URL::page($c);
}
?>

<div class="ccm-dashboard-form-actions-wrapper">
<div class="ccm-dashboard-form-actions">
	<a href="<?=$back?>" class="btn float-start btn-secondary"><?=t('Back')?></a>
<?php if (is_object($key)) {
    ?>
	<button type="submit" class="btn btn-primary float-end"><?=t('Save')?></button>
<?php 
} else {
    ?>
	<button type="submit" class="btn btn-primary float-end"><?=t('Add')?></button>
<?php 
} ?>
</div>
</div>

