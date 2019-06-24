<?php
namespace Concrete\Core\Import\Item\Express;

use Concrete\Core\Entity\Express\Entity;

defined('C5_EXECUTE') or die('Access Denied.');

interface ItemInterface
{

    public function import(\SimpleXMLElement $element, Entity $entity);

}
