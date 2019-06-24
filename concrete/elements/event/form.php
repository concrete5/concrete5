
<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Calendar\Event\EventOccurrence;

$fp = FilePermissions::getGlobal();
$tp = new TaskPermission();
$version = null;
if ($occurrence) {
    $version = $occurrence->getEvent()->getRecentVersion();
    $calendar = $version->getEvent()->getCalendar();
}

$permissions = new Permissions($calendar);

$timezone = $calendar->getTimezone();

$renderer = new \Concrete\Core\Attribute\Form\Renderer(
    new \Concrete\Core\Attribute\Context\DashboardFormContext(),
    $version
);

//$group = ResponseAssetGroup::get();
//$group->requireAsset('javascript', 'autosize');

$category = \Concrete\Core\Attribute\Key\Category::getByHandle('event');
if (is_object($category) && $category->allowAttributeSets()) {
    $sets = $category->getAttributeSets();
}

$summarySet = false;
$tabs[] = array('summary', t('Summary'), true);
$tabs[] = array('dates', t('Dates'));
foreach ($sets as $set) {
    if ($set->getAttributeSetHandle() == 'calendar_summary') {
        $summarySet = $set;
        continue;
    }
    $tabs[] = array('tab-' . $set->getAttributeSetHandle(), $set->getAttributeSetDisplayName());
}

$repetitions = null;
if ($version) {
    $repetitions = $version->getRepetitions();
}

?>

<?php echo Loader::helper('concrete/ui')->tabs($tabs);?>

<div class="ccm-event-form">

    <div class="ccm-tab-content" id="ccm-tab-content-summary">

    <?php
    /** @var EventOccurrence $occurrence */
    if ($occurrence) {
        if ($occurrence->getRepetition()->repeats()) {
            ?>
            <div>
                <div class="radio">
                    <label>
                        <input type="radio" name="edit_type" value="local"/> <?= t('Just this occurrence') ?>
                    </label>
                </div>
                <div class="radio">
                    <label>
                        <input type="radio" name="edit_type" value="all" checked/> <?= t('All occurrences') ?>
                    </label>
                </div>
            </div>
            <hr/>
        <?php

        }
    }
    ?>


        <div class="form-group">
            <label for="name" class="control-label">
                <?= t('Name') ?>
            </label>

            <input type="text" class="form-control" name="name" value="<?= $version ? h($version->getName()) : '' ?>">
            <hr/>
        </div>
        <div class="form-group">
            <label for="name" class="control-label">
                <?= t('Description') ?>
            </label>

            <?=Core::make('editor')->outputStandardEditor('description', $version ? $version->getDescription() : '')?>
        </div>

        <?php if ($permissions->canEditCalendarEventMoreDetailsLocation()) { ?>

            <div class="form-group">
                <label for="page" class="control-label"><?=t('More Details Link Destination')?></label>
                <?php
                $cID = false;
                if (is_object($version)) {
                    $page = $version->getPageObject();
                    if (is_object($page)) {
                        $cID = $page->getCollectionID();
                    }
                }
                ?>
                <?=Core::make("helper/form/page_selector")->selectPage('cID', $cID)?>
            </div>

        <?php } ?>

        <?php
        if ($summarySet) {
            foreach($summarySet->getAttributeKeys() as $basicKey) {
                echo $renderer->render($basicKey);
            }
        }
        ?>

    </div>


    <div class="ccm-tab-content" id="ccm-tab-content-dates">
        <div class="form-group repeat-date-time">
            <?php
            $selector = new \Concrete\Core\Form\Service\Widget\DurationSelector();
            echo $selector->selectDuration('event', $repetitions, $timezone);
            ?>
        </div>

        <?php
        if ($occurrence) {
            ?>
            <div class="single-occurrence-date-time" style="display:none">
                <?php
                $form = \Core::make('helper/form');
                $dt = \Core::make('helper/form/date_time');

                $pdStartDateDateTime = new DateTime();
                $pdStartDateDateTime->setTimestamp($occurrence->getStart());
                $pdStartDateDateTime->setTimezone(new DateTimeZone($timezone));
                $pdStartDate = $pdStartDateDateTime->format('Y-m-d H:i:s');

                $pdEndDateDateTime = new DateTime();
                $pdEndDateDateTime->setTimestamp($occurrence->getEnd());
                $pdEndDateDateTime->setTimezone(new DateTimeZone($timezone));
                $pdEndDate = $pdEndDateDateTime->format('Y-m-d H:i:s');

                $singleRepetition = new \Concrete\Core\Calendar\Event\EventRepetition();
                $singleRepetition->setStartDate($pdStartDate);
                $singleRepetition->setEndDate($pdEndDate);
                ?>
                <div id="ccm-permissions-access-entity-dates">
                    <?php
                    $selector = new \Concrete\Core\Form\Service\Widget\DurationSelector();
                    echo $selector->selectDuration('local', $singleRepetition, $timezone, false, false);
                    ?>

                </div>
            </div>
            <?php

        }?>

    </div>

    <?php

    foreach ($sets as $set) {
        if ($set->getAttributeSetHandle() == 'calendar_summary') {
            continue;
        }
        ?>
        <div class="ccm-tab-content" id="ccm-tab-content-tab-<?=$set->getAttributeSetHandle()?>">
            <?php
            $keys = $set->getAttributeKeys();
        foreach ($keys as $ak) {
            echo $renderer->render($ak);
        }
        ?>
        </div>
        <?php

    }

    ?>
</div>

<script type="text/javascript">
    var CCM_EDITOR_SECURITY_TOKEN = "<?php echo Loader::helper('validation/token')->generate('editor')?>";
    _.defer(function() {
        var radios = $("input[name='edit_type']"),
            local = $('div.single-occurrence-date-time'),
            all = $('div.repeat-date-time');

        radios.closest('form').change(function() {
            if (radios.filter(':checked').val() === 'local') {
                local.show();
                all.hide();
            } else {
                local.hide();
                all.show();
            }
        });
    });

</script>