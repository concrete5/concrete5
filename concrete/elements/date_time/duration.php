<?php
defined('C5_EXECUTE') or die('Access Denied.');

$repeatDays = array();
for ($i = 1; $i <= 30; ++$i) {
    $repeatDays[$i] = $i;
}
$repeatWeeks = array();
for ($i = 1; $i <= 30; ++$i) {
    $repeatWeeks[$i] = $i;
}
$repeatMonths = array();
for ($i = 1; $i <= 12; ++$i) {
    $repeatMonths[$i] = $i;
}

$values = array();
if (Punic\Calendar::has12HoursClock()) {
    foreach (array(12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11) as $hour) {
        $values[] = $hour . ':00am';
        $values[] = $hour . ':05am';
        $values[] = $hour . ':10am';
        $values[] = $hour . ':15am';
        $values[] = $hour . ':20am';
        $values[] = $hour . ':25am';
        $values[] = $hour . ':30am';
        $values[] = $hour . ':35am';
        $values[] = $hour . ':40am';
        $values[] = $hour . ':45am';
        $values[] = $hour . ':50am';
        $values[] = $hour . ':55am';
    }
    foreach (array(12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11) as $hour) {
        $values[] = $hour . ':00pm';
        $values[] = $hour . ':05pm';
        $values[] = $hour . ':10pm';
        $values[] = $hour . ':15pm';
        $values[] = $hour . ':20pm';
        $values[] = $hour . ':25pm';
        $values[] = $hour . ':30pm';
        $values[] = $hour . ':35pm';
        $values[] = $hour . ':40pm';
        $values[] = $hour . ':45pm';
        $values[] = $hour . ':50pm';
        $values[] = $hour . ':55pm';
    }
} else {
    foreach (range(0, 23) as $hour) {
        foreach (range(0, 55, 5) as $minute) {
            $values[] = $hour . ':' . substr('0' . $minute, -2);
        }
    }
}

$repeats = array(
    '' => t('** Options'),
    'daily' => t('Every Day'),
    'weekly' => t('Every Week'),
    'monthly' => t('Every Month'),
);

$weekDays = \Punic\Calendar::getSortedWeekdays('wide');

?>

<script type="text/template" data-template="duration-wrapper">

    <div class="ccm-date-time-duration-wrapper">

        <a href="javascript:void(0)" data-delete="duration" class="ccm-date-time-duration-delete icon-link"><i class="fa fa-minus-circle"></i></a>

        <input type="hidden" name="<%=options.namespace%>_repetitionSetID[]" value="<%=repetition.setID%>">
        <input type="hidden" name="<%=options.namespace%>_repetitionID_<%=repetition.setID%>" value="<%=repetition.repetitionID%>">

        <div class="form-group">
            <div class="row">
                <div class="col-sm-6 ccm-date-time-date-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="control-label"><?= tc('Start date', 'From')  ?></label> <i
                                class="fa fa-info-circle launch-tooltip"
                                title="<?php echo t('Choose Repeat Event and choose a frequency to make this event recurring.') ?>"></i>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" data-column="date">
                            <input type="text" class="form-control" name="<%=options.namespace%>_pdStartDate_pub_<%=repetition.setID%>" value="<%=repetition.pdStartDate%>">
                            <input type="hidden" class="form-control" name="<%=options.namespace%>_pdStartDate_<%=repetition.setID%>" value="<%=repetition.pdStartDate%>">
                        </div>
                        <div class="col-sm-6">
                            <select class="form-control" name="<%=options.namespace%>_pdStartDateSelectTime_<%=repetition.setID%>" data-select="start-time">
                                <?php foreach ($values as $value) { ?>
                                    <option value="<?= $value ?>" <% if (repetition.pdStartDateSelectTime == '<?=$value?>') { %>selected<% } %>><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 ccm-date-time-date-group">
                    <div class="row">
                        <div class="col-sm-12">
                            <label class="control-label"><?= tc('End date', 'To') ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" data-column="date">
                            <input type="text" class="form-control" name="<%=options.namespace%>_pdEndDate_pub_<%=repetition.setID%>" value="<%=repetition.pdEndDate%>">
                            <input type="hidden" class="form-control" name="<%=options.namespace%>_pdEndDate_<%=repetition.setID%>" value="<%=repetition.pdEndDate%>">
                        </div>
                        <div class="col-sm-6">
                            <select class="form-control" name="<%=options.namespace%>_pdEndDateSelectTime_<%=repetition.setID%>" data-select="end-time">
                                <?php foreach ($values as $value) { ?>
                                    <option value="<?= $value ?>" <% if (repetition.pdEndDateSelectTime == '<?=$value?>') { %>selected<% } %>><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
            </div>
        </div>

    </div>

    <div class="form-group-highlight">

        <div data-wrapper="duration-repeat" style="display: none">

            <div class="row">
                <div class="col-sm-3">
                    <label><input name="<%=options.namespace%>_pdStartDateAllDayActivate_<%=repetition.setID%>" <% if (repetition.pdStartDateAllDay) { %>checked<% } %> value="1" type="checkbox"> <?= t("All Day") ?></label>
                </div>
                <div class="col-sm-3">
                    <% if (options.allowRepeat) { %>
                        <label><input name="<%=options.namespace%>_pdRepeat_<%=repetition.setID%>" value="1" <% if (repetition.pdRepeats) { %>checked<% } %> type="checkbox"> <?= t("Repeat Event") ?></label>
                    <% } %>
                </div>
                <div class="col-sm-6">
                    <div class="pull-right text-muted">
                        <%=repetition.timezone.timezone%>
                    </div>
                </div>
            </div>

        </div>

        <div data-wrapper="duration-repeat-selector" style="display: none">

            <br/>

            <div class="form-group">
                <label for="pdRepeatPeriod" class="control-label"><?= t('Repeats') ?></label>
                <div class="">
                    <select class="form-control" name="<%=options.namespace%>_pdRepeatPeriod_<%=repetition.setID%>">
                        <?php foreach ($repeats as $key => $value) { ?>
                            <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriod == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div data-wrapper="duration-dates-repeat-daily" style="display: none">

                <div class="form-group">
                    <label for="<%=options.namespace%>_pdRepeatPeriodDaysEvery_<%=repetition.setID%>" class="control-label"><?= t('Repeat every') ?></label>
                    <div class="">
                        <div class="form-inline">
                            <select class="form-control" style="width: 60px" name="<%=options.namespace%>_pdRepeatPeriodDaysEvery_<%=repetition.setID%>">
                                <?php foreach ($repeatDays as $key => $value) { ?>
                                    <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriodDaysEvery == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                                <?php } ?>
                            </select>
                            <?= t('days') ?>
                        </div>
                    </div>
                </div>

            </div>

            <div data-wrapper="duration-dates-repeat-monthly" style="display: none">


                <div class="form-group">
                    <label for="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" class="control-label"><?= t('Repeat By') ?></label>
                    <div class="form-group">
                        <div class="radio">
                            <label>
                                <input type="radio" name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" <% if (repetition.pdRepeatPeriodMonthsRepeatBy == 'month') { %>checked<% } %> value="month">
                                <?= t('Same date.')?>
                            </label>
                            <div class="help-block">
                                <?=t('e.g. every Dec 25th...')?>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="radio">
                            <label>
                                <input type="radio" name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" <% if (repetition.pdRepeatPeriodMonthsRepeatBy == 'week') { %>checked<% } %> value="week">
                                <?= t('Same type of day from start of the month.')?>
                            </label>
                            <div class="help-block">
                                <?=t('e.g. every third Thursday...')?>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="radio">
                            <label>
                                <input type="radio" name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" <% if (repetition.pdRepeatPeriodMonthsRepeatBy == 'lastweekday') { %>checked<% } %> value="lastweekday">
                                <?= t('The last type of day in the month.') ?>
                            </label>
                            <div>
                                <select name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatLastDay_<%=repetition.setID%>" class="form-control">
                                    <?php foreach($weekDays as $weekDay) { ?>
                                        <option value="<?=$weekDay['id']?>" <% if (repetition.pdRepeatPeriodMonthsRepeatLastDay == '<?=$weekDay['id']?>') { %>selected<% } %>><?=h($weekDay['name'])?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="help-block">
                                <?=t('e.g. every last Friday...')?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="<%=options.namespace%>_pdRepeatPeriodMonthsEvery_<%=repetition.setID%>" class="control-label"><?= t('Repeat every') ?></label>
                    <div class="">
                        <div class="form-inline">
                            <select class="form-control" style="width: 60px" name="<%=options.namespace%>_pdRepeatPeriodMonthsEvery_<%=repetition.setID%>">
                                <?php foreach ($repeatMonths as $key => $value) { ?>
                                    <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriodMonthsEvery == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                                <?php } ?>
                            </select>
                            <?= t('months') ?>
                        </div>
                    </div>
                </div>

            </div>


            <div data-wrapper="duration-dates-repeat-weekly" style="display: none">


                <div data-wrapper="duration-repeat-weekly-dow" style="display: none">

                    <div class="form-group">
                        <label class="control-label"><?= tc('Date', 'On') ?></label>
                        <div class="">
                            <?php foreach ($weekDays as $weekDay) { ?>
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" name="<%=options.namespace%>_pdRepeatPeriodWeeksDays_<%=repetition.setID%>[]" value="<?=$weekDay['id']?>" <% if (_.contains(repetition.pdRepeatPeriodWeekDays, '<?=$weekDay['id']?>')) { %> checked <% } %>> <?=h($weekDay['name'])?>
                                    </label>
                                </div>
                            <?php } ?>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label for="<%=options.namespace%>_pdRepeatPeriodWeeksEvery_<%=repetition.setID%>" class="control-label"><?= t('Repeat every') ?></label>
                    <div class="">
                        <div class="form-inline">
                            <select class="form-control" style="width: 60px" name="<%=options.namespace%>_pdRepeatPeriodWeeksEvery_<%=repetition.setID%>">
                                <?php foreach ($repeatWeeks as $key => $value) { ?>
                                    <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriodWeeksEvery == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                                <?php } ?>
                            </select>
                            <?= t('weeks') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div data-wrapper="duration-repeat-dates" style="display: none">


                <div class="form-group">
                    <label class="control-label"><?= t('Starts On') ?></label>
                    <div class="">
                        <input type="text" class="form-control" disabled="disabled" value="" name="<%=options.namespace%>_pdStartRepeatDate_<%=repetition.setID%>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pdEndRepeatDate" class="control-label"><?= t('Ends') ?></label>
                    <div class="">
                        <div class="radio">
                            <label>
                                <input type="radio" name="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>" value="" <% if (!repetition.pdEndRepeatDate) { %>checked <% } %>> <?=t('Never') ?>
                            </label>
                        </div>
                        <div class="radio">
                            <label>
                                <input type="radio" name="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>" value="date" <% if (repetition.pdEndRepeatDate == 'date') { %>checked <% } %>>
                                <input type="text" class="form-control" name="<%=options.namespace%>_pdEndRepeatDateSpecific_pub_<%=repetition.setID%>" value="<%=repetition.pdEndRepeatDateSpecific%>">
                                <input type="hidden" class="form-control" name="<%=options.namespace%>_pdEndRepeatDateSpecific_<%=repetition.setID%>" value="<%=repetition.pdEndRepeatDateSpecific%>">
                            </label>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <hr/>

    </div>

</script>
