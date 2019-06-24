<?php
namespace Concrete\Controller\SinglePage\Dashboard\System\Multilingual;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Multilingual\Page\Section\Section as MultilingualSection;
use Concrete\Core\Multilingual\Page\PageList as MultilingualPageList;
use Concrete\Core\Page\Controller\DashboardSitePageController;

defined('C5_EXECUTE') or die('Access Denied.');

class PageReport extends DashboardSitePageController
{
    public $helpers = array('form');

    public function view()
    {
        $this->requireAsset('core/sitemap');
        $list = MultilingualSection::getList($this->getSite());
        $sections = array();
        usort($list, function ($item) {
           if ($item->getLocale() == $this->getSite()->getDefaultLocale()->getLocale()) {
               return -1;
           } else {
               return 1;
           }
        });
        foreach ($list as $pc) {
            $sections[$pc->getCollectionID()] = $pc->getLanguageText() . " (" . $pc->getLocale() . ")";
        }
        $this->set('sections', $sections);
        $this->set('sectionList', $list);

        if (!isset($_REQUEST['sectionID']) && (count($sections) > 0)) {
            foreach ($sections as $key => $value) {
                $sectionID = $key;
                break;
            }
        } else {
            $sectionID = $_REQUEST['sectionID'];
        }

        if (!isset($_REQUEST['targets']) && (count($sections) > 1)) {
            $i = 0;
            foreach ($sections as $key => $value) {
                if ($key != $sectionID) {
                    $targets[$key] = $key;
                    break;
                }
                ++$i;
            }
        } else {
            $targets = isset($_REQUEST['targets']) ? $_REQUEST['targets'] : null;
        }
        if (!isset($targets) || (!is_array($targets))) {
            $targets = array();
        }

        $targetList = array();
        foreach ($targets as $key => $value) {
            $targetList[] = MultilingualSection::getByID($key);
        }
        $this->set('targets', $targets);
        $this->set('targetList', $targetList);
        $this->set('sectionID', $sectionID);
        $this->set('fh', \Core::make('multilingual/interface/flag'));

        if (isset($sectionID) && $sectionID > 0) {
            $pl = new MultilingualPageList();
            $pc = \Page::getByID($sectionID);
            $pl->setSiteTreeObject($pc->getSiteTreeObject());
            $path = $pc->getCollectionPath();
            if (strlen($path) > 1) {
                $pl->filterByPath($path);
            }

            if (isset($_REQUEST['keywords']) && $_REQUEST['keywords']) {
                $pl->filterByName($_REQUEST['keywords']);
            }

            $pl->setItemsPerPage(25);
            if (!isset($_REQUEST['showAllPages']) || !$_REQUEST['showAllPages']) {
                $pl->filterByMissingTargets($targetList);
            }
            $pagination = $pl->getPagination();
            $this->set('pagination', $pagination);
            $this->set('pages', $pagination->getCurrentPageResults());
            $this->set('section', MultilingualSection::getByID($sectionID));
            $this->set('pl', $pl);
        }
    }
}
