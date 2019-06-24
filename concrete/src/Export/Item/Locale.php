<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Attribute\Key\Category;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Entity\LocaleTrait;
use Concrete\Core\Export\Item\ItemInterface;
use Concrete\Core\Export\ExportableInterface;
use Concrete\Core\Localization\Locale\LocaleInterface;

defined('C5_EXECUTE') or die('Access Denied.');

class Locale implements ItemInterface
{

    /**
     * @param LocaleInterface $locale
     * @param \SimpleXMLElement $xml
     * @return mixed
     */
    public function export($locale, \SimpleXMLElement $xml)
    {
        $node = $xml->addChild('locale');
        $node->addAttribute('language', $locale->getLanguage());
        $node->addAttribute('country', $locale->getCountry());
        return $node;
    }

}
