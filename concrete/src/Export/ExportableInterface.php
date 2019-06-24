<?php
namespace Concrete\Core\Export;

use Concrete\Core\Export\Item\ItemInterface;

defined('C5_EXECUTE') or die('Access Denied.');

interface ExportableInterface
{

    /**
     * @return ItemInterface
     */
    public function getExporter();

}
