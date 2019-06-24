<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php if ($control->getHeadline()) { ?>
    <h3><?=$control->getHeadline()?></h3>
<?php } ?>
<?php if ($control->getBody()) { ?>
    <?=$control->getBody()?>
<?php } ?>
