<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\User\Group\Group;
use Concrete\Core\User\Group\GroupList;
use Concrete\Core\User\UserInfo;

defined('C5_EXECUTE') or die('Access Denied.');

class User implements ItemInterface
{

    /**
     * @param $user UserInfo
     * @param \SimpleXMLElement $xml
     * @return \SimpleXMLElement
     */
    public function export($user, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('user');

        // basic information
        $node->addAttribute('username', $user->getUserName());
        $node->addAttribute('email', $user->getUserEmail());
        if (!$user->isActive()) {
            $node->addAttribute('inactive', 1);
        }
        if (!$user->isValidated()) {
            $node->addAttribute('unvalidated', 1);
        }
        if ($timezone = $user->getUserObject()->getUserTimezone()) {
            $node->addAttribute('timezone', $timezone);
        }

        if ($language = $user->getUserDefaultLanguage()) {
            $node->addAttribute('language', $language);
        }

        // attributes
        $category = Category::getByHandle('user');
        $attributes = $category->getController()->getAttributeValues($user->getEntityObject());
        if (count($attributes) > 0) {
            $child = $node->addChild('attributes');
            foreach ($attributes as $av) {
                $ak = $av->getAttributeKey();
                $cnt = $ak->getController();
                $cnt->setAttributeValue($av);
                $attributeKey = $child->addChild('attributekey');
                $attributeKey->addAttribute('handle', $ak->getAttributeKeyHandle());
                $cnt->exportValue($attributeKey);
            }
        }

        // groups
        $list = new GroupList();
        $list->filterByUserID($user->getUserID());
        $groups = $list->getResults();
        if (count($groups)) {
            $child = $node->addChild('groups');
            foreach($groups as $groupObject) {
                $group = $child->addChild('group');
                $group->addAttribute('path', $groupObject->getGroupPath());
            }
        }


        unset($user);
        unset($category);

        return $node;
    }

}