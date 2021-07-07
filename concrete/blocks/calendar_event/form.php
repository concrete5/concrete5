<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Entity\Attribute\Key\EventKey;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var string $mode */
/** @var string $calendarEventAttributeKeyHandle */
/** @var int $calendarID */
/** @var int $eventID */
/** @var string $displayEventAttributes */
/** @var bool $enableLinkToPage */
/** @var bool $displayEventName */
/** @var bool $displayEventDate */
/** @var bool $displayEventDescription */
/** @var array $calendarEventPageKeys */
/** @var EventKey[] $eventKeys */
/** @var array $calendars */
/** @var array $displayEventAttributes */
/** @var bool $allowExport */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);

?>

<fieldset>
    <legend>
        <?php echo t('Data Source') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('mode', t('Mode')); ?>
        <?php echo $form->select('mode', [
            "S" => t('Specific – Display details about a specific calendar event.'),
            "P" => t('Page – Display details about the event attached to a custom attribute.'),
            "R" => t('Request – Display details about an event occurrence passed through the URL request.')
        ], $mode, ["data-select" => "mode"]); ?>
    </div>

    <div data-group="specific">
        <div class="form-group">
            <?php echo $form->label('calendarID', t('Calendar')) ?>
            <?php echo $form->select('calendarID', $calendars, $calendarID, ['data-select' => 'calendar']) ?>
        </div>

        <div class="form-group">
            <?php echo $form->label('eventID', t('Event')) ?>
            <div data-wrapper="calendar-event-selector"><?php echo t('Choose a Calendar') ?></div>
        </div>
    </div>

    <div data-group="page">
        <div class="form-group">
            <?php echo $form->label('calendarEventAttributeKeyHandle', t('Retrieve Event from Attribute')) ?>
            <?php echo $form->select('calendarEventAttributeKeyHandle', $calendarEventPageKeys, $calendarEventAttributeKeyHandle) ?>
        </div>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Event Data to Display') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('', t('Core Properties')) ?>

        <div class="form-check">
            <?php echo $form->checkbox('displayEventName', 1, $displayEventName) ?>
            <?php echo $form->label("displayEventName", t('Name'), ["class" => "form-check-label"]) ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('displayEventDate', 1, $displayEventDate) ?>
            <?php echo $form->label("displayEventDate", t('Occurrence Date and Time'), ["class" => "form-check-label"]) ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('displayEventDescription', 1, $displayEventDescription) ?>
            <?php echo $form->label("displayEventDescription", t('Description'), ["class" => "form-check-label"]) ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('allowExport', 1, $allowExport) ?>
            <?php echo $form->label("allowExport", t('Allow event export'), ["class" => "form-check-label"]) ?>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('', t('Custom Attributes')) ?>

        <?php foreach ($eventKeys as $ak) { ?>
            <div class="form-check">
                <?php echo $form->checkbox('displayEventAttributes[]', $ak->getAttributeKeyID(), in_array($ak->getAttributeKeyID(), $displayEventAttributes), ["name" => "displayEventAttributes[]", "id" => "displayEventAttributes_" . $ak->getAttributeKeyID()]) ?>
                <?php echo $form->label("displayEventAttributes_" . $ak->getAttributeKeyID(), $ak->getAttributeKeyDisplayName(), ["class" => "form-check-label"]) ?>
            </div>
        <?php } ?>
    </div>

    <div data-group="linking">
        <div class="form-group">
            <?php echo $form->label('', t('Linking')) ?>

            <div class="form-check">
                <?php echo $form->checkbox('enableLinkToPage', 1, $enableLinkToPage) ?>
                <?php echo $form->label("enableLinkToPage", t('Link Event Name to Detail Page'), ["class" => "form-check-label"]) ?>
            </div>
        </div>
    </div>
</fieldset>

<!--suppress EqualityComparisonWithCoercionJS -->
<script type="text/javascript">
    $(function () {
        $('select[data-select=mode]').on('change', function () {
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

        $('select[data-select=calendar]').on('change', function () {
            if ($(this).val()) {
                $('div[data-wrapper=calendar-event-selector]').html('').concreteCalendarEventSelector({
                    inputName: 'eventID',
                    calendarID: $(this).val()
                    <?php if (isset($eventID) && $eventID > 0) {
                    ?>,
                    eventID: '<?php echo $eventID?>'
                    <?php
                    } ?>
                });
            } else {
                $('div[data-wrapper=calendar-event-selector]').html('<?php echo t('Choose a Calendar')?>');
            }
        }).trigger('change');

        $('input[name=displayEventName]').on('change', function () {
            if ($(this).is(':checked')) {
                $('div[data-group=linking]').show();
            } else {
                $('div[data-group=linking]').hide();
            }
        }).trigger('change');
    });
</script>
