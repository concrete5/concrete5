<?php

namespace Concrete\Core\User\Search\Field\Field;

use Concrete\Core\Search\Field\AbstractField;
use Concrete\Core\Search\ItemList\ItemList;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\UserList;

class UserGroupField extends AbstractField
{
    protected $requestVariables = [
        'gID', 'uGroupIn',
    ];

    public function getKey()
    {
        return 'user_group';
    }

    public function getDisplayName()
    {
        return t('User Group');
    }

    /**
     * @param UserList $list
     * @param $request
     */
    public function filterList(ItemList $list)
    {
        $filterGroups = [];
        if (isset($this->data['gID']) && is_array($this->data['gID'])) {
            foreach ($this->data['gID'] as $gID) {
                $g = \Group::getByID($gID);
                if (is_object($g)) {
                    $gp = new \Permissions($g);
                    if ($gp->canSearchUsersInGroup()) {
                        $filterGroups[] = $g;
                    }
                }
            }
        }
        $inGroup = true;
        if ($this->data['uGroupIn'] == 'not') {
            $inGroup = false;
        }
        $list->filterByInAnyGroup($filterGroups, $inGroup);
    }

    public function renderSearchField()
    {
        $gl = new GroupList();
        $g1 = $gl->getResults();
        $html = '<div class="form-group"><select multiple name="gID[]" class="ccm-enhanced-select">';
        foreach ($g1 as $g) {
            $gp = new \Permissions($g);
            if ($gp->canSearchUsersInGroup($g)) {
                $html .= '<option value="' . $g->getGroupID() . '" ';
                if (is_array($this->data['gID']) && in_array($g->getGroupID(), $this->data['gID'])) {
                    $html .= 'selected="selected" ';
                }
                $html .= '>' . $g->getGroupDisplayName() . '</option>';
            }
        }
        $html .= '</select></div><br/>';

        $html .= '<div class="form-group"><select name="uGroupIn" class="form-select">';
        $html .= '<option value="in"' . ($this->data['uGroupIn'] == 'in' ? ' selected' : '') . '>' . t('Search for users in group(s)') . '</option>';
        $html .= '<option value="not"' . ($this->data['uGroupIn'] == 'not' ? ' selected' : '') . '>' . t('Search for users not included in group(s)') . '</option>';
        $html .= '</select></div>';

        return $html;
    }
}
