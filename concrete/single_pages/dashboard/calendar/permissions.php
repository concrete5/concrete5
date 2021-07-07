<?php defined('C5_EXECUTE') or die("Access Denied.");
$preferences = Core::make('Concrete\Core\Calendar\Utility\Preferences');

?>

<div class="ccm-dashboard-header-buttons">
<form method="post" action="<?=$view->action('update_permissions_inheritance')?>">
    <input type="hidden" name="caID" value="<?=$calendar->getID()?>">
<?=Core::make('token')->output('update_permissions_inheritance')?>
            <?php if (!$calendar->arePermissionsSetToOverride()) {
    ?>
                <button name="update_inheritance" value="override" class="btn btn-secondary float-end"><?=t('Override Default Permissions')?></button>
            <?php 
} else {
    ?>
                <button name="update_inheritance" value="revert" class="btn btn-secondary float-end"><?=t('Remove Custom Permissions')?></button>
            <?php 
} ?>
</form>
</div>

<form method="post" enctype="multipart/form-data" action="<?=$view->action('save_permissions')?>">
    <input type="hidden" name="caID" value="<?=$calendar->getID()?>">
    <?=Core::make('token')->output('save_permissions')?>
    <fieldset>
        <div id="ccm-permission-list-form">
            <?php View::element('permission/lists/calendar', array(
                "editPermissions" => $calendar->arePermissionsSetToOverride(), "calendar" => $calendar, )); ?>
        </div>

    </fieldset>


    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">

    <?php if ($calendar->arePermissionsSetToOverride()) {
    ?>
        <a href="<?=URL::to($preferences->getPreferredViewPath(), 'view', $calendar->getID())?>" class="btn btn-secondary"><?=t('Back')?></a>
        <button type="submit" name="submit" class="btn float-end btn-primary"><?=t('Save')?></button>
    <?php 
} else {
    ?>
        <a href="<?=URL::to($preferences->getPreferredViewPath(), 'view', $calendar->getID())?>" class="btn btn-secondary"><?=t('Back')?></a>
    <?php 
} ?>

        </div>
    </div>
</form>
