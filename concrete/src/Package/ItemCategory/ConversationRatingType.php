<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Conversation\Rating\Type;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die('Access Denied.');

class ConversationRatingType extends AbstractCategory
{

    public function getItemCategoryDisplayName()
    {
        return t('Conversation Rating Type');
    }

    public function getItemName($type)
    {
        return $type->getConversationRatingTypeDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }

}
