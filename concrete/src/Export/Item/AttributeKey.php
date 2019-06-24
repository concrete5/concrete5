<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class AttributeKey implements ItemInterface
{

    public function export($key, \SimpleXMLElement $xml)
    {
        /**
         * @var $key Key
         */
        $type = $key->getAttributeType()->getAttributeTypeHandle();
        $categoryHandle = $key->getAttributeKeyCategoryHandle();
        $akey = $xml->addChild('attributekey');
        $akey->addAttribute('handle', $key->getAttributeKeyHandle());

        $akey->addAttribute('name', $key->getAttributeKeyName());
        $akey->addAttribute('package', $key->getPackageHandle());
        $akey->addAttribute('searchable', $key->isAttributeKeySearchable());
        $akey->addAttribute('indexed', $key->isAttributeKeySearchable());
        $akey->addAttribute('type', $type);
        $akey->addAttribute('category', $categoryHandle);

        $key->getController()->exportKey($akey);
        return $akey;
    }

}
