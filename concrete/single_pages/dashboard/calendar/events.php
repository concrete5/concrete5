<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Calendar\Event\EventOccurrence;
use Punic\Calendar as PunicCalendar;

if (!isset($topic)) {
    $topic = null;
}

Loader::element('calendar/header', array(
    'calendar' => $calendar,
    'calendars' => $calendars,
));
?>

<div id="calendar-header">

    <h3 id="calendar-month-name"><?= $monthText ?> <?= $year ?></h3>

    <div class="btn-group">
        <a href="<?= $previousLink ?>" class="btn btn-sm btn-secondary"><i class="fa fa-angle-double-left"></i></a>
        <a data-nav="month" href="javascript:void(0)" class="btn btn-sm btn-secondary"><i class="far fa-calendar"></i></a>
        <a href="<?= $nextLink ?>" class="btn btn-sm btn-secondary"><i class="fa fa-angle-double-right"></i></a>
    </div>

    <?php if (isset($topics) && is_array($topics)) {
        ?>
        <div class="btn-group" id="calendar-topics">
            <button type="button" id="topics_button" class="btn btn-secondary btn-sm" data-bs-toggle="dropdown">
                <?= $topic ? h($topic->getTreeNodeDisplayName('html')) : t('All Categories') ?>
                <span class="caret"></span>
            </button>
            <div class="dropdown-menu" role="menu" aria-labelledby="topics_button">
                <a href="?topic_id=0" class="dropdown-item"><?= t('All Categories') ?></a>
                <?php
                /** @var \Concrete\Core\Tree\Node\Node $topic_node */
                foreach ((array)$topics as $topic_node) {
                    ?>
                        <a class="dropdown-item" href="?topic_id=<?= $topic_node->getTreeNodeID() ?>">
                            <?= h($topic_node->getTreeNodeDisplayName('html')) ?>
                        </a>
                    <?php
                }
                ?>
            </div>
        </div>

        <?php
    } else {
        ?>
        <br/><br/>
        <?php
    } ?>

    <div id="calendar-navigation-month-select-wrapper">

   </div>

</div>

<style type="text/css">
    div#calendar-navigation-month-select-wrapper {
        position: relative;
    }
</style>

<script type="text/javascript">
    $(function () {
        var $dp = $("<input type='text' />").hide().datepicker({
            onSelect: function(dateText, inst) {
                var month = inst.currentMonth + 1,
                    year = inst.currentYear,
                    url = CCM_DISPATCHER_FILENAME + '/dashboard/calendar/events/view/<?=$calendar->getID()?>/' + year + '/' + month;
                window.location.href = url;
            },
            showButtonPanel: true,
            changeMonth: true,
            changeYear: true
        }).prependTo('body');

        var today = moment(<?=$todayDateTimestamp * 1000 ?>).tz(
            '<?=$calendar->getTimezone()?>'
        );

        // god this is so !@#! stupid
        $dp.datepicker('setDate', $.datepicker.parseDate('yy-mm-dd', today.format('YYYY-MM-DD')));

        $("a[data-nav=month]").click(function(e) {
            if ($dp.datepicker('widget').is(':hidden')) {
                $dp.show().datepicker('show').hide();
                $dp.datepicker("widget").position({
                    my: "middle top",
                    at: "middle bottom",
                    of: $('#calendar-navigation-month-select-wrapper')
                });
            } else {
                $dp.hide();
            }
            e.preventDefault();
        });
    });
</script>

<table class="ccm-dashboard-calendar table table-bordered">
    <thead>
    <tr>
        <?php
        for($weekday = 0; $weekday < 7; $weekday++) {
            ?><td width="<?= 100 / 7?>%"><h4><?= PunicCalendar::getWeekdayName($weekday, 'abbreviated', '', true) ?></h4></td><?php
        }
        ?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?php
        $cols = 0;
        $cellCounter = 0;
        $isToday = false;
        Loader::helper('text');
        for ($i = 1 - $firstDayInMonthNum; $i <= $daysInMonth; ++$i) {
            ++$cellCounter;
            if ($cols >= 7) {
                echo '</tr><tr>';
                $cols = 0;
            }
            ++$cols;
            $isToday = (date('Y') == $year && $month == date('m') && $i == date('j'));
            ?>
            <td class="<?php if ($isToday) {
                ?>ccm-dashboard-calendar-today<?php
            }
            ?> <?php if ($i > 0) {
                ?>ccm-dashboard-calendar-active-day<?php
            }
            ?>">
                <div class="ccm-dashboard-calendar-date-wrap">
                    <?php if ($i > 0) {
                        ?>
                        <div class="ccm-dashboard-calendar-date"><?= $i ?></div>

                        <?php
                        $list = new \Concrete\Core\Calendar\Event\EventOccurrenceList();
                        if ($topic) {
                            $list->filterByTopic($topic);
                        }
                        $list->filterByCalendar($calendar);
                        $list->includeInactiveEvents();
                        $list->filterByDate(date('Y-m-d', strtotime($year . '-' . $month . '-' . $i)));
                        $results = $list->getResults();

                        /** @var EventOccurrence $occurrence */
                        foreach ($results as $occurrence) {
                            $menu = new \Concrete\Core\Calendar\Event\Menu\EventOccurrenceMenu($occurrence);
                            $event = $occurrence->getEvent();
                            $color = $linkFormatter->getEventOccurrenceBackgroundColor($occurrence);
                            $date = $dateFormatter->getOccurrenceDateString($occurrence);
                            ?>
                            <div class="ccm-dashboard-calendar-date-event">
                                <?php print $menu->getMenuElement();?>
                                <?= $linkFormatter->getEventOccurrenceLinkObject($occurrence); ?>
                            </div>
                            <?php

                        }
                        ?>
                        <?php
                    } else {
                        ?>
                        <div class="ccm-dashboard-calendar-date-inactive">&nbsp;</div>
                        <?php
                    }
                    ?>
                </div>
            </td>
            <?php
        }
        while ($cols < 7) {
            echo '<td><div class="ccm-dashboard-calendar-date-wrap"><div class="ccm-dashboard-calendar-date-inactive">&nbsp;</div></div></td>';
            ++$cols;
        }
        ?>
    </tr>
    </tbody>
</table>

<script type="text/javascript">
    $(function() {
        var admin = new ConcreteCalendarAdmin($('body'));
    });
    <?php
    // Show an event edit dialog if there was a provided event version occurrence ID
    if (isset($initialEdit) && $initialEdit) {
        ?>
        let initDialog = function() {
            if (typeof $.fn.dialog !== 'function') {
                return setTimeout(initDialog, 50);
            }

            let anchor = $(document.createElement('a'))
                .attr('dialog-title', 'Edit')
                .attr('dialog-width', 1100)
                .attr('dialog-height', 600)
                .attr('href', "/ccm/calendar/dialogs/event/edit?versionOccurrenceID=<?= (int) $initialEdit ?>")

            anchor.dialog().click()
        }
        initDialog()
        <?php
    }
    ?>
</script>
