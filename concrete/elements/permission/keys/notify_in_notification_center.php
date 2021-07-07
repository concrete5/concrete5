<?php

use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Permission\Access\NotifyInNotificationCenterNotificationAccess $permissionAccess
 */

$included = $permissionAccess->getAccessListItems();
$excluded = $permissionAccess->getAccessListItems(Key::ACCESS_TYPE_EXCLUDE);
$subscriptions = app('manager/notification/subscriptions')->getSubscriptions();
$form = app('helper/form');
$resolverManager = app(ResolverManagerInterface::class);
?>

<fieldset>
    <legend>
        <?= t('Users/Groups Receiving Notifications') ?>
        <a
            class="dialog-launch btn btn-sm btn-secondary float-end"
            href="<?= h($resolverManager->resolve(['/ccm/system/permissions/access/entity']) . '?disableDuration=1&accessType=' . Key::ACCESS_TYPE_INCLUDE . '&pkCategoryHandle=notification') ?>"
            dialog-width="500"
            dialog-height="350"
            dialog-title="<?= t('Add Access Entity') ?>"
        ><?= t('Add') ?></a>
    </legend>
    <?php
    if ($included === []) {
        ?>
        <p class="text-muted"><?= t('None.') ?></p>
        <?php
    } else {
        $elementCount = 0;
        foreach ($included as $assignment) {
            $entity = $assignment->getAccessEntityObject();
            ?>
            <div class="form-group" data-form-group="notification" data-access-entity="<?= $entity->getAccessEntityID() ?>">
                <?= $form->label('', $entity->getAccessEntityLabel()) ?>
                <div class="input-group">
                    <?= $form->select('subscriptionsIncluded[' . $entity->getAccessEntityID() . ']', ['A' => t('All Subscriptions'), 'C' => t('Custom')], $assignment->getSubscriptionsAllowedPermission()) ?>
                    <a class="btn btn-outline-danger" href="javascript:void(0)" onclick="ccm_deleteAccessEntityAssignment(<?= $entity->getAccessEntityID() ?>)">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
                <div class="subscription-list<?= $assignment->getSubscriptionsAllowedPermission() != 'C' ? ' d-none"' : '' ?>">
                    <?php
                    foreach ($subscriptions as $subscription) {
                        $elementID = 'subscriptionIdentifierInclude_' . $elementCount++;
                        ?>
                        <div class="form-check">
                            <?= $form->checkbox(
                            'subscriptionIdentifierInclude[' . $entity->getAccessEntityID() . '][]',
                            $subscription->getSubscriptionIdentifier(),
                            in_array($subscription->getSubscriptionIdentifier(), $assignment->getSubscriptionsAllowedArray()) || $assignment->getSubscriptionsAllowedPermission() == 'A',
                            ['id' => $elementID]
                        ) ?>
                            <label for="<?= $elementID ?>" class="form-check-label"><?= $subscription->getSubscriptionName() ?></label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</fieldset>

<fieldset>
    <legend>
        <?= t('Users/Groups Excluded from Notifications') ?>
        <a
            href="<?= h($resolverManager->resolve(['/ccm/system/permissions/access/entity']) . '?disableDuration=1&accessType=' . Key::ACCESS_TYPE_EXCLUDE . '&pkCategoryHandle=notification') ?>"
            dialog-width="500"
            dialog-height="350"
            dialog-title="<?= t('Add Access Entity') ?>"
            class="dialog-launch btn btn-sm btn-secondary float-end"
        ><?= t('Add') ?></a>
    </legend>
    <?php
    if ($excluded === []) {
        ?>
        <p class="text-muted"><?= t('None.') ?></p>
        <?php
    } else {
        $elementCount = 0;
        foreach ($excluded as $assignment) {
            $entity = $assignment->getAccessEntityObject();
            ?>
            <div class="form-group" data-form-group="notification">
                <?= $form->label('', $entity->getAccessEntityLabel()) ?>
                <div class="input-group">
                    <?= $form->select('subscriptionsExcluded[' . $entity->getAccessEntityID() . ']', ['N' => t('No Subscriptions'), 'C' => t('Custom')], $assignment->getSubscriptionsAllowedPermission()) ?>
                    <a class="btn btn-outline-danger" href="javascript:void(0)" onclick="ccm_deleteAccessEntityAssignment(<?= $entity->getAccessEntityID() ?>)">
                        <i class="fas fa-trash"></i>
                    </a>
                    
                </div>
                <div class="subscription-list<?= $assignment->getSubscriptionsAllowedPermission() != 'C' ? ' d-none"' : '' ?>">
                    <?php
                    foreach ($subscriptions as $subscription) {
                        $elementID = 'subscriptionIdentifierExclude_' . $elementCount++;
                        ?>
                        <div class="form-check">
                            <?= $form->checkbox(
                            'subscriptionIdentifierExclude[' . $entity->getAccessEntityID() . '][]',
                            $subscription->getSubscriptionIdentifier(),
                            in_array($subscription->getSubscriptionIdentifier(), $assignment->getSubscriptionsAllowedArray()) || $assignment->getSubscriptionsAllowedPermission() == 'N',
                            ['id' => $elementID]
                        ) ?>
                            <label for="<?= $elementID ?>" class="form-check-label"><?= $subscription->getSubscriptionName() ?></label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
            <?php
        }
    }
    ?>
</fieldset>    

<script>
$(document).ready(function() {
    $("div[data-form-group=notification] select").on('change', function() {
        var $this = $(this);
        $this.closest('[data-form-group]').find('div.subscription-list').toggleClass('d-none', $this.val() !== 'C');
        $('div.ccm-dashboard-form-actions-wrapper').removeClass('d-none').show();
    });
    $("div[data-form-group=notification] input").change(function() {
        $('div.ccm-dashboard-form-actions-wrapper').removeClass('d-none').show();
    });
});
</script>
