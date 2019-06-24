<?php
namespace Concrete\Core\Multilingual\Page\Section\Processor;

use Concrete\Core\Foundation\Processor\ActionInterface;
use Concrete\Core\Foundation\Processor\TaskInterface;
use Concrete\Core\Page\Page;

defined('C5_EXECUTE') or die('Access Denied.');

class ReplaceContentLinksTask implements TaskInterface
{
    public function execute(ActionInterface $action)
    {
        $target = $action->getTarget();
        $subject = $action->getSubject();

        \Cache::disableAll();
        $c = Page::getByID($subject['cID']);
        if (is_object($c) && !$c->isError()) {
            $blocks = $c->getBlocks();
            $nvc = $c->getVersionToModify();
            $isApproved = $c->getVersionObject()->isApproved();
            foreach ($blocks as $b) {
                if ($b->getBlockTypeHandle() == 'content') {
                    $content = $b->getController()->content;
                    $content = preg_replace_callback(
                        '/{CCM:CID_([0-9]+)}/i',
                        function ($matches) use ($subject, $target) {
                            $cID = $matches[1];
                            if ($cID > 0) {
                                $link = Page::getByID($cID, 'ACTIVE');
                                $section = $target->getSection();
                                if (is_object($section)) {
                                    $relatedID = $section->getTranslatedPageID($link);
                                    if ($relatedID) {
                                        return sprintf('{CCM:CID_%s}', $relatedID);
                                    }
                                }
                            }
                        },
                        $content);
                    $ob = $b;
                    // replace the block with the version of the block in the later version (if applicable)
                    $b2 = \Block::getByID($b->getBlockID(), $nvc, $b->getAreaHandle());

                    if ($b2->isAlias()) {
                        $nb = $ob->duplicate($nvc);
                        $b2->deleteBlock();
                        $b2 = clone $nb;
                    }
                    $data = array('content' => $content);
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
