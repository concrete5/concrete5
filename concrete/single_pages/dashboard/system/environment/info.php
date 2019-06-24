<?php defined('C5_EXECUTE') or die('Access Denied.');?>
<?=Loader::helper('concrete/dashboard')->getDashboardPaneHeaderWrapper(t('Environment'), false, 'span8 offset2');?>

<textarea style="width: 99%; height: 340px;" onclick="this.select()" id="ccm-dashboard-environment-info"><?php echo t('Unable to load environment info')?></textarea>

<script type="text/javascript">
$(document).ready(function() {
    $.get('<?= $view->action('get_environment_info')?>').then(function(data) {
        $('#ccm-dashboard-environment-info').text(data.replace('&nbsp;', ' '));
    });
});
</script>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper();?>
