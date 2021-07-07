<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="ccm-ui">

<?php
/**
 * @var $event \Concrete\Core\Entity\Calendar\CalendarEvent
 */
$attributes = \Concrete\Core\Attribute\Key\EventKey::getList();
?>

    <?php if (!$version->isApproved()) { ?>
        <div class="alert alert-info"><?=t('This occurrence belongs to an event version that is not yet approved.')?></div>
    <?php } ?>
    <h3><?=$version->getName()?></h3>

    <?php
    $repetitions = $version->getRepetitions();
    ?>

    <h4><?=t('Dates')?></h4>
    <?php foreach($repetitions as $repetition) { ?>
        <p><?=$repetition?></p>
    <?php } ?>

    <h3><?=t('Description')?></h3>
    <?= $version->getDescription() ?>

    <hr/>

    <?php

    foreach ($attributes as $ak) {
        $av = $version->getAttributeValueObject($ak);
        if (is_object($av)) { ?>

        <div class="form-group">
            <label class="control-label form-label"><?=$ak->getAttributeKeyDisplayName()?></label>
            <div><?=$av->getValue('displaySanitized', 'display')?></div>
        </div>

    <?php
        }
    }
    ?>

</div>
