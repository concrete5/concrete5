<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">

<?php
/**
 * @var $event \Concrete\Core\Entity\Calendar\CalendarEvent
 */
$event = $occurrence->getEvent();
$attributes = \Concrete\Core\Attribute\Key\EventKey::getList();
?>

    <?php if (!$occurrence->getVersion()->isApproved()) { ?>
        <div class="alert alert-info"><?=t('This occurrence belongs to an event version that is not yet approved.')?></div>
    <?php } ?>
    <h3><?= h($event->getName()) ?></h3>
    <div><?=$dateFormatter->getOccurrenceDateString($occurrence)?></div>
    <?php if ($url) { ?>
        <strong><a href="<?= $url ?>" target="_blank"><?=t('View Event Page')?></a></strong>
    <?php } ?>
    <h3><?=t('Description')?></h3>
    <?= $event->getDescription() ?>

    <hr/>

    <?php

    foreach ($attributes as $ak) {
        $av = $event->getAttributeValueObject($ak);
        if (is_object($av)) { ?>

        <div class="form-group">
            <label class="control-label form-label"><?=$ak->getAttributeKeyDisplayName()?></label>
            <div><?=$av->getDisplayValue()?></div>
        </div>

    <?php
        }
    }
    ?>

</div>

<script type="text/javascript">
    $(function () {
        $('.ccm-attribute-image-file-image').magnificPopup({
            type: 'image',
            mainClass: 'mfp-zoom-in mfp-img-mobile',
            image: {
                verticalFit: true
            },
            callbacks: {
                open: function() {
                    $('.mfp-content').addClass('ccm-ui');
                }
            }
        });
    });
</script>
