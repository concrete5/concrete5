<div class="form-group">
    <?=$form->label('calendarID', t('Calendar'))?>
    <?=$form->select('calendarID', $calendars, $calendarID, array('data-select' => 'calendar'));?>
</div>

<div class="form-group">
    <?=$form->label('eventID', t('Event'))?>
    <div data-wrapper="calendar-event-selector"><?=t('Choose a Calendar')?></div>
</div>


<script type="text/javascript">
    $(function() {
        $('select[data-select=calendar]').on('change', function() {
            if ($(this).val()) {
                $('div[data-wrapper=calendar-event-selector]').html('').concreteCalendarEventSelector({
                    inputName: '<?=$view->field('eventID')?>',
                    calendarID: $(this).val()
                    <?php if (isset($eventID) && $eventID > 0) {
    ?>,
                    eventID: '<?=$eventID?>'
                    <?php 
} ?>
                });
            } else {
                $('div[data-wrapper=calendar-event-selector]').html('<?=t('Choose a Calendar')?>');
            }
        }).trigger('change');
    });
</script>
