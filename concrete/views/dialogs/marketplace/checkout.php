<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">
    <?php
    $mi = \Concrete\Core\Marketplace\Marketplace::getInstance();
    echo $mi->getMarketplacePurchaseFrame($mri, '100%', '100%');
    ?>
</div>
