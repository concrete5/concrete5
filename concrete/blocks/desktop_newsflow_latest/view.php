<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php if (is_object($slot)) { ?>
<div class="ccm-block-dashboard-newsflow-latest-wrapper">
    <div class="ccm-block-dashboard-newsflow-latest">
        <?=$slot->getContent()?>
    </div>
</div>
<?php 
}
