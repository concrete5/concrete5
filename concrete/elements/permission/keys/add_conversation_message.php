<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php
$included = $permissionAccess->getAccessListItems();
$form = Loader::helper('form');

if (count($included) > 0) {
    ?>

	<h3><?=t('New Message Approval')?></h3>

	<?php foreach ($included as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="form-group">
	<label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
	<?=$form->select('addMessageApproval[' . $entity->getAccessEntityID() . ']', array('A' => t('Approved'), 'U' => t('Pending')), $assignment->getNewConversationMessageApprovalStatus())?>
</div>

<?php 
}
} else {
    ?>
	<p><?=t('No access entities selected.')?></p>
<?php 
} ?>