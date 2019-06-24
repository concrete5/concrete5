<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="clearfix">

<?php if (isset($_REQUEST['message'])) {
    ?>


<div class="alert alert-success" id="ccm-permissions-message-list">
<?php
if ($_REQUEST['message'] == 'custom_options_saved') {
    ?>
	<?=t('Custom Options saved.')?>
<?php 
} elseif ($_REQUEST['message'] == 'workflows_saved') {
    ?>
	<?=t('Workflow Options saved.')?>
<?php 
} elseif ($_REQUEST['message'] == 'entity_removed') {
    ?>
	<?=t('User/Group Removed')?>
<?php 
} elseif ($_REQUEST['message'] == 'entity_added') {
    ?>
	<?=t('User/Group Added')?>
<?php 
}
    ?>
</div>

<?php 
} ?>
</div>
<script type="text/javascript">
$(function() {
	$("#ccm-permissions-message-list").show('highlight', {'color': '#fff'}, function() {
		setTimeout(function() {
			$("#ccm-permissions-message-list").fadeOut(300, 'easeInExpo');
		}, 1200);
	});
});
</script>

