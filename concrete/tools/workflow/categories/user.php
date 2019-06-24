<?php
defined('C5_EXECUTE') or die('Access Denied.');
$u = User::getByUserID($_REQUEST['uID']);
$obj = new stdClass;
$obj->tableData=t('None');

use \Concrete\Core\Workflow\Progress\Response as WorkflowProgressResponse;
use \Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use \Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;
use \Concrete\Core\Workflow\Progress\Category as WorkflowProgressCategory;

if ($_REQUEST['task'] == 'save_user_workflow_progress' && Loader::helper("validation/token")->validate('save_user_workflow_progress')) {
    $wp = UserWorkflowProgress::getByID($_REQUEST['wpID']);

    if (is_object($wp)) {
        $wf = $wp->getWorkflowObject();
        $form = Loader::helper('form');
        $obj->wpID = $wp->getWorkflowProgressID();

        if ($wf->canApproveWorkflowProgressObject($wp)) {
            $task = WorkflowProgress::getRequestedTask();

            if ($task) {
                $r = $wp->runTask($task, $_POST);

                if (($r instanceof WorkflowProgressResponse) && $r->getWorkflowProgressResponseURL() != '') {
                    $obj->redirect = $r->getWorkflowProgressResponseURL();
                    $obj->message = $r->message;
                } else {
                    $obj->redirect = BASE_URL . View::url('/dashboard/users/search') . '?uID=' . $_REQUEST['uID'];
                }

                $category = WorkflowProgressCategory::getByID($wp->getWorkflowProgressCategoryID());
                $list = $category->getPendingWorkflowProgressList();
                $items = $list->get();
                // start a new buffer
                ob_start();
                Loader::element('workflow/progress/categories/user/table_data', array('items' => $items, 'list' => $list));
                $obj->tableData = ob_get_contents();
                // flush the bugger & close it
                ob_end_clean();
            }
        }
    }
}

print Loader::helper('json')->encode($obj);