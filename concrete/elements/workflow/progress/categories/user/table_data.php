<?php
defined('C5_EXECUTE') or die('Access Denied.');
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>

<?php if (count($items) > 0) { ?>

    <table class="ccm-results-list table table-condensed" id="ccm-workflow-waiting-for-me">
        <tr>
            <th class="<?=$list->getSearchResultsClass('Request Type')?>"><?=t('Request Type')?></a></th>
            <th class="<?=$list->getSearchResultsClass('uName')?>"><a href="<?=$list->getSortByURL('uName', 'asc')?>"><?=t('Username')?></a></th>
            <th class="<?=$list->getSearchResultsClass('wpDateLastAction')?>"><a href="<?=$list->getSortByURL('wpDateLastAction', 'desc')?>"><?=t('Last Action')?></a></th>
            <th class="<?=$list->getSearchResultsClass('wpCurrentStatus')?>"><a href="<?=$list->getSortByURL('wpCurrentStatus', 'desc')?>"><?=t('Current Status')?></a></th>
            <th>&nbsp;</th>
        </tr>
        <?php
        $noitems = true;
        foreach($items as $it) {
            $u = $it->getUserObject();
            $wp = $it->getWorkflowProgressObject();
            $wf = $wp->getWorkflowObject();
            $wfr = $wp->getWorkflowRequestObject();

            if ($wf->canApproveWorkflowProgressObject($wp)) {
                $noitems = false;
                ?>
                <tr class="ccm-workflow-waiting-for-me-row<?=$wp->getWorkflowProgressID()?>">
                    <td><?=method_exists($wfr, 'getRequestActionText') ? $wfr->getRequestActionText() : '';?></td>
                    <td><?=$u->getUserName();?></td>
                    <td>
                        <?= $dh->formatDateTime($wp->getWorkflowProgressDateLastAction(), true)?>
                    </td>
                    <td><a href="javascript:void(0)" title="<?=t('Click for history.')?>" onclick="$(this).parentsUntil('tr').parent().next().show()"><?=$wf->getWorkflowProgressStatusDescription($wp)?></a></td>
                    <td class="ccm-workflow-progress-actions">
                        <form action="<?=$wp->getWorkflowProgressFormAction()?>" method="post">

                            <?php $actions = $wp->getWorkflowProgressActions(); ?>
                            <?php foreach($actions as $act) {
                                $attribs = '';
                                $_attribs = $act->getWorkflowProgressActionExtraButtonParameters();
                                foreach($_attribs as $key => $value) {
                                    $attribs .= $key . '="' . $value . '" ';
                                }
                                $br = '';
                                $bl = '';

                                if ($act->getWorkflowProgressActionStyleInnerButtonLeftHTML()) {
                                    $bl = $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() . ' ';
                                }
                                if ($act->getWorkflowProgressActionStyleInnerButtonRightHTML()) {
                                    $br = ' ' . $act->getWorkflowProgressActionStyleInnerButtonRightHTML();
                                }
                                if ($act->getWorkflowProgressActionURL() != '') {
                                    print '<a href="' . $act->getWorkflowProgressActionURL() . '&source=dashboard" ' . $attribs . ' class="btn btn-mini ' . $act->getWorkflowProgressActionStyleClass() . '">' . $bl . $act->getWorkflowProgressActionLabel() . $br . '</a> ';
                                } else {
                                    print '<button type="submit" ' . $attribs . ' name="action_' . $act->getWorkflowProgressActionTask() . '" class="btn btn-mini ' . $act->getWorkflowProgressActionStyleClass() . '">' . $bl . $act->getWorkflowProgressActionLabel() . $br . '</button> ';
                                }
                            } ?>
                        </form>
                    </td>
                </tr>

            <?php  }

        } ?>

    </table>
<?php } else { ?>
    <p><?=t('None.')?></p>
<?php } ?>