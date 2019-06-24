<?php defined('C5_EXECUTE') or die('Access Denied.');

if (is_array($workflowList) && !empty($workflowList)) {
    $app = Concrete\Core\Support\Facade\Facade::getFacadeApplication();
    $cih = $app->make('helper/concrete/ui');

    foreach ($workflowList as $i => $wl) {
        $wr = $wl->getWorkflowRequestObject();
        $wf = $wl->getWorkflowObject();
        $form = '<form data-form="workflow" method="post" action="' . $wl->getWorkflowProgressFormAction() . '">';
        $text = '';
        $description = $wr->getWorkflowRequestDescriptionObject();
        if ($description) {
            $text = '<p>' . $description->getInContextDescription() . '</p>';
        }
        $requester = $wr->getRequesterUserObject();
        $dateAdded = $wl->getWorkflowProgressDateAdded();
        if ($requester && $dateAdded) {
            $text .= '<p><small>' . t(/*i18n: %1$s is a user name, %2$s is a date/time*/'Submitted by %1$s on %2$s', $requester->getUserDisplayName(), $app->make('date')->formatPrettyDateTime($dateAdded)) . '</small></p>';
        }

        $actions = $wl->getWorkflowProgressActions();
        $buttons = [];

        if (!empty($actions)) {
            foreach ($actions as $act) {

                $inner = $act->getWorkflowProgressActionStyleInnerButtonLeftHTML() . ' ' . $act->getWorkflowProgressActionLabel() . ' ' . $act->getWorkflowProgressActionStyleInnerButtonRightHTML();

                if ($act->getWorkflowProgressActionURL() != '') {
                    $button = new \HtmlObject\Link($act->getWorkflowProgressActionURL(), $inner);
                } else {
                    $button = new \HtmlObject\Link('#', $inner);
                    $button->setAttribute('data-workflow-task', $act->getWorkflowProgressActionTask());
                }

                if (!empty($act->getWorkflowProgressActionExtraButtonParameters())) {
                    foreach ($act->getWorkflowProgressActionExtraButtonParameters() as $key => $value) {
                        $button->setAttribute($key, $value);
                    }
                }

                // sigh. le hack
                if (strpos($act->getWorkflowProgressActionStyleClass(), 'dialog-launch') > -1) {
                    $button->addClass('dialog-launch');
                }
                $buttons[] = $button;
            }
        }

        if (!empty($displayInline)) {
            echo $form;
            echo implode("\n", $buttons);
            echo '</form>';
        } else {
            echo $cih->notify(array(
                'text' => $text,
                'type' => 'info',
                'form' => $form,
                'icon' => 'fa fa-info-circle',
                'buttons' => $buttons
            ));
        }
    }
}
