<?php
namespace Concrete\Core\Import;

use Concrete\Core\Import\Item\ItemInterface;

defined('C5_EXECUTE') or die('Access Denied.');

interface ImportableInterface
{

    /**
     * @return ItemInterface
     */
    public function getImporter();

}
