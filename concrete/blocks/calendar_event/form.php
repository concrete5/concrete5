<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<fieldset>
    <legend><?=t('Data Source')?></legend>

    <div class="form-group">
        <label class="control-label"><?=t('Mode')?></label>
        <select class="form-control"  data-select="mode" name="mode">
            <option value="S" <?php if ($mode == 'S') {
    ?>selected<?php 
} ?>><?=t('Specific – Display details about a specific calendar event.')?></option>
            <option value="P" <?php if ($mode == 'P') {
    ?>selected<?php 
} ?>><?=t('Page – Display details about the event attached to a custom attribute.')?></option>
            <option value="R" <?php if ($mode == 'R') {
            ?>selected<?php
            } ?>><?=t('Request – Display details about an event occurrence passed through the URL request.')?></option>
        </select>
    </div>

    <div data-group="specific">
        <div class="form-group">
            <?=$form->label('calendarID', t('Calendar'))?>
            <?=$form->select('calendarID', $calendars, $calendarID, ['data-select' => 'calendar'])?>
        </div>

        <div class="form-group">
            <?=$form->label('eventID', t('Event'))?>
            <div data-wrapper="calendar-event-selector"><?=t('Choose a Calendar')?></div>
        </div>
    </div>

    <div data-group="page">
        <div class="form-group">
            <?=$form->label('calendarEventAttributeKeyHandle', t('Retrieve Event from Attribute'))?>
            <?=$form->select('calendarEventAttributeKeyHandle', $calendarEventPageKeys, $calendarEventAttributeKeyHandle)?>
        </div>
    </div>
</fieldset>
<fieldset>
    <legend><?=t('Event Data to Display')?></legend>

    <div class="form-group">
        <label class="control-label"><?=t('Core Properties')?></label>
        <div class="checkbox">
            <label><?=$form->checkbox('displayEventName', 1, $displayEventName)?> <?=t('Name')?></label>
        </div>
        <div class="checkbox">
            <label><?=$form->checkbox('displayEventDate', 1, $displayEventDate)?> <?=t('Occurrence Date and Time')?></label>
        </div>
        <div class="checkbox">
            <label><?=$form->checkbox('displayEventDescription', 1, $displayEventDescription)?> <?=t('Description')?></label>
        </div>
    </div>

    <div class="form-group">
        <label class="control-label"><?=t('Custom Attributes')?></label>
        <?php foreach ($eventKeys as $ak) {
    ?>
        <div class="checkbox">
            <label><?=$form->checkbox('displayEventAttributes[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $displayEventAttributes))?> <?=$ak->getAttributeKeyDisplayName()?></label>
        </div>
        <?php 
} ?>
    </div>

    <div data-group="linking">
        <div class="form-group">
            <label class="control-label"><?=t('Linking')?></label>
            <div class="checkbox">
                <label><?=$form->checkbox('enableLinkToPage', 1, $enableLinkToPage)?> <?=t('Link Event Name to Detail Page')?></label>
            </div>
        </div>
    </div>

</fieldset>

<script type="text/javascript">
$(function() {
    $('select[data-select=mode]').on('change', function() {
        if ($(this).val() == 'S') {
            $('div[data-group=page]').hide();
            $('div[data-group=specific]').show();
        } else if ($(this).val() == 'P') {
            $('div[data-group=specific]').hide();
            $('div[data-group=page]').show();
        } else {
            $('div[data-group=specific]').hide();
            $('div[data-group=page]').hide();
        }
    }).trigger('change');

    $('select[data-select=calendar]').on('change', function() {
        if ($(this).val()) {
            $('div[data-wrapper=calendar-event-selector]').html('').concreteCalendarEventSelector({
                inputName: 'eventID',
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

    $('input[name=displayEventName]').on('change', function() {
        if ($(this).is(':checked')) {
            $('div[data-group=linking]').show();
        } else {
            $('div[data-group=linking]').hide();
        }
    }).trigger('change');

});
</script>