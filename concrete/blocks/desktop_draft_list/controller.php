<?php
namespace Concrete\Block\DesktopDraftList;

use Concrete\Core\Block\BlockController;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\PageList;
use Concrete\Core\User\UserInfo;
use Permissions;
use URL;

defined('C5_EXECUTE') or die('Access Denied.');

class Controller extends BlockController
{
    public $helpers = ['form'];
    protected $btTable = 'btDesktopDraftList';
    protected $defaultDraftsPerPage = 10;

    public function getBlockTypeDescription()
    {
        return t('Displays a list of all drafts.');
    }

    public function getBlockTypeName()
    {
        return t('Draft List');
    }

    public function add()
    {
        $this->set('defaultDraftsPerPage', $this->defaultDraftsPerPage);
    }

    public function edit()
    {
        $this->set('defaultDraftsPerPage', $this->defaultDraftsPerPage);
    }

    public function validate($args)
    {
        $e = $this->app->make('helper/validation/error');
        if (!empty($args['draftsPerPage'])) {
            $numbersValidation = $this->app->make('helper/validation/numbers');
            if (!$numbersValidation->integer($args['draftsPerPage'])) {
                $e->add(t('You must specify an integer value for the number of drafts per page.'));
            }
        }

        return $e;
    }

    public function save($args)
    {
        if (empty($args['draftsPerPage'])) {
            $args['draftsPerPage'] = $this->defaultDraftsPerPage;
        }
        parent::save($args);
    }

    public function view()
    {
        $myDrafts = [];
        $showPagination = false;
        $pagination = null;
        $site = $this->app->make('site')->getSite();
        if (is_object($site)) {
            $draftsParentPage = Page::getDraftsParentPage($site);
            if (is_object($draftsParentPage) && !empty($draftsParentPage->getCollectionID())) {
                $list = new PageList();
                $list->setNameSpace('b' . $this->bID);
                $list->filterByParentID($draftsParentPage->getCollectionID());
                $list->includeSystemPages();
                $list->includeInactivePages();
                $list->sortBy('cDateAdded', 'desc');
                $list->setPageVersionToRetrieve($list::PAGE_VERSION_RECENT);
                $list->setItemsPerPage((!empty($this->draftsPerPage)) ? $this->draftsPerPage : $this->defaultDraftsPerPage);
                $pagination = $list->getPagination();
                $drafts = $pagination->getCurrentPageResults();
                if ($pagination->haveToPaginate()) {
                    $showPagination = true;
                    $pagination = $pagination->renderDefaultView();
                }
            }
        }

        if (!empty($drafts)) {
            $date = $this->app->make('helper/date');
            $navigation = $this->app->make('helper/navigation');
            foreach ($drafts as $draft) {
                $dp = new Permissions($draft);
                if ($dp->canEditPageContents()) {
                    $draftName = $draft->getCollectionName();
                    if (empty($draftName)) {
                        $draftName = t('(Untitled)');
                    }
                    $draftUser = null;
                    $draftUserID = $draft->getCollectionUserID();
                    if (!empty($draftUserID)) {
                        $user = UserInfo::getByID($draftUserID);
                        if (is_object($user)) {
                            $draftUser = $user->getUserName();
                        }
                    }
                    $deleteLink = null;
                    if ($dp->canDeletePage()) {
                        $deleteLink = URL::to('/ccm/system/dialogs/page/delete') . '?cID=' . $draft->getCollectionID();
                    }
                    $myDrafts[] = [
                        'link' => $navigation->getLinkToCollection($draft),
                        'id' => $draft->getCollectionID(),
                        'name' => $draftName,
                        'dateAdded' => $date->formatDateTime($draft->getCollectionDateAdded(), false),
                        'user' => $draftUser,
                        'deleteLink' => $deleteLink,
                    ];
                }
            }
        }
        $this->set('drafts', $myDrafts);
        $this->set('showPagination', $showPagination);
        $this->set('pagination', $pagination);
    }
}
