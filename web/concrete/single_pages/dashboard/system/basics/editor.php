<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Rich Text Editor'), t('Control the options available for TinyMCE.'), false, false);?>
<?php
$h = Loader::helper('concrete/ui');
?>	
<div class="ccm-pane-body ccm-pane-body-footer">
	<?=t('The editor currently has no globally configurable options.')?>
</div>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false);?>