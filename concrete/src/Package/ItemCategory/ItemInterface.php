<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

interface ItemInterface
{

    function hasItems(Package $package);
    function getItems(Package $package);
    function getItemName($mixed);
    function getItemCategoryDisplayName();
    function removeItems(Package $package);
    function renderList(Package $package);

}
