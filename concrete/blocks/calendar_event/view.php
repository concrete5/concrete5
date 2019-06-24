<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if ($event) { ?>
    <div class="ccm-block-calendar-event-wrapper">

        <?php if ($displayEventName) {
    ?>
            <div class="ccm-block-calendar-event-header">
                <h3><?php if ($enableLinkToPage && $eventOccurrenceLink) {
    ?>
                        <a href="<?=$eventOccurrenceLink?>"><?= h($event->getName()) ?></a>
                    <?php
} else {
    ?>
                        <?= h($event->getName()) ?>
                    <?php
}
    ?>
                </h3>
            </div>
        <?php
}
    ?>

        <?php if ($displayEventDate) {
    ?>
            <div class="ccm-block-calendar-event-date-time">
                <?= $formatter->getOccurrenceDateString($occurrence) ?>
            </div>
        <?php
}
    ?>

        <?php if ($displayEventDescription && $event->getDescription()) {
    ?>
            <div class="ccm-block-calendar-event-description">
                <p><?= $event->getDescription() ?></p>
            </div>
        <?php
}
    ?>

        <?php if (count($displayEventAttributes)) {
    ?>
            <div class="ccm-block-calendar-event-attributes">
                <?php foreach ($displayEventAttributes as $akID) {
    $ak = \Concrete\Core\Attribute\Key\EventKey::getByID($akID);
    if (is_object($ak)) {
        echo $event->getAttribute($ak->getAttributeKeyHandle(), 'displaySanitized');
    }
}
    ?>
            </div>
        <?php
}
    ?>


    </div>
<?php
} ?>
