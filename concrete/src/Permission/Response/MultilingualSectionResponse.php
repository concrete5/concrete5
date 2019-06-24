<?php
namespace Concrete\Core\Permission\Response;

defined('C5_EXECUTE') or die('Access Denied.');
class MultilingualSectionResponse extends PageResponse
{
    public function canImportMultilingualSection()
    {
        $u = new \User();

        return $u->isSuperUser();
    }
}
