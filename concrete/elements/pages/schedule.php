<?php
defined('C5_EXECUTE') or die("Access Denied.");
$datetime = Loader::helper('form/date_time');

$publishDate = '';
$publishEndDate = '';
if (isset($page) && is_object($page)) {
    $v = CollectionVersion::get($page, "RECENT");
    $publishDate = $v->getPublishDate();
    $publishEndDate = $v->getPublishEndDate();

    $scheduled = CollectionVersion::get($page, "SCHEDULED");
    if (!$scheduled->isError()) {
        ?>
        <div class="alert alert-warning">
            <p><?= t("At least one version is already scheduled to publish."); ?><br>
            <?= t("This version will be scheduled to publish separately."); ?></p>
        </div>
        <?php
    }
}

$dateService = Core::make('date');
$timezone = $dateService->getUserTimeZoneID();
$timezone = $dateService->getTimezoneDisplayName($timezone);
?>

<div class="form-group form-group-last">
    <label class="control-label form-label"><?=t('From')?></label>
    <?= $datetime->datetime('cvPublishDate', $publishDate, true, true,
        'light-panel-calendar'); ?>
</div>
<div class="form-group form-group-last">
    <label class="control-label form-label"><?=t('To')?></label>
    <?= $datetime->datetime('cvPublishEndDate', $publishEndDate, true, true,
        'light-panel-calendar'); ?>
</div>

<div style="text-align: right">
    <span class="form-text help-block"><?=t('Time Zone: %s', $timezone)?></span>
</div>

<div class="dialog-buttons">
    <button type="submit" name="action" value="schedule"
            class="btn btn-primary ccm-check-in-schedule">
        <?=t('Schedule')?>
    </button>
</div>

<style type="text/css">
    div.ui-dialog button.ccm-check-in-schedule {
        float: right;
    }
</style>
