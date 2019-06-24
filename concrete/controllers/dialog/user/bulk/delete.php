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
use Concrete\Core\Support\Facade\Url;

class Delete extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/user/bulk/delete';
    protected $users = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function view()
    {
        $this->set('users', $this->users);
        $this->set('excluded', $this->excluded);
    }

    public function submit()
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
            $pk = PermissionKey::getByHandle('delete_user');
            $pa = $pk->getPermissionAccessObject();
            $workflows = $pa->getWorkflows();
            $workflowAttached = count($workflows);

            foreach ($this->users as $ui) {
                /* @var $ui \Concrete\Core\User\UserInfo: */

                $up = new Permissions($ui);
                /* @var $up \Concrete\Core\Permission\Checker */
                if ($up->canDeleteUser()) {
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

                    if (!in_array('delete', $workflowRequestActions)) {
                        $ui->triggerDelete($u);
                        ++$count;
                    }
                }
            }
        }

        $r->setMessage(t2('%s user deleted', '%s users deleted', $count));
        $r->setTitle(t('Users Deleted'));
        $r->setRedirectURL(Url::to('/dashboard/users/search'));
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
        $pk = PermissionKey::getByHandle('delete_user');
        /* @var $pk \Concrete\Core\Permission\Key\UserKey */
        if (!$pk->can()) {
            $this->canEdit = false;
            $this->set('users', []);

            return $this->canEdit;
        }

        $u = new User();
        $excluded_user_ids = [];
        $excluded_user_ids[] = $u->getUserID(); // can't delete yourself
        $excluded_user_ids[] = USER_SUPER_ID;   // can't delete the super user (admin)

        $sh = Core::make('helper/security');
        if (is_array($this->request('item'))) {
            $this->users = [];
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
