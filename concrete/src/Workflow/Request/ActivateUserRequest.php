<?php
namespace Concrete\Core\Workflow\Request;

use Concrete\Core\Entity\User\User;
use Concrete\Core\User\Event\DeactivateUser;
use Concrete\Core\User\UserInfo;
use Doctrine\ORM\EntityManager;
use PermissionKey;
use Loader;
use Config;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Workflow\Description as WorkflowDescription;
use Concrete\Core\Workflow\Progress\Progress as WorkflowProgress;
use Concrete\Core\Workflow\Progress\Action\Action as WorkflowProgressAction;

defined('C5_EXECUTE') or die('Access Denied.');

class ActivateUserRequest extends UserRequest
{
    protected $requestAction = 'activate';

    public function __construct()
    {
        $pk = PermissionKey::getByHandle('activate_user');
        parent::__construct($pk);
    }

    public function setRequestAction($action)
    {
        $this->requestAction = $action;
    }

    public function isActivationRequest()
    {
        return $this->requestAction == 'activate';
    }

    public function isRegisterActivationRequest()
    {
        return $this->requestAction == 'register_activate';
    }

    public function isDeactivationRequest()
    {
        return $this->requestAction == 'deactivate';
    }

    public function getWorkflowRequestDescriptionObject()
    {
        $d = new WorkflowDescription();
        $ui = UserInfo::getByID($this->getRequestedUserID());
        if (!is_object($ui)) {
            $d->setEmailDescription(t("Invalid user."));
            $d->setDescription(t("Invalid user."));
            $d->setInContextDescription(t("Invalid user."));
            return $d;
        }

        $app = Application::getFacadeApplication();
        $url = $app['url/manager'];
        $link = (string) $url->resolve([
            '/dashboard/users/search/view',
            $ui->getUserID()
        ]);
        if ($this->isDeactivationRequest()) {
            $d->setEmailDescription(t(
                "User account \"%s\" has pending deactivation request which needs to be approved.",
                $ui->getUserName()
            ));
            $d->setDescription(t(
                "User <a target=\"_blank\" href=\"%s\">%s</a> submitted for Deactivation.",
                $link,
                $ui->getUserName()
            ));
            $d->setInContextDescription(t("User submitted for Deactivation."));
        } else {
            $d->setEmailDescription(t(
                "User account \"%s\" has pending activation request which needs to be approved.",
                $ui->getUserName()
            ));
            $d->setDescription(t(
                "User <a target=\"_blank\" href=\"%s\">%s</a> submitted for Approval.",
                $link,
                $ui->getUserName()
            ));
            $d->setInContextDescription(t("User submitted for Approval."));
        }
        $d->setShortStatus(t("Pending"));

        return $d;
    }

    public function approve(WorkflowProgress $wp)
    {
        $ui = UserInfo::getByID($this->getRequestedUserID());
        $wpr = parent::approve($wp);

        $app = Application::getFacadeApplication();
        $urlm = $app['url/manager'];
        if ($this->isDeactivationRequest()) {
            $wpr->message = t("User %s has been deactivated.", $ui->getUserName());
            $url = (string) $urlm->resolve([
                '/dashboard/users/search/view',
                $this->getRequestedUserID(),
                'deactivated'
            ]);
            $wpr->setWorkflowProgressResponseURL($url);

            $em = $app->make(EntityManager::class);
            $requested = $em->find(User::class, $this->getRequestedUserID());
            $requester = $em->find(User::class, $this->getRequesterUserID());

            $event = DeactivateUser::create($requested, $requester);
            $director = $app->make('director');

            $director->dispatch('on_before_user_deactivate', $event);
            $ui->deactivate();
            $director->dispatch('on_after_user_deactivate', $event);
        } else {
            $wpr->message = t("User %s has been activated.", $ui->getUserName());
            $url = (string) $urlm->resolve([
                '/dashboard/users/search/view',
                $this->getRequestedUserID(),
                'activated'
            ]);
            $wpr->setWorkflowProgressResponseURL($url);
            $ui->activate();
            $this->sendActivationEmail($ui);
        }

        return $wpr;
    }

    public function sendActivationEmail(UserInfo $ui)
    {
        $mh = Loader::helper('mail');
        $mh->to($ui->getUserEmail());
        if (Config::get('concrete.email.register_notification.address')) {
            $mh->from(Config::get('concrete.email.register_notification.address'), t('Website Registration Notification'));
        } else {
            $adminUser = UserInfo::getByID(USER_SUPER_ID);
            $mh->from($adminUser->getUserEmail(), t('Website Registration Notification'));
        }
        $mh->addParameter('uID', $ui->getUserID());
        $mh->addParameter('user', $ui);
        $mh->addParameter('uName', $ui->getUserName());
        $mh->addParameter('uEmail', $ui->getUserEmail());
        $mh->addParameter('siteName', \Core::make('site')->getSite()->getSiteName());
        $mh->load('user_registered_approval_complete');
        $mh->sendMail();
    }

    /**
     * after caneling activate(register activate) request, do nothing
     *
     * @return object
     */
    public function cancel(WorkflowProgress $wp)
    {
        $wpr = parent::cancel($wp);
        if ($this->isDeactivationRequest()) {
            $wpr->message = t("User deactivation request has been cancelled.");
        } else {
            $wpr->message = t("User activation request has been cancelled.");
        }

        return $wpr;
    }

    public function getWorkflowRequestStyleClass()
    {
        return 'info';
    }

    public function getWorkflowRequestApproveButtonClass()
    {
        return '';
    }

    public function getWorkflowRequestApproveButtonInnerButtonRightHTML()
    {
        return '<i class="fa fa-thumbs-o-up"></i>';
    }

    public function getWorkflowRequestApproveButtonText()
    {
        if ($this->isDeactivationRequest()) {
            return t('Deactivate');
        } else {
            return t('Activate');
        }
    }

    public function getWorkflowRequestAdditionalActions(WorkflowProgress $wp)
    {
        $buttons = array();
        $button = new WorkflowProgressAction();
        $button->setWorkflowProgressActionLabel(t('Review'));
        $button->addWorkflowProgressActionButtonParameter('dialog-title', t('User Details'));
        $button->addWorkflowProgressActionButtonParameter('dialog-width', '420');
        $button->addWorkflowProgressActionButtonParameter('dialog-height', '310');
        $button->setWorkflowProgressActionURL(\URL::to('/ccm/system/dialogs/user/details') . '?uID=' . $this->getRequestedUserID());
        $button->setWorkflowProgressActionStyleClass('dialog-launch');
        $buttons[] = $button;

        return $buttons;
    }

    /**
     * Gets the translated text of action of user workflow request
     *
     * @return string
     */
    public function getRequestActionText()
    {
        if ($this->isDeactivationRequest()) {
            return t("Deactivation");
        } else {
            return t("Activation");
        }
    }
}
