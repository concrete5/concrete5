<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php $included = $permissionAccess->getAccessListItems(); ?>
<?php $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?php $attributes = CollectionAttributeKey::getList(); ?>
<?php $form = Loader::helper('form'); ?>

<?php if (count($included) > 0 || count($excluded) > 0) {
    ?>

<?php if (count($included) > 0) {
    ?>

<h3><?=t('Who can edit what?')?></h3>

<?php foreach ($included as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="clearfix">
	<label class="form-label"><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('propertiesIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <?php if ($assignment->getAttributesAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php foreach ($attributes as $ak) {
    ?>
			<li><label><input type="checkbox" name="akIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$ak->getAttributeKeyDisplayName()?></span></label></li>
		<?php 
}
    ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowEditName[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditName()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Name')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDescription[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDescription()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Short Description')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUID[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditUserID()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Owner')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDateTime[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDateTime()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Public Date/Time')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditPaths[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditPaths()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Paths')?></span></label></li>
	</ul>

	</div>
</div>


<?php 
}
}
    ?>


<?php if (count($excluded) > 0) {
    ?>

<h3><?=t('Who can\'t edit what?')?></h3>

<?php foreach ($excluded as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">

	<?=$form->select('propertiesExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Page Attributes'), 'C' => t('Custom')), $assignment->getAttributesAllowedPermission())?><br/><br/>
	<ul class="page-type-list inputs-list" <?php if ($assignment->getAttributesAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php foreach ($attributes as $ak) {
    ?>
			<li><label><input type="checkbox" name="akIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$ak->getAttributeKeyID()?>" <?php if (in_array($ak->getAttributeKeyID(), $assignment->getAttributesAllowedArray())) {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$ak->getAttributeKeyDisplayName()?></span></label></li>
		<?php 
}
    ?>
	</ul>
	<ul class="inputs-list">
		<li><label><input type="checkbox" name="allowEditNameExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditName()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Name')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDescriptionExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDescription()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Short Description')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditUIDExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditUserID()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Owner')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditDateTimeExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditDateTime()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Public Date/Time')?></span></label></li>
		<li><label><input type="checkbox" name="allowEditPathsExcluded[<?=$entity->getAccessEntityID()?>]" value="1" <?php if ($assignment->allowEditPaths()) {
    ?>checked="checked" <?php 
}
    ?> /> <span><?=t('Paths')?></span></label></li>
	</ul>
	</div>
</div>



<?php 
}
}
    ?>

<?php 
} else {
    ?>
	<p><?=t('No users or groups selected.')?></p>
<?php 
} ?>

<script type="text/javascript">
$(function() {
	$("#ccm-tab-content-custom-options select").change(function() {
		if ($(this).val() == 'C') {
			$(this).parent().find('ul.page-type-list').show();
		} else {
			$(this).parent().find('ul.page-type-list').hide();
		}
	});
	});
</script>
