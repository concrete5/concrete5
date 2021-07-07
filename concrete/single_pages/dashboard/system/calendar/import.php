<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if (isset($numCalendars) && $numCalendars) { ?>

    <form method="post" action="<?=$view->action('submit')?>">
        <?=$token->output('submit')?>
        <div class="alert alert-info">
            <?=t2('%s calendar found.', '%s calendars found.', $numCalendars)?>
            <?=t("Click 'Import' to import this data into the core calendar system. This data will be added to any existing calendar data.")?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <button class="float-end btn btn-success" type="submit" ><?=t('Import')?></button>
            </div>
        </div>
    </form>

<?php } else { ?>
    <div class="alert alert-warning">
        <?=t('No calendar data found. Currently this page is only used to import calendar data from the concrete5 marketplace calendar, which is now fully integrated into the core.')?>
    </div>

<?php } ?>
