<?php
namespace Concrete\Core\Multilingual\Page\Section\Processor;

use Concrete\Core\Foundation\Processor\ActionInterface;
use Concrete\Core\Foundation\Processor\TaskInterface;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

class ReplaceBlockPageRelationsTask implements TaskInterface
{
    public function execute(ActionInterface $action)
    {
        $target = $action->getTarget();
        $subject = $action->getSubject();

        \Cache::disableAll();
        $c = Page::getByID($subject['cID']);
        $db = \Database::connection();
        if (is_object($c) && !$c->isError()) {
            $blocks = $c->getBlocks();
            $nvc = $c->getVersionToModify();
            $isApproved = $c->getVersionObject()->isApproved();
            foreach ($blocks as $b) {
                $controller = $b->getController();
                $pageColumns = $controller->getBlockTypeExportPageColumns();
                if (count($pageColumns)) {
                    $columns = $db->MetaColumnNames($controller->getBlockTypeDatabaseTable());
                    $data = array();
                    $record = $controller->getBlockControllerData();
                    foreach ($columns as $key) {
                        $data[$key] = isset($record->{$key}) ? $record->{$key} : null;
                    }

                    foreach ($pageColumns as $column) {
                        $cID = empty($data[$column]) ? 0 : $data[$column];
                        if ($cID > 0) {
                            $link = Page::getByID($cID, 'ACTIVE');
                            $section = $target->getSection();
                            if (is_object($section)) {
                                $relatedID = $section->getTranslatedPageID($link);
                                if ($relatedID) {
                                    $data[$column] = $relatedID;
                                }
                            }
                        }
                    }

                    unset($data['bID']);

                    $ob = $b;
                    // replace the block with the version of the block in the later version (if applicable)
                    $b2 = \Block::getByID($b->getBlockID(), $nvc, $b->getAreaHandle());
                    if ($b2->isAlias()) {
                        $nb = $ob->duplicate($nvc);
                        $b2->deleteBlock();
                        $b2 = clone $nb;
                    }
                    $b2->update($data);
                }
            }
            if ($isApproved) {
                $nvc->getVersionObject()->approve();
            }
        }
    }

    public function finish(ActionInterface $action)
    {
        return;
    }
}
