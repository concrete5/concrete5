<?php
// Arguments
/* Concrete\Core\Foundation\Repetition\RepetitionInterface|null $pd */

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Permission\Duration;
use Punic\Calendar;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Application::getFacadeApplication();

$service = $app->make('helper/date');
/* @var Concrete\Core\Localization\Service\Date $service */
$form = $app->make('helper/form');
/* @var Concrete\Core\Form\Service\Form $form */
$dt = $app->make('helper/form/date_time');
/* @var Concrete\Core\Form\Service\Widget\DateTime $dt */

$pdRepeatPeriod = '';
$pdRepeatPeriodDaysEvery = 1;
$pdRepeatPeriodWeeksEvery = 1;
$pdRepeatPeriodMonthsEvery = 1;
$pdRepeatPeriodMonthsRepeatBy = 'month';
$pdEndRepeatDate = '';

if (isset($pd) && is_object($pd)) {
    $pdStartDate = $pd->getStartDate();
    $pdStartDateAllDay = $pd->isStartDateAllDay();
    if ($pdStartDate && $pdStartDateAllDay) {
        // We need to assume that the date is in the user timezone, otherwise we risk to change day (because the $service->datetime() method will convert the timezone
        $pdStartDate = $service->toDateTime($pdStartDate, 'system', 'user')->format('Y-m-d H:i:s');
    }
    $pdEndDate = $pd->getEndDate();
    $pdEndDateAllDay = $pd->isEndDateAllDay();
    if ($pdEndDate && $pdEndDateAllDay) {
        // We need to assume that the date is in the user timezone, otherwise we risk to change day (because the $service->datetime() method will convert the timezone
        $pdEndDate = $service->toDateTime($pdEndDate, 'system', 'user')->format('Y-m-d H:i:s');
    }
    $pdRepeats = $pd->repeats();
    $pdRepeatPeriodWeekDays = $pd->getRepeatPeriodWeekDays();
    switch ($pd->getRepeatPeriod()) {
        case $pd::REPEAT_DAILY:
            $pdRepeatPeriod = 'daily';
            $pdRepeatPeriodDaysEvery = $pd->getRepeatPeriodEveryNum();
            break;
        case $pd::REPEAT_WEEKLY:
            $pdRepeatPeriod = 'weekly';
            $pdRepeatPeriodWeeksEvery = $pd->getRepeatPeriodEveryNum();
            break;
        case $pd::REPEAT_MONTHLY:
            $pdRepeatPeriod = 'monthly';
            $pdRepeatPeriodMonthsEvery = $pd->getRepeatPeriodEveryNum();
            break;
    }
    $pdRepeatPeriodMonthsRepeatLastDay = $pd->getRepeatMonthLastWeekday();
    switch ($pd->getRepeatMonthBy()) {
        case Duration::MONTHLY_REPEAT_MONTHLY:
            $pdRepeatPeriodMonthsRepeatBy = 'month';
            break;
        case Duration::MONTHLY_REPEAT_WEEKLY:
            $pdRepeatPeriodMonthsRepeatBy = 'week';
            break;
        case Duration::MONTHLY_REPEAT_LAST_WEEKDAY:
            $pdRepeatPeriodMonthsRepeatBy = 'lastweekday';
            break;
    }
    $pdEndRepeatDateSpecific = $pd->getRepeatPeriodEnd();
    if ($pdEndRepeatDateSpecific) {
        $pdEndRepeatDate = 'date';
    } else {
        $pdEndRepeatDateSpecific = null;
    }    
} else {
    $pdStartDate = '';
    $pdEndDate = '';
    $pdRepeats = false;
    $pdStartDateAllDay = false;
    $pdEndDateAllDay = false;
    $pdRepeatPeriodWeekDays = [];
    $pdRepeatPeriodMonthsRepeatLastDay = 0;
    $pdEndRepeatDateSpecific = null;
}
$weekdays = [];
$wd = Calendar::getFirstWeekday();
for ($i = 0; $i < 7; ++$i) {
    $weekdays[$wd] = Calendar::getWeekdayName($wd, 'wide', '', true);
    $wd = ($wd + 1) % 7;
}
?>
<style type="text/css">
    #ccm-permissions-access-entity-dates .ccm-activate-date-time {
        margin-right: 8px;
    }
</style>
<div id="ccm-permissions-access-entity-dates">
    <div class="form-group">
        <label for="pdStartDate_activate" class="control-label"><?= tc('Start date', 'From') ?></label>
        <div>
            <?= $dt->datetime('pdStartDate', $pdStartDate, true) ?>
            <div class="checkbox">
                <label><?= $form->checkbox('pdStartDateAllDayActivate', 1, $pdStartDateAllDay) ?> <?= t('All Day') ?></label>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="pdEndDate_activate" class="control-label"><?= tc('End date', 'To') ?></label>
        <div>
            <?= $dt->datetime('pdEndDate', $pdEndDate, true) ?>
            <div class="checkbox"><label><?= $form->checkbox('pdEndDateAllDayActivate', 1, $pdEndDateAllDay) ?> <?= t('All Day') ?></label></div>
        </div>
    </div>
</div>
<div id="ccm-permissions-access-entity-repeat" style="display: none">
    <div class="form-group">
        <div>
            <div class="checkbox"><label><?= $form->checkbox('pdRepeat', 1, $pdRepeats) ?> <?= t('Repeat...') ?></label></div>
        </div>
    </div>
</div>
<div id="ccm-permissions-access-entity-repeat-selector" style="display: none">
    <div class="form-group">
        <label for="pdRepeatPeriod" class="control-label"><?= t('Repeats')?></label>
        <div>
            <?= $form->select(
                'pdRepeatPeriod',
                [
                    '' => t('** Options'),
                    'daily' => t('Every Day'),
                    'weekly' => t('Every Week'),
                    'monthly' => t('Every Month'),
                ],
                $pdRepeatPeriod
            ) ?>
        </div>
    </div>
    <div id="ccm-permissions-access-entity-dates-repeat-daily" style="display: none">
        <div class="form-group">
            <label for="pdRepeatPeriodDaysEvery" class="control-label"><?= t('Repeat every') ?></label>
            <div>
                <div class="form-inline">
                    <?php
                    $range = range(1, 30);
                    echo $form->select(
                        'pdRepeatPeriodDaysEvery',
                        array_combine($range, $range),
                        $pdRepeatPeriodDaysEvery,
                        ['style' => 'width: 60px']
                    ); ?>
                    <?= t('days') ?>
                </div>
            </div>
        </div>
    </div>
    <div id="ccm-permissions-access-entity-dates-repeat-monthly" style="display: none">
        <div class="form-group">
            <label for="pdRepeatPeriodMonthsRepeatBy" class="control-label"><?= t('Repeat By') ?></label>
            <div>
                <div class="radio">
                    <label>
                        <?= $form->radio('pdRepeatPeriodMonthsRepeatBy', 'month', $pdRepeatPeriodMonthsRepeatBy) ?>
                        <?= t('Day of Month') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <?= $form->radio('pdRepeatPeriodMonthsRepeatBy', 'week', $pdRepeatPeriodMonthsRepeatBy) ?>
                        <?= t('Day of Week') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <?= $form->radio('pdRepeatPeriodMonthsRepeatBy', 'lastweekday', $pdRepeatPeriodMonthsRepeatBy) ?>
                        <?= t('The last ') ?>
                        <select name="pdRepeatPeriodMonthsRepeatLastDay">
                            <?php
                            foreach ($weekdays as $key => $value) {
                                ?><option value="<?= $key?>"<?= ($key == $pdRepeatPeriodMonthsRepeatLastDay) ? ' selected="selected"' : ''?>><?= $value ?></option><?php
                            }
                            ?>
                        </select>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="pdRepeatPeriodMonthsEvery" class="control-label"><?= t('Repeat every') ?></label>
            <div>
                <div class="form-inline">
                    <?php
                    $range = range(1, 12);
                    echo $form->select(
                        'pdRepeatPeriodMonthsEvery',
                        array_combine($range, $range),
                        $pdRepeatPeriodMonthsEvery,
                        ['style' => 'width: 60px']
                    );
                    ?>
                    <?= t('months') ?>
                </div>
            </div>
        </div>
    </div>
    <div id="ccm-permissions-access-entity-dates-repeat-weekly" style="display: none">
        <div id="ccm-permissions-access-entity-dates-repeat-weekly-dow" style="display: none">
            <div class="form-group">
                <label class="control-label"><?= t('On') ?></label>
                <div>
                    <?php
                    foreach ($weekdays as $key => $value) {
                        ?>
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="pdRepeatPeriodWeeksDays[]" value="<?= $key ?>"<?= in_array($key, $pdRepeatPeriodWeekDays) ? ' checked="checked"' : '' ?> />
                                <?= $value ?>
                            </label>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label for="pdRepeatPeriodWeeksEvery" class="control-label"><?= t('Repeat every') ?></label>
            <div>
                <div class="form-inline">
                    <?php
                    $range = range(1, 30);
                    echo $form->select(
                        'pdRepeatPeriodWeeksEvery',
                        array_combine($range, $range),
                        $pdRepeatPeriodWeeksEvery,
                        ['style' => 'width: 60px']
                    ) ?>
                    <?= t('weeks') ?>
                </div>
            </div>
        </div>
    </div>
    <div id="ccm-permissions-access-entity-dates-repeat-dates" style="display: none">
        <div class="form-group">
            <label class="control-label"><?= t('Starts On') ?></label>
            <div>
                <input type="text" class="form-control" disabled="disabled" value="" name="pdStartRepeatDate"/>
            </div>
        </div>
        <div class="form-group">
            <label for="pdEndRepeatDate" class="control-label"><?= t('Ends') ?></label>
            <div>
                <div class="radio"><label><?= $form->radio('pdEndRepeatDate', '', $pdEndRepeatDate) ?> <?= t('Never') ?></label></div>
                <div class="radio"><label><?= $form->radio('pdEndRepeatDate', 'date', $pdEndRepeatDate) ?> <?= $dt->date('pdEndRepeatDateSpecific', $pdEndRepeatDateSpecific) ?></label></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
var ccm_accessEntityCalculateRepeatOptions = function () {
    // get the difference between start date and end date
    if (!$("#pdStartDate_activate").is(':checked')) {
        return false;
    }

    var sdf = ($("#pdStartDate_dt_pub").datepicker('option', 'altFormat'));
    var sdfr = $.datepicker.parseDate(sdf, $("#pdStartDate_dt").val());
    if (!sdfr) {
        return;
    }
    var edf = ($("#pdEndDate_dt_pub").datepicker('option', 'altFormat'));
    var edfr = $.datepicker.parseDate(edf, $("#pdEndDate_dt").val());
    if (!edfr) {
        return;
    }
    var sh = $("select[name=pdStartDate_h]").val();
    var eh = $("select[name=pdEndDate_h]").val();
    if ($("select[name=pdStartDate_a]").val() == 'PM' && (sh < 12)) {
        sh = parseInt(sh) + 12;
    } else if (sh == 12 && $("select[name=pdStartDate_a]").val() == 'AM') {
        sh = 0;
    }
    if ($("select[name=pdEndDate_a]").val() == 'PM' && (eh < 12)) {
        eh = parseInt(eh) + 12;
    } else if (eh == 12 && $("select[name=pdEndDate_a]").val() == 'AM') {
        eh = 0;
    }
    var startDate = new Date(sdfr.getFullYear(), sdfr.getMonth(), sdfr.getDate(), sh, $('select[name=pdStartDate_m]').val(), 0);
    var endDate = new Date(edfr.getFullYear(), edfr.getMonth(), edfr.getDate(), eh, $('select[name=pdEndDate_m]').val(), 0);
    var difference = ((endDate.getTime() / 1000) - (startDate.getTime() / 1000));
    if (difference >= 60 * 60 * 24) {
        $('select[name=pdRepeatPeriod] option[value=daily]').attr('disabled', true);
        $("#ccm-permissions-access-entity-dates-repeat-weekly-dow").hide();
    } else {
        $('select[name=pdRepeatPeriod] option[value=daily]').attr('disabled', false);
        $("#ccm-permissions-access-entity-dates-repeat-weekly-dow").show();
    }
    $('input[name=pdStartRepeatDate]').val($("#pdStartDate_dt_pub").val());
    switch (sdfr.getDay()) {
        case 0:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=0]").attr('checked', true);
            break;
        case 1:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=1]").attr('checked', true);
            break;
        case 2:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=2]").attr('checked', true);
            break;
        case 3:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=3]").attr('checked', true);
            break;
        case 4:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=4]").attr('checked', true);
            break;
        case 5:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=5]").attr('checked', true);
            break;
        case 6:
            $("#ccm-permissions-access-entity-dates-repeat-weekly-dow input[value=6]").attr('checked', true);
            break;
    }
};
var ccm_accessEntityCheckRepeat = function () {
    if ($('input[name=pdRepeat]').is(':checked')) {
        $("#ccm-permissions-access-entity-repeat-selector").show();
    } else {
        $("#ccm-permissions-access-entity-repeat-selector").hide();
    }
};
var ccm_accessEntityOnActivateDates = function () {
    if ($("#pdStartDate_activate").is(':checked') || $("#pdEndDate_activate").is(':checked')) {
        ccm_accessEntityCalculateRepeatOptions();
    }
    if ($("#pdStartDate_activate").is(':checked') && $("#pdEndDate_activate").is(':checked')) {
        $("#ccm-permissions-access-entity-repeat").show();
    } else {
        $("#ccm-permissions-access-entity-repeat").hide();
    }
    if ($("#pdStartDate_activate").is(':checked')) {
        $('#pdStartDateAllDayActivate').attr('disabled', false);
    } else {
        $('input[name=pdStartDateAllDayActivate]').attr('disabled', true);
    }
    if ($("#pdEndDate_activate").is(':checked')) {
        $('#pdEndDateAllDayActivate').attr('disabled', false);
    } else {
        $('input[name=pdEndDateAllDayActivate]').attr('disabled', true);
    }

    if ($("input[name=pdStartDateAllDayActivate]").is(':checked')) {
        $('span#pdStartDate_tw').hide();
    } else {
        $('span#pdStartDate_tw').show();
    }

    if ($("input[name=pdEndDateAllDayActivate]").is(':checked')) {
        $('span#pdEndDate_tw').hide();
    } else {
        $('span#pdEndDate_tw').show();
    }
};
var ccm_accessEntityOnRepeatPeriodChange = function () {
    $("#ccm-permissions-access-entity-dates-repeat-daily").hide();
    $("#ccm-permissions-access-entity-dates-repeat-weekly").hide();
    $("#ccm-permissions-access-entity-dates-repeat-monthly").hide();
    if ($('select[name=pdRepeatPeriod]').val() != '') {
        $("#ccm-permissions-access-entity-dates-repeat-" + $('select[name=pdRepeatPeriod]').val()).show();
        $("#ccm-permissions-access-entity-dates-repeat-dates").show();
    }
};
var ccm_accessEntityCalculateRepeatEnd = function () {
    if ($('input[name=pdEndRepeatDate]:checked').val() == 'date') {
        $("#ccm-permissions-access-entity-dates-repeat-dates .ccm-input-date-wrapper input").attr('disabled', false);
    } else {
        $("#ccm-permissions-access-entity-dates-repeat-dates .ccm-input-date-wrapper input").attr('disabled', true);
    }
};
$(function () {
    $("#ccm-permissions-access-entity-dates input[type=checkbox]").click(function () {
        ccm_accessEntityOnActivateDates();
    });

    $("select[name=pdRepeatPeriod]").change(function () {
        ccm_accessEntityOnRepeatPeriodChange();
    });

    $("input[name=pdRepeat]").click(function () {
        ccm_accessEntityCheckRepeat();
    });

    $("#ccm-permissions-access-entity-dates span.ccm-input-date-wrapper input, #ccm-permissions-access-entity-dates span.ccm-input-time-wrapper select").change(function () {
        ccm_accessEntityCalculateRepeatOptions();
    });
    $("#ccm-permissions-access-entity-dates-repeat-dates input.ccm-input-date").attr('disabled', true);
    $('input[name=pdEndRepeatDate]').change(function () {
        ccm_accessEntityCalculateRepeatEnd();
    });
    ccm_accessEntityCalculateRepeatOptions();
    ccm_accessEntityOnActivateDates();
    ccm_accessEntityCheckRepeat();
    ccm_accessEntityOnRepeatPeriodChange();
    ccm_accessEntityCalculateRepeatEnd();
});
</script>
