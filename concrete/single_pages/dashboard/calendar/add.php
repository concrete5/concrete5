<?php
use Concrete\Core\Page\Type\Type;

defined('C5_EXECUTE') or die('Access Denied.');
$form = Core::make('helper/form');
$color = Core::make('helper/form/color');
$preferences = Core::make('Concrete\Core\Calendar\Utility\Preferences');

if (!isset($calendar) || !is_object($calendar)) {
    $calendar = null;
}
$calendarName = null;
$buttonText = t('Add Calendar');
$enableMoreDetails = false;
$eventPageParentID = 0;
$eventPageTypeID = 0;
$eventPageAttributeKeyHandle = '';
if ($calendar !== null) {
    $calendarName = $calendar->getName();
    $enableMoreDetails = $calendar->enableMoreDetails();
    if ($enableMoreDetails == 'C') {
        $enableMoreDetails = 'create';
    } else if ($enableMoreDetails == 'A') {
        $enableMoreDetails = 'associate';
    }
    $eventPageParentID = $calendar->getEventPageParentID();
    $eventPageTypeID = $calendar->getEventPageTypeID();
    $eventPageAttributeKeyHandle = $calendar->getEventPageAttributeKeyHandle();
    $eventPageAssociatedID = $calendar->getEventPageAssociatedID();
    $buttonText = t('Save Calendar');
}
?>

<form method="post" action="<?=$view->action('submit')?>">
    <?=Loader::helper('validation/token')->output('submit')?>
    <fieldset>
        <legend><?=t('Summary')?></legend>
        <?php if ($calendar !== null) {
    ?>
            <input type="hidden" name="caID" value="<?=$calendar->getID()?>" />
        <?php

} ?>

        <div class="form-group">
            <?=$form->label('calendarName', t('Calendar Name'))?>
            <?=$form->text('calendarName', $calendarName)?>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions ">
                <a href="<?=$view->url($preferences->getPreferredViewPath())?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
                <button type="submit" class="btn btn-primary pull-right"><?=$buttonText?></button>
            </div>
        </div>
    </fieldset>
    <fieldset>
        <legend><?=t('More Details')?></legend>
        <div class="form-group">
            <?=$form->label('enableMoreDetails', t('Enable Additional Event Details'))?>
            <?=$form->select('enableMoreDetails', [
                '' => t('Do not link to a "More Details" page.'),
                'create' => t('Create and link to a new "More Details" page.'),
                'associate' => t('Link to an existing page')
            ], $enableMoreDetails)
            ?>
        </div>

        <div data-more-details-group="create" style="display: none">
            <div class="form-group">
                <?=$form->label('eventPageParentID', t('Parent Page'))?>
                <?=Core::make('helper/form/page_selector')->selectPage('eventPageParentID', $eventPageParentID)?>
            </div>

            <div class="form-group">
                <?=$form->label('eventPageTypeID', t('Page Type'))?>
                <?=$form->select('eventPageTypeID', $types, $eventPageTypeID)?>
            </div>

            <div class="form-group">
                <?=$form->label('eventPageAttributeKeyHandle', t('Calendar Event Attribute'))?>
                <?=$form->select('eventPageAttributeKeyHandle', $attributeKeys, $eventPageAttributeKeyHandle)?>
            </div>

        </div>

        <div data-more-details-group="associate" style="display: none">
            <div class="form-group">
                <?=$form->label('eventPageAssociatedID', t('Link to Page'))?>
                <?=Core::make('helper/form/page_selector')->selectPage('eventPageAssociatedID', isset($eventPageAssociatedID) ? $eventPageAssociatedID : null)?>
            </div>
            <div class="help-block">
                <?=t('<strong>Important</strong>: The page that you link to should contain a Calendar Event block or custom code that can render a specific calendar occurrence.')?>
            </div>
        </div>

    </fieldset>
</form>

<script type="text/javascript">
    $(function () {

        var steps = [{
            content: '<p><span class="h5"><?=t('Calendar Name')?></span><br/><?=t('Each separate calendar gets a complete separate list of events. Each front-end block can display content from one or multiple calendars.')?></p>',
            highlightTarget: false,
            nextButton: true,
            target: $('input[name=calendarName]'),
            my: 'top center',
            at: 'bottom center'
        }];

        var tour = new Tourist.Tour({
            steps: steps,
            tipClass: 'Bootstrap',
            tipOptions:{
                showEffect: 'slidein'
            }
        });
        tour.on('start', function() {
            ConcreteHelpLauncher.close();
        });
        ConcreteHelpGuideManager.register('add-calendar', tour);

        $('select[name=enableMoreDetails]').on('change', function() {
            var group = $(this).val();
            $('div[data-more-details-group]').hide();
            if (group) {
                $('div[data-more-details-group=' + group + ']').show();
            }
        }).trigger('change');
    });

</script>