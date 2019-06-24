<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<h2><?=t('Slot')?></h2>
<div>
<?=Loader::helper('form')->select('slot', array('A' => 'A', 'B' => 'B', 'C' => 'C'))?>
</div>