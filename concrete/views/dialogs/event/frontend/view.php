<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-block-calendar-event-dialog-details">

<?php
$lightboxProperties = $blockController->getSelectedLightboxProperties();

if (count($lightboxProperties)) {
    ?>
    <?php foreach ($lightboxProperties as $column) {
    $title = $blockController->getPropertyTitle($column);
    if ($title) {
        ?>
            <h4><?=$title?></h4>
        <?php

    }
    ?>
        <?=$blockController->getPropertyValue($column, $occurrence)?>
    <?php

}
    ?>
<?php

} ?>

</div>
