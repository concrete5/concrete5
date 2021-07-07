<?php
defined('C5_EXECUTE') or die("Access Denied.");

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
                            <label class="control-label form-label"><?= tc('Start date', 'From')  ?></label> <i
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
                            <select name="<%=options.namespace%>_pdStartDateSelectTime_<%=repetition.setID%>"  class="form-select" data-select="start-time">
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
                            <label class="control-label form-label"><?= tc('End date', 'To') ?></label>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6" data-column="date">
                            <input type="text" class="form-control" name="<%=options.namespace%>_pdEndDate_pub_<%=repetition.setID%>" value="<%=repetition.pdEndDate%>">
                            <input type="hidden" class="form-control" name="<%=options.namespace%>_pdEndDate_<%=repetition.setID%>" value="<%=repetition.pdEndDate%>">
                        </div>
                        <div class="col-sm-6">
                            <select name="<%=options.namespace%>_pdEndDateSelectTime_<%=repetition.setID%>" class="form-select" data-select="end-time">
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
                    <div class="float-end text-muted">
                        <%=repetition.timezone.timezone%>
                    </div>
                </div>
            </div>

        </div>

        <div data-wrapper="duration-repeat-selector" style="display: none">

            <br/>

            <div class="form-group">
                <label for="pdRepeatPeriod" class="control-label form-label"><?= t('Repeats') ?></label>
                <div class="">
                    <select class="form-select" name="<%=options.namespace%>_pdRepeatPeriod_<%=repetition.setID%>">
                        <?php foreach ($repeats as $key => $value) { ?>
                            <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriod == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>

            <div data-wrapper="duration-dates-repeat-daily" style="display: none">

                <div class="form-group">
                
                    <label for="<%=options.namespace%>_pdRepeatPeriodDaysEvery_<%=repetition.setID%>" class="control-label form-label"><?= t('Repeat every') ?></label>

                    <div class="input-group">
                    
                        <select class="form-select" style="width: 75px; flex-shrink: 1; flex-grow: 0" name="<%=options.namespace%>_pdRepeatPeriodDaysEvery_<%=repetition.setID%>">
                            <?php foreach ($repeatDays as $key => $value) { ?>
                                <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriodDaysEvery == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                            <?php } ?>
                        </select>

                        
                        <div class="input-group-text">
                            <?= t('days') ?>
                        </div>
                        
                    </div>
                </div>

            </div>

            <div data-wrapper="duration-dates-repeat-monthly" style="display: none">


                <div class="form-group">
                    <label for="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" class="control-label form-label"><?= t('Repeat By') ?></label>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>_date" name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" <% if (repetition.pdRepeatPeriodMonthsRepeatBy == 'month') { %>checked<% } %> value="month">
                            <label class="form-check-label" for="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>_date">
                                <?= t('Same date.')?>
                            </label>
                            <div class="form-text">
                                <?=t('e.g. every Dec 25th...')?>
                            </div>

                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>_day" name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" <% if (repetition.pdRepeatPeriodMonthsRepeatBy == 'week') { %>checked<% } %> value="week">
                            <label class="form-check-label" for="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>_day">
                                <?= t('Same type of day from start of the month.')?>
                            </label>
                            <div class="form-text">
                                <?=t('e.g. every third Thursday...')?>
                            </div>
                        </div>

                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>_lastday" name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>" <% if (repetition.pdRepeatPeriodMonthsRepeatBy == 'lastweekday') { %>checked<% } %> value="lastweekday">
                            <label class="form-check-label" for="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatBy_<%=repetition.setID%>_lastday">
                                <?= t('The last type of day in the month.') ?>
                            </label>
                            <div>
                                <select name="<%=options.namespace%>_pdRepeatPeriodMonthsRepeatLastDay_<%=repetition.setID%>" class="form-select">
                                    <?php foreach($weekDays as $weekDay) { ?>
                                        <option value="<?=$weekDay['id']?>" <% if (repetition.pdRepeatPeriodMonthsRepeatLastDay == '<?=$weekDay['id']?>') { %>selected<% } %>><?=h($weekDay['name'])?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-text">
                                <?=t('e.g. every last Friday...')?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">

                    <label for="<%=options.namespace%>_pdRepeatPeriodMonthsEvery_<%=repetition.setID%>" class="control-label form-label"><?= t('Repeat every') ?></label>

                    <div class="input-group">
                    
                        <select class="form-select" style="width: 75px; flex-shrink:1; flex-grow: 0" name="<%=options.namespace%>_pdRepeatPeriodMonthsEvery_<%=repetition.setID%>" id="<%=options.namespace%>_pdRepeatPeriodMonthsEvery_<%=repetition.setID%>_months">
                            <?php foreach ($repeatMonths as $key => $value) { ?>
                                <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriodMonthsEvery == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                            <?php } ?>
                        </select>

                        <div class="input-group-text">
                            <label for="<%=options.namespace%>_pdRepeatPeriodMonthsEvery_<%=repetition.setID%>_months">
                            <?= t('months') ?>
                            </label>
                        </div>
                    </div>

                </div>

            </div>


            <div data-wrapper="duration-dates-repeat-weekly" style="display: none">


                <div data-wrapper="duration-repeat-weekly-dow" style="display: none">

                    <div class="form-group">
                        <label class="control-label form-label"><?= tc('Date', 'On') ?></label>
                        <div class="">
                            <?php
                            $i = 0;
                            foreach ($weekDays as $weekDay) { ?>
                                <div class="form-check">

                                    <input type="checkbox" class="form-check-input" id="<%=options.namespace%>_pdRepeatPeriodWeeksDays_<%=repetition.setID%>_wkday<?=$i?>"  name="<%=options.namespace%>_pdRepeatPeriodWeeksDays_<%=repetition.setID%>[]" value="<?=$weekDay['id']?>">
                                    <label class="form-check-label" for="<%=options.namespace%>_pdRepeatPeriodWeeksDays_<%=repetition.setID%>_wkday<?=$i?>">
                                        <?=h($weekDay['name'])?>
                                    </label>
                                </div>
                            <?php $i++;} ?>
                        </div>
                    </div>

                </div>

                <div class="form-group">
                    <label for="<%=options.namespace%>_pdRepeatPeriodWeeksEvery_<%=repetition.setID%>" class="control-label form-label"><?= t('Repeat every') ?></label>

                    <div class="input-group">

                        <select class="form-select" style="width: 75px; flex-shrink:1; flex-grow: 0" name="<%=options.namespace%>_pdRepeatPeriodWeeksEvery_<%=repetition.setID%>">
                            <?php foreach ($repeatWeeks as $key => $value) { ?>
                                <option value="<?= $key ?>" <% if (repetition.pdRepeatPeriodWeeksEvery == '<?=$key?>') { %>selected<% } %>><?= $value ?></option>
                            <?php } ?>
                        </select>
                        
                        <div class="input-group-text">
                            <label>
                                <?= t('weeks') ?>
                            </label>
                        </div>

                    </div>
                </div>
            </div>

            <div data-wrapper="duration-repeat-dates" style="display: none">


                <div class="form-group">
                    <label class="control-label form-label"><?= t('Starts On') ?></label>
                    <div class="">
                        <input type="text" class="form-control" disabled="disabled" value="" name="<%=options.namespace%>_pdStartRepeatDate_<%=repetition.setID%>"/>
                    </div>
                </div>

                <div class="form-group">

                    <label for="pdEndRepeatDate" class="control-label form-label"><?= t('Ends') ?></label>

                    <div class="">

                        <div class="form-check">

                            <input type="radio" class="form-check-input" id="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>_never" name="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>" value="" <% if (!repetition.pdEndRepeatDate) { %>checked <% } %>>
                            
                            <label class="form-check-label" for="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>_never">
                                <?=t('Never') ?>
                            </label>
                            
                        </div>

                        <div class="row row-cols-auto g-0 align-items-center mt-sm-2">

                            <div class="col-auto">
                                <input type="radio" id="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>_specific" name="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>" value="date" <% if (repetition.pdEndRepeatDate == 'date') { %>checked <% } %>>
                            </div>

                            <div class="col-auto">
                                <label for="<%=options.namespace%>_pdEndRepeatDate_<%=repetition.setID%>_specific" class="pl-sm-2">
                                    <input type="text" class="form-control" name="<%=options.namespace%>_pdEndRepeatDateSpecific_pub_<%=repetition.setID%>" value="<%=repetition.pdEndRepeatDateSpecific%>">
                                    <input type="hidden" class="form-control" name="<%=options.namespace%>_pdEndRepeatDateSpecific_<%=repetition.setID%>" value="<%=repetition.pdEndRepeatDateSpecific%>">
                                </label>
                            </div>

                        </div>

                    </div>
                </div>

            </div>

        </div>

        <hr/>

    </div>

</script>
