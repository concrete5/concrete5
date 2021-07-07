<?php

defined('C5_EXECUTE') or die("Access Denied.");

Loader::element('calendar/header', array(
    'calendar' => $calendar,
    'calendars' => $calendars,
    'mode' => 'list',
));
$topic_id = Request::getInstance()->get('topic_id');
?>

<form method="get" action="<?= $view->action('search') ?>">
    <label class="control-label form-label" for="query"><?= t('Search') ?></label>
    <div class="input-group">
        <?= $form->text('query', array('placeholder' => t('Keywords'))) ?>
        <?php if (isset($topics) && is_array($topics)) { ?>

            <select name="topic_id" class="form-select">
                <option value=""><?= t('All Categories') ?></option>
                <?php foreach ($topics as $topic_node) { ?>
                    <option <?php if ($topic_id == $topic_node->getTreeNodeID()) { ?>selected<?php } ?>
                            value="<?= $topic_node->getTreeNodeID() ?>"><?= h($topic_node->getTreeNodeDisplayName('html')) ?></option>
                <?php } ?>
            </select>

        <?php } ?>

        <button type="submit" class="btn btn-secondary"><?= t('Search') ?></button>
    </div>
</form>


<?php if (count($events)) { ?>

    <table class="ccm-search-results-table" data-table="event-list">
        <thead>
        <tr>
            <th></th>
            <th><span><?= t('Name') ?></span></th>
            <th><span><?= t('Date/Time') ?></span></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($events as $occurrence) {
            $menu = new \Concrete\Core\Calendar\Event\Menu\EventOccurrenceMenu($occurrence);
            $event = $occurrence->getEvent();
            $color = $linkFormatter->getEventOccurrenceBackgroundColor($occurrence);
            $date = $dateFormatter->getOccurrenceDateString($occurrence);
            ?>
            <tr>
                <td><span class="event-swatch" style="background-color: <?= $color ?>"></span></td>
                <td class="ccm-search-results-name">
                    <?php
                    print $menu->getMenuElement();
                    print h($event->getName());

                    if (!$occurrence->getVersion()->isApproved()) {
                        print ' <i class="fa fa-exclamation-circle"></i>';
                    }
                    ?>

                </td>
                <td class="ccm-search-results-date"><?= $date ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>

    <?php if (is_object($pagination) && $pagination->haveToPaginate()) { ?>
        <div class="ccm-search-results-pagination"><?= $pagination->renderDefaultView() ?></div>
    <?php } ?>

<?php } else { ?>
    <p><?= t('No upcoming events found.') ?></p>
<?php } ?>

<style type="text/css">
    span.event-swatch {
        width: 16px;
        height: 16px;
        border-radius: 2px;
        display: inline-block;
    }

    td.ccm-search-results-name {
        width: 35%;
    }

    td.ccm-search-results-date {
        width: 65%;
    }

    td.ccm-search-results-controls {
        white-space: nowrap;
        padding-right: 20px !important;
    }

</style>

<script type="text/javascript">
    $(function () {
        var admin = new ConcreteCalendarAdmin($('body'));
    });
</script>
