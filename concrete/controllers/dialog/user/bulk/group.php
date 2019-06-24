<?php
namespace Concrete\Controller\Dialog\User\Bulk;
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Controller\Backend\UserInterface as BackendInterfaceController;
use PermissionKey;
use Permissions;
use UserInfo;
use Core;
use GroupList;
use Concrete\Core\User\Group\Group as UserGroup;
use Concrete\Core\User\EditResponse as UserEditResponse;

class Group extends BackendInterfaceController
{
    protected $viewPath = '/dialogs/user/bulk/group';
    protected $users = [];
    protected $canEdit = false;
    protected $excluded = false;

    public function groupadd()
    {
        $this->form('add');
    }

    public function groupremove()
    {
        $this->form('remove');
    }

    public function groupremovesubmit()
    {
        $this->handler('remove');
    }

    public function groupaddsubmit()
    {
        $this->handler('add');
    }

    private function form($function)
    {
        $this->set('users', $this->users);
        $this->set('excluded', $this->excluded);
        $this->set('gArray', $this->getGroups());
        $this->set('function', $function);
    }

    private function getGroups()
    {
        $gl = new GroupList();
        $gl->sortBy('gID', 'asc');
        $groups = $gl->getResults();
        $return = [];
        foreach ($groups as $v) {
            $return[$v->getGroupID()] = $v->getGroupDisplayName(false);
        }

        return $return;
    }

    private function handler($function)
    {
        $r = new UserEditResponse();
        if (!$this->validateAction()) {
            $r->setError(new \Exception(t('Invalid Token')));
            $r->outputJSON();
            \Core::shutdown();
        }

        $groupIDs = $this->post('groupIDs');
        if (!$groupIDs || !is_array($groupIDs)) {
            $r->setError(new \Exception(t('No groups selected')));
            $r->outputJSON();
            \Core::shutdown();
        }

        $sh = Core::make('helper/security');
        $dh = Core::make('helper/date');
        /* @var $dh \Concrete\Core\Localization\Service\Date */

        $r->setUsers($this->users);

        $updated = [];

        foreach ($groupIDs as $gID) {
            $group = UserGroup::getByID($sh->sanitizeInt($gID));

            foreach ($this->users as $ui) {
                /* @var $ui \Concrete\Core\User\UserInfo: */

                $up = new Permissions($ui);
                /* @var $up \Concrete\Core\Permission\Checker */
                if (!$up->canAssignGroup()) {
                    continue;
                }

                $uo = $ui->getUserObject();

                if ('add' == $function && $uo->inGroup($group)) {
                    continue;
                }
                if ('remove' == $function && !$uo->inGroup($group)) {
                    continue;
                }

                if ('add' == $function) {
                    $uo->enterGroup($group);
                    $obj = new \stdClass();
                    $obj->gDisplayName = $group->getGroupDisplayName();
                    $obj->gID = $group->getGroupID();
                    $obj->gDateTimeEntered = $dh->formatDateTime($group->getGroupDateTimeEntered($uo));
                    $r->setAdditionalDataAttribute('groups', [$obj]);
                } else {
                    $uo->exitGroup($group);
                    $obj = new \stdClass();
                    $obj->gID = $group->getGroupID();
                    $r->setAdditionalDataAttribute('group', $obj);
                }
                $updated[$ui->getUserID()] = true;
            }
        }

        $r->setMessage(t2('%s user updated', '%s users updated', count($updated)));
        $r->setTitle(t('User Groups Updated'));
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
        $pk = PermissionKey::getByHandle('assign_group');
        /* @var $pk \Concrete\Core\Permission\Key\UserKey */
        if (!$pk->can()) {
            $this->canEdit = false;
            $this->set('users', []);

            return $this->canEdit;
        }

        $excluded_user_ids = [];
        $excluded_user_ids[] = USER_SUPER_ID;   // probably unwise to update groups of super user (admin)

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
