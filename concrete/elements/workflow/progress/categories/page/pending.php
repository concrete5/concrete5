<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

$list = $category->getPendingWorkflowProgressList();
$items = $list->get();
if (count($items) > 0) {
    ?>

<div id="ccm-workflow-waiting-for-me-wrapper">
<table class="ccm-results-list table table-condensed" id="ccm-workflow-waiting-for-me">
<tr>
	<th class="<?=$list->getSearchResultsClass('cvName')?>"><a href="<?=$list->getSortByURL('cvName', 'asc')?>"><?=t('Page Name')?></a></th>
	<th><?=t('URL')?></th>
	<th class="<?=$list->getSearchResultsClass('wpDateLastAction')?>"><a href="<?=$list->getSortByURL('wpDateLastAction', 'desc')?>"><?=t('Last Action')?></a></th>
	<th class="<?=$list->getSearchResultsClass('wpCurrentStatus')?>"><a href="<?=$list->getSortByURL('wpCurrentStatus', 'desc')?>"><?=t('Current Status')?></a></th>
	<th>&nbsp;</th>
</tr>
<?php
$noitems = true;
    foreach ($items as $it) {
        $p = $it->getPageObject();
        $wp = $it->getWorkflowProgressObject();
        $wf = $wp->getWorkflowObject();
        if ($wf->canApproveWorkflowProgressObject($wp)) {
            $noitems = false;
            ?>
<tr class="ccm-workflow-waiting-for-me-row<?=$wp->getWorkflowProgressID()?>">
	<td><?=$p->getCollectionName()?></td>
	<td><a href="<?=Loader::helper('navigation')->getLinkToCollection($p)?>"><?=$p->getCollectionPath()?></a>
	<td><?=$dh->formatDateTime($wp->getWorkflowProgressDateLastAction(), true)?></td>
	<td><a href="javascript:void(0)" title="<?=t('Click for history.')?>" onclick="$(this).parentsUntil('tr').parent().next().show()"><?=$wf->getWorkflowProgressStatusDescription($wp)?></a></td>
	<td class="ccm-workflow-progress-actions">
	<form action="<?=$wp->getWorkflowProgressFormAction()?>" method="post">
	<?php $actions = $wp->getWorkflowProgressActions();
            ?>
	<?php foreach ($actions as $act) {
    $attribs = '';
    $_attribs = $act->getWorkflowProgressActionExtraButtonParameters();
    foreach ($_attribs as $key => $value) {
        $attribs .= $key . '="' . $value . '" ';
    }
    $br = '';
    $bl = '';
    if ($act->getWorkflowProgressActionStyleInnerButtonLeftHTML()) {
        $bl = $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() . '&nbsp;&nbsp;';
    }
    if ($act->getWorkflowProgressActionStyleInnerButtonRightHTML()) {
        $br = '&nbsp;&nbsp;' . $act->getWorkflowProgressActionStyleInnerButtonRightHTML();
    }
    if ($act->getWorkflowProgressActionURL() != '') {
        echo '<a href="' . $act->getWorkflowProgressActionURL() . '&source=dashboard" ' . $attribs . ' class="btn btn-mini ' . $act->getWorkflowProgressActionStyleClass() . '">' . $bl . $act->getWorkflowProgressActionLabel() . $br . '</a> ';
    } else {
        echo '<button type="submit" ' . $attribs . ' name="action_' . $act->getWorkflowProgressActionTask() . '" class="btn btn-mini ' . $act->getWorkflowProgressActionStyleClass() . '">' . $bl . $act->getWorkflowProgressActionLabel() . $br . '</button> ';
    }
}
            ?>
	</form>
	</td>
</tr>
<tr class="ccm-workflow-waiting-for-me-row<?=$wp->getWorkflowProgressID()?> ccm-workflow-progress-history">
	<td colspan="6">
		<?=Loader::element('workflow/progress/history', array('wp' => $wp))?>
	</td>
</tr>

<?php 
        }
    }
    ?>
<?php if ($noitems) {
    ?>
	<tr>
		<td colspan="5"><?=t('There is nothing currently waiting for you.')?></td>
	</tr>
<?php 
}
    ?>
</table>
</div>

<script type="text/javascript">
$(function() {
	$('.ccm-workflow-progress-actions form').ajaxForm({ 
		dataType: 'json',
		beforeSubmit: function() {
			jQuery.fn.dialog.showLoader();
		},
		success: function(r) {
			var wpID = r.wpID;
			$('.ccm-workflow-waiting-for-me-row' + wpID).fadeOut(300, function() {
				jQuery.fn.dialog.hideLoader();
				$('.ccm-workflow-waiting-for-me-row' + wpID).remove();
				if ($('#ccm-workflow-waiting-for-me tr').length == 1) { 
					$("#ccm-workflow-waiting-for-me-wrapper").html('<?=t('None.')?>');
				}
			});
		}
	});
});
</script>

<?php 
} else {
    ?>
	<p><?=t('None.')?></p>
<?php 
} ?>