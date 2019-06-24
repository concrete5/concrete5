<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php $included = $permissionAccess->getAccessListItems(); ?>
<?php $excluded = $permissionAccess->getAccessListItems(PermissionKey::ACCESS_TYPE_EXCLUDE); ?>
<?php $subscriptions = \Core::make('manager/notification/subscriptions')->getSubscriptions(); ?>
<?php $form = Loader::helper('form'); ?>


<h4><?=t('Users/Groups Receiving Notifications')?>
    <a style="float: right" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?disableDuration=1&accessType=<?=PermissionKey::ACCESS_TYPE_INCLUDE?>&pkCategoryHandle=notification" dialog-width="500" dialog-height="350" dialog-title="<?=t('Add Access Entity')?>" class="dialog-launch btn btn-sm btn-default"><?=t('Add')?></a>

</h4>



<?php

if (count($included) == 0) { ?>
    <p class="text-muted"><?=t('None.')?></p>

<?php }

foreach ($included as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="form-group" data-form-group="notification" data-access-entity="<?=$entity->getAccessEntityID()?>">


    <label class="control-label"><?=$entity->getAccessEntityLabel()?></label>

    <div style="padding-right: 30px; position: relative">
        <a href="javascript:void(0)" class="icon-link" style="position: absolute; top: 5px; right: 0px" onclick="ccm_deleteAccessEntityAssignment(<?=$entity->getAccessEntityID()?>)"><i class="fa fa-trash-o"></i></a>

        <?=$form->select('subscriptionsIncluded[' . $entity->getAccessEntityID() . ']', array('A' => t('All Subscriptions'), 'C' => t('Custom')), $assignment->getSubscriptionsAllowedPermission())?>
    </div>
	<div class="subscription-list" <?php if ($assignment->getSubscriptionsAllowedPermission() != 'C') {
    ?>style="display: none"<?php 
}
    ?>>
		<?php foreach ($subscriptions as $subscription) {
    ?>
			<div class="checkbox"><label><input type="checkbox" name="subscriptionIdentifierInclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$subscription->getSubscriptionIdentifier()?>" <?php if (in_array($subscription->getSubscriptionIdentifier(), $assignment->getSubscriptionsAllowedArray()) || $assignment->getSubscriptionsAllowedPermission() == 'A') {
    ?> checked="checked" <?php 
}
    ?> /> <span><?=$subscription->getSubscriptionName()?></span></label></div>
		<?php 
}
    ?>
	</div>
</div>


<?php
}
    ?>

<h4><?=t('Users/Groups Excluded from Notifications')?>

    <a style="float: right" href="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/permissions/access_entity?disableDuration=1&accessType=<?=PermissionKey::ACCESS_TYPE_EXCLUDE?>&pkCategoryHandle=notification" dialog-width="500" dialog-height="350" dialog-title="<?=t('Add Access Entity')?>" class="dialog-launch btn btn-sm btn-default"><?=t('Add')?></a>

</h4>

<?php

if (count($excluded) == 0) { ?>
    <p class="text-muted"><?=t('None.')?></p>

<?php }

foreach ($excluded as $assignment) {
    $entity = $assignment->getAccessEntityObject();
    ?>


<div class="form-group" data-form-group="notification">
	<label class="control-label"><?=$entity->getAccessEntityLabel()?></label>
    <div style="padding-right: 30px; position: relative">
        <a href="javascript:void(0)" class="icon-link" style="position: absolute; top: 5px; right: 0px" onclick="ccm_deleteAccessEntityAssignment(<?=$entity->getAccessEntityID()?>)"><i class="fa fa-trash-o"></i></a>

        <?=$form->select('subscriptionsExcluded[' . $entity->getAccessEntityID() . ']', array('N' => t('No Subscriptions'), 'C' => t('Custom')), $assignment->getSubscriptionsAllowedPermission())?>
        <div class="subscription-list" <?php if ($assignment->getSubscriptionsAllowedPermission() != 'C') {
        ?>style="display: none"<?php
    }
        ?>>
            <?php foreach ($subscriptions as $subscription) {
        ?>
                <div class="checkbox"><label><input type="checkbox" name="subscriptionIdentifierExclude[<?=$entity->getAccessEntityID()?>][]" value="<?=$subscription->getSubscriptionIdentifier()?>" <?php if (in_array($subscription->getSubscriptionIdentifier(), $assignment->getSubscriptionsAllowedArray()) || $assignment->getSubscriptionsAllowedPermission() == 'N') {
        ?> checked="checked" <?php
    }
        ?> /> <span><?=$subscription->getSubscriptionName()?></span></label></div>
            <?php
    }
        ?></div>
    </div>
</div>



<?php
}
    ?>

<script type="text/javascript">
$(function() {
	$("div[data-form-group=notification] select").change(function() {
		if ($(this).val() == 'C') {
			$(this).closest('[data-form-group]').find('div.subscription-list').show();
		} else {
            $(this).closest('[data-form-group]').find('div.subscription-list').hide();
		}
        $('div.ccm-dashboard-form-actions-wrapper').show();
	});
    $("div[data-form-group=notification] input").change(function() {
        $('div.ccm-dashboard-form-actions-wrapper').show();
    });
});
</script>