<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php $included = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_INCLUDE); ?>
<?php $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?php
$gl = new GroupList();
$gl->filter('gID', REGISTERED_GROUP_ID, '>');
$gl->sortBy('gID', 'asc');
$gIDs = $gl->getResults();
$gArray = array();
foreach ($gIDs as $gID) {
    $groups[] = Group::getByID($gID['gID']);
}
?>
<?php $form = Loader::helper('form'); ?>

<?php if (count($included) > 0 || count($excluded) > 0) {
    ?>

<?php if (count($included) > 0) {
    ?>

<h3><?=t('Who can assign what?')?></h3>

<?php foreach ($included as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('groupsIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Groups'), 'C' => t('Custom')), $assignment->getGroupsAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <?php if ($assignment->getGroupsAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php foreach ($groups as $g) {
    ?>
			<li><label><input type="checkbox" name="gIDInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$g->getGroupID()?>" <?php if ($assignment->getGroupsAllowedPermission() == 'A' || in_array($g->getGroupID(), $assignment->getGroupsAllowedArray())) {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$g->getGroupDisplayName()?></span></label></li>
		<?php 
}
    ?>
	</ul>
	</div>
</div>

<?php 
}
}
    ?>


<?php if (count($excluded) > 0) {
    ?>

<h3><?=t('Who can\'t assign what?')?></h3>

<?php foreach ($excluded as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="clearfix">
	<label><?=$entity->getAccessEntityLabel()?></label>
	<div class="input">
	<?=$form->select('groupsExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Groups'), 'C' => t('Custom')), $assignment->getGroupsAllowedPermission())?><br/><br/>
	<ul class="inputs-list" <?php if ($assignment->getGroupsAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php foreach ($groups as $g) {
    ?>
			<li><label><input type="checkbox" name="gIDExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$g->getGroupID()?>" <?php if (in_array($g->getGroupID(), $assignment->getGroupsAllowedArray())) {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$g->getGroupDisplayName()?></span></label></li>
		<?php 
}
    ?>
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
			$(this).parent().find('ul.inputs-list').show();
		} else {
			$(this).parent().find('ul.inputs-list').hide();
		}
	});
});
</script>