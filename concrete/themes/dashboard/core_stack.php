<?php
defined('C5_EXECUTE') or die('Access Denied.');
$this->inc('elements/header.php'); ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper($c->getCollectionName())?>

<?php

$a = new Area(STACKS_AREA_NAME);
$a->display($c);
?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper()?>


<?php $this->inc('elements/footer.php'); ?>