<?php
namespace Concrete\Core\Export\Item;

use Concrete\Core\Export\ExportableInterface;

defined('C5_EXECUTE') or die('Access Denied.');

interface ItemInterface
{

    public function export($mixed, \SimpleXMLElement $element);

}
