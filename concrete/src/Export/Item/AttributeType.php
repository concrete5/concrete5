<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeType implements ItemInterface
{

    public function export($type, \SimpleXMLElement $xml)
    {
        $db = \Database::connection();
        $atype = $xml->addChild('attributetype');
        $atype->addAttribute('handle', $type->getAttributeTypeHandle());
        $atype->addAttribute('package', $type->getPackageHandle());
        $categories = $db->GetCol(
            'select akCategoryHandle from AttributeKeyCategories inner join AttributeTypeCategories where AttributeKeyCategories.akCategoryID = AttributeTypeCategories.akCategoryID and AttributeTypeCategories.atID = ?',
            array($type->getAttributeTypeID())
        );
        if (count($categories) > 0) {
            $cat = $atype->addChild('categories');
            foreach ($categories as $catHandle) {
                $cat->addChild('category')->addAttribute('handle', $catHandle);
            }
        }
        return $atype;
    }

}
