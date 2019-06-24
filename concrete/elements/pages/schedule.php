<?php
defined('C5_EXECUTE') or die('Access Denied.');
$datetime = loader::helper('form/date_time');

$publishDate = '';
$publishEndDate = '';
if (isset($page) && is_object($page)) {
    $v = CollectionVersion::get($page, "RECENT");
    $publishDate = $v->getPublishDate();
    $publishEndDate = $v->getPublishEndDate();
}

$dateService = Core::make('date');
$timezone = $dateService->getUserTimeZoneID();
$timezone = $dateService->getTimezoneDisplayName($timezone);
?>

<div class="form-group form-group-last">
    <label class="control-label"><?=t('From')?></label>
    <?= $datetime->datetime('cvPublishDate', $publishDate, true, true,
        'dark-panel-calendar'); ?>
</div>
<div class="form-group form-group-last">
    <label class="control-label"><?=t('To')?></label>
    <?= $datetime->datetime('cvPublishEndDate', $publishEndDate, true, true,
        'dark-panel-calendar'); ?>
</div>

<div style="text-align: right">
    <span class="help-block"><?=t('Time Zone: %s', $timezone)?></span>
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
