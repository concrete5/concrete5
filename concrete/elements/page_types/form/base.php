<?php
defined('C5_EXECUTE') or die('Access Denied.');
use \Concrete\Core\Page\Type\PublishTarget\Type\Type as PageTypePublishTargetType;

$form = Loader::helper('form');
$templates = array();
$ag = \Concrete\Core\Http\ResponseAssetGroup::get();
$ag->requireAsset('selectize');
$pagetemplates = PageTemplate::getList();
foreach ($pagetemplates as $pt) {
    $templates[$pt->getPageTemplateID()] = $pt->getPageTemplateDisplayName();
}
$targetTypes = PageTypePublishTargetType::getList();

$ptName = '';
$ptHandle = '';
$ptPageTemplateID = array();
$ptAllowedPageTemplates = 'A';
$ptDefaultPageTemplateID = 0;
$ptLaunchInComposer = 0;
$ptIsFrequentlyAdded = 1;
$token = 'add_page_type';
if (isset($pagetype) && is_object($pagetype)) {
    $token = 'update_page_type';
	$siteType = $pagetype->getSiteTypeObject();
    $ptName = $pagetype->getPageTypeName();
    $ptHandle = $pagetype->getPageTypeHandle();
    $ptLaunchInComposer = $pagetype->doesPageTypeLaunchInComposer();
    $ptDefaultPageTemplateID = $pagetype->getPageTypeDefaultPageTemplateID();
    $ptAllowedPageTemplates = $pagetype->getPageTypeAllowedPageTemplates();
    $ptIsFrequentlyAdded = $pagetype->isPageTypeFrequentlyAdded();
    $selectedtemplates = $pagetype->getPageTypeSelectedPageTemplateObjects();
    foreach ($selectedtemplates as $pt) {
        $ptPageTemplateID[] = $pt->getPageTemplateID();
    }
} else {
    $pagetype = null;
}
?>

<?=Loader::helper('validation/token')->output($token)?>
<input type="hidden" name="siteTypeID" value="<?=$siteType->getSiteTypeID()?>">

	<div class="form-group">
		<?=$form->label('ptName', t('Page Type Name'))?>
    	<?=$form->text('ptName', $ptName, array('class' => 'span5'))?>
	</div>

	<div class="form-group">
		<?=$form->label('ptHandle', t('Page Type Handle'))?>
		<?=$form->text('ptHandle', $ptHandle, array('class' => 'span5'))?>
	</div>

	<div class="form-group">
		<?=$form->label('ptDefaultPageTemplateID', t('Default Page Template'))?>
		<?=$form->select('ptDefaultPageTemplateID', $templates, $ptDefaultPageTemplateID, array('class' => 'span5'))?>
	</div>

	<div class="form-group">
		<?=$form->label('ptLaunchInComposer', t('Launch in Composer?'))?>
		<?=$form->select('ptLaunchInComposer', array('0' => t('No'), '1' => t('Yes')), $ptLaunchInComposer, array('class' => 'span5'))?>
	</div>

    <div class="form-group">
        <?=$form->label('ptIsFrequentlyAdded', t('Is this page type frequently added?'))?>
        <?=$form->select('ptIsFrequentlyAdded', array('0' => t('No'), '1' => t('Yes')), $ptIsFrequentlyAdded, array('class' => 'span5'))?>
        <div class="help-block"><?=t('Frequently added page types are always visible in the Pages panel.')?></div>
    </div>

	<div class="form-group">
		<?=$form->label('ptAllowedPageTemplates', t('Allowed Page Templates'))?>
		<?=$form->select('ptAllowedPageTemplates', array('A' => t('All'), 'C' => t('Selected Page Templates'), 'X' => t('Everything But Selected')), $ptAllowedPageTemplates, array('class' => 'span3'))?>
	</div>

	<div class="form-group" data-form-row="page-templates">
		<?=$form->label('ptPageTemplateID', t('Page Templates'))?>
        <div style="width: 100%">
    		<?=$form->selectMultiple('ptPageTemplateID', $templates, $ptPageTemplateID, array('style' => 'width: 100%'))?>
        </div>
    </div>

	<div class="form-group">
		<?=$form->label('ptPublishTargetTypeID', t('Publish Method'))?>
        <?php for ($i = 0; $i < count($targetTypes); ++$i) {
    $t = $targetTypes[$i];
    if (!is_object($pagetype)) {
        $selected = ($i == 0);
    } else {
        $selected = $pagetype->getPageTypePublishTargetTypeID();
    }
    ?>
            <div class="radio"><label><?=$form->radio('ptPublishTargetTypeID', $t->getPageTypePublishTargetTypeID(), $selected)?><?=$t->getPageTypePublishTargetTypeDisplayName()?></label></div>
        <?php
} ?>
	</div>

	<?php foreach ($targetTypes as $t) {
    if ($t->hasOptionsForm()) {
        ?>

		<div style="display: none" data-page-type-publish-target-type-id="<?=$t->getPageTypePublishTargetTypeID()?>">
			<?php $t->includeOptionsForm($pagetype, $siteType);
        ?>
		</div>

	<?php
    }
} ?>

<script type="text/javascript">
$(function() {
    $('#ptPageTemplateID').removeClass('form-control').selectize({
        plugins: ['remove_button']
    });

	$('input[name=ptPublishTargetTypeID]').on('click', function() {
		$('div[data-page-type-publish-target-type-id]').hide();
		var ptPublishTargetTypeID = $('input[name=ptPublishTargetTypeID]:checked').val();
		$('div[data-page-type-publish-target-type-id=' + ptPublishTargetTypeID + ']').show();
	});
	$('input[name=ptPublishTargetTypeID]:checked').trigger('click');
	$('select[name=ptAllowedPageTemplates]').on('change', function() {
		if ($(this).val() == 'A') {
			$('div[data-form-row=page-templates]').hide();
		} else {
			$('div[data-form-row=page-templates]').show();
		}
	}).trigger('change');
});
</script>
