<?php

defined('C5_EXECUTE') or die("Access Denied.");

$calendarPermissions = new Permissions($calendar);
$preferences = Core::make('Concrete\Core\Calendar\Utility\Preferences');
if (!isset($mode)) {
    $mode = null;
}
if (!isset($month)) {
    $month = null;
}
?>


<div class="ccm-dashboard-header-buttons">
    <div class="btn-group">
        <div class="btn-group">
            <button type="button" id="calendar_button" class="btn btn-secondary dropdown-toggle"
                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= h($calendar->getName()) ?>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu" role="menu" aria-labelledby="calendar_button">
                <?php foreach ($calendars as $cal) {
    $p = new \Permissions($cal);
    if ($p->canViewCalendarInEditInterface()) {
        ?>
                        <a href="<?= URL::to($preferences->getPreferredViewPath(), 'view',
                                $cal->getID()) ?>" class="dropdown-item"><?= h($cal->getName()) ?></a>
                    <?php
    }
    ?>
                    <?php

} ?>
                <?php if ($calendarPermissions->canEditCalendar() || $calendarPermissions->canEditCalendarPermissions()) {
    ?>
                <div class="dropdown-divider"></div>

            <?php if ($calendarPermissions->canEditCalendar()) {
    ?>
                <a href="<?= URL::to('/dashboard/calendar/add', $calendar->getID()) ?>"
                   class="dropdown-item"><?= t("Details") ?></a>
            <?php
}
    ?>
            <?php if ($calendarPermissions->canEditCalendarPermissions()) {
    ?>
                <a href="<?= URL::to('/dashboard/calendar/permissions',
                    $calendar->getID()) ?>" class="dropdown-item"><?= t("Permissions") ?></a>

                    <?php
}
    ?>
                    <?php
} ?>

                    <?php if ($calendarPermissions->canDeleteCalendar()) {
    ?>
                        <div class="dropdown-divider"></div>
                <a href="#" data-dialog="delete-calendar" class="dropdown-item"><span class="text-danger"><?= t(
                                "Delete Calendar") ?></span></a>
            <?php
} ?>
            </div>
        </div>
        <a href="<?= URL::to('/dashboard/calendar/events', 'view',
            $calendar->getID()) ?>" class="btn btn-secondary <?php if ($mode != 'list') {
    ?>active<?php
} ?>"><i class="fa fa-calendar"></i></a>
        <a href="<?= URL::to('/dashboard/calendar/event_list', 'view',
            $calendar->getID()) ?>" class="btn btn-secondary <?php if ($mode == 'list') {
    ?>active<?php
} ?>"><i class="fa fa-list"></i></a>
        <?php if ($calendarPermissions->canAddCalendarEvent()) {
    ?>
            <a class="dialog-launch btn btn-primary" dialog-width="1100" dialog-title="<?= t('Add Event') ?>"
               dialog-height="600"
               href="<?= URL::to('/ccm/calendar/dialogs/event/add?caID=' . $calendar->getID()) ?>"><?= t("Add Event") ?></a>
        <?php
} ?>
    </div>
</div>


<div style="display: none">
    <div id="ccm-dialog-delete-calendar" class="ccm-ui">
        <form method="post" class="form-stacked" action="<?= $view->action('delete_calendar') ?>">
            <?= Loader::helper("validation/token")->output('delete_calendar') ?>
            <input type="hidden" name="caID" value="<?= $calendar->getID() ?>"/>

            <p><?= t('Are you sure? This action cannot be undone.') ?></p>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
            <button class="btn btn-danger float-end" onclick="$('#ccm-dialog-delete-calendar form').submit()"><?= t(
                    'Delete Calendar') ?></button>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {
        $('a[data-dialog=delete-calendar]').on('click', function () {
            jQuery.fn.dialog.open({
                element: '#ccm-dialog-delete-calendar',
                modal: true,
                width: 320,
                title: '<?=t("Delete Calendar")?>',
                height: 'auto'
            });
        });

        $('select#ccm-dashboard-calendar-year-selector').on('change', function () {
            window.location.href = '<?=URL::to('/dashboard/calendar/events', 'view', $calendar->getID())?>/'
                + $(this).val() + '/' + '<?=$month?>';
        });
    });
</script>

<style type="text/css">
    table.ccm-dashboard-calendar {
        width: 100%;
    }

    table.ccm-dashboard-calendar > thead > tr > td,
    table.ccm-dashboard-calendar {
        border-width: 0px !important;
    }

    table.ccm-dashboard-calendar td {
        min-height: 100px;
    }

    div.ccm-dashboard-calendar-date-wrap {
        min-height: 80px;
    }

    div.ccm-dashboard-calendar-date {
        text-align: right;
        font-size: 1.1em;
        margin-bottom: 20px;
        color: #666;
    }

    td.ccm-dashboard-calendar-today {
        background-color: rgba(91, 192, 222, 0.15);
    }

    div.ccm-dashboard-calendar-date-event {
        padding: 0px;
    }

    div.ccm-dashboard-calendar-date-event > a {
        display: block;
        text-decoration: none;
        color: #fff;
        padding: 2px 10px 2px 10px;
        margin-left: -8px;
        margin-right: -8px;
        text-decoration: none;
        position: relative;
        border-top: 1px solid white;
    }
    div.ccm-dashboard-calendar-date-event > a i.fa {
        position: absolute;
        top: 6px;
        right: 8px;
    }


    .ccm-calendar-date-event-pending {
        opacity: 0.3;
    }

    .ccm-calendar-date-event-cancelled {
        background-color: #3988ED;
        display: block;
        text-decoration: none;
        color: #fff;
        padding: 2px 10px 2px 10px;
        margin-left: -8px;
        margin-right: -8px;
        cursor: not-allowed;
        opacity: .5;
    }

    div.ccm-dashboard-calendar-date-event > a:hover {
        color: #ccc;
    }

    div#calendar-header {
        position: relative;
        text-align: center;
    }

    h3#calendar-month-name {
        position: absolute;
        top: 0px;
        left: 0px;
        margin-top: 0px;
    }

    div#calendar-topics {
        position: absolute;
        top: 0px;
        right: 0px;
    }


</style>
