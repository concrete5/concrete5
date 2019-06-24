<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<?php
    $mi = Marketplace::getInstance();
    if ($mi->hasConnectionError() && $mi->getConnectionError() == Marketplace::E_MARKETPLACE_SUPPORT_MANUALLY_DISABLED) {
        ?>
		<p><?=t("External marketplace support has been disabled for this website.")?></p>
	<?php 
    } else {
        ?>
		<style type="text/css">
		div.ccm-pane-body {padding-top: 0px; padding-right: 0px; padding-left: 0px}
		div.ccm-pane-body div.ccm-error { padding:15px 20px; };
		</style>
		<?php
        echo $mi->getMarketplaceFrame('100%', '400', false, isset($startStep) ? h($startStep) : null);
    }
?>
