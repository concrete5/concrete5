<?php
namespace Concrete\Controller\Dialog\User\Bulk;
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use Concrete\Core\Application\EditResponse as UserEditResponse;
use PermissionKey;
use Permissions;
use UserInfo;
use Core;
use Concrete\Core\User\User;
use Concrete\Core\Workflow\Progress\UserProgress as UserWorkflowProgress;

class Activate extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/user/bulk/activate';
    protected $users = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function activate()
    {
        $this->form('activate');
    }

    public function deactivate()
    {
        $this->form('deactivate');
    }

    public function deactivatesubmit()
    {
        $this->handler('deactivate');
    }

    public function activatesubmit()
    {
        $this->handler('activate');
    }

    private function form($function)
    {
        $this->set('users', $this->users);
        $this->set('excluded', $this->excluded);
        $this->set('function', $function);
    }

    private function handler($function)
    {
        $r = new UserEditResponse();
        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            \Core::shutdown();
        }

        $u = new User();
        $count = 0;
        if (count($this->users) > 0) {
            // check if workflow is attached to this request
            $pk = PermissionKey::getByHandle('activate_user');
            $pa = $pk->getPermissionAccessObject();
            $workflows = $pa->getWorkflows();
            $workflowAttached = count($workflows);

            foreach ($this->users as $ui) {
                /* @var $ui \Concrete\Core\User\UserInfo: */

                if ('activate' == $function && $ui->isActive()) {
                    continue;
                }
                if ('deactivate' == $function && !$ui->isActive()) {
                    continue;
                }

                $up = new Permissions($ui);
                /* @var $up \Concrete\Core\Permission\Checker */
                if ($up->canActivateUser()) {
                    $workflowRequestActions = [];
                    // Fetch triggered workflow request actions of current user when workflow is attached to this request
                    // so that same request action won't trigger twice.
                    if ($workflowAttached) {
                        $workflowList = UserWorkflowProgress::getList($ui->getUserID());

                        if (count($workflowList) > 0) {
                            foreach ($workflowList as $wp) {
                                $wr = $wp->getWorkflowRequestObject();
                                $workflowRequestActions[] = $wr->getRequestAction();
                            }
                        }
                    }

                    if (!in_array($function, $workflowRequestActions)) {
                        if ('activate' == $function) {
                            $ui->triggerActivate('activate', $u->getUserID());
                        } else {
                            $ui->triggerDeactivate($u->getUserID());
                        }
                        ++$count;
                    }
                }
            }
        }

        if ('activate' == $function) {
            $r->setMessage(t2('%s user activated', '%s users activated', $count));
            $r->setTitle(t('Users Activated'));
        } else {
            $r->setMessage(t2('%s user deactivated', '%s users deactivated', $count));
            $r->setTitle(t('Users Deactivated'));
        }

        //$response->setRedirectURL(Url::to('/dashboard/users/search'));
        $r->outputJSON();
    }

    protected function canAccess()
    {
        $tp = Core::make('helper/concrete/user');
        /* @var $tp \Concrete\Core\Application\Service\User */

        if ($tp->canAccessUserSearchInterface()) {
            $this->populateUsers();
        }

        return $this->canEdit;
    }

    protected function populateUsers()
    {
        $pk = PermissionKey::getByHandle('activate_user');
        /* @var $pk \Concrete\Core\Permission\Key\UserKey */
        if (!$pk->can()) {
            $this->canEdit = false;
            $this->set('users', []);

            return $this->canEdit;
        }

        $u = new User();
        $excluded_user_ids = [];
        $excluded_user_ids[] = $u->getUserID(); // can't de/activate yourself
        $excluded_user_ids[] = USER_SUPER_ID;   // can't de/activatethe super user (admin)

        $sh = Core::make('helper/security');
        if (is_array($this->request('item'))) {
            foreach ($this->request('item') as $uID) {
                $ui = UserInfo::getByID($sh->sanitizeInt($uID));
                if (is_object($ui) && !$ui->isError()) {
                    $up = new Permissions($ui);
                    /* @var $up \Concrete\Core\Permission\Checker */
                    if (!$up->canViewUser() || (in_array($ui->getUserID(), $excluded_user_ids))) {
                        $this->excluded = true;
                    } else {
                        $this->users[] = $ui;
                    }
                }
            }
        }

        $this->canEdit = true;

        return $this->canEdit;
    }
}
