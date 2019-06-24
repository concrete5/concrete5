<?php
namespace Concrete\Block\DesktopWaitingForMe;

defined('C5_EXECUTE') or die('Access Denied.');
use Concrete\Core\Block\BlockController;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Notification\Alert\Filter\FilterListFactory;
use Concrete\Core\Workflow\Progress\Category;
use Core;
use Concrete\Core\Notification\Alert\AlertList;

class Controller extends BlockController
{
    public $helpers = array('form');

    protected $btInterfaceWidth = 450;
    protected $btInterfaceHeight = 560;

    public function getBlockTypeDescription()
    {
        return t("Displays workflow actions waiting for you.");
    }

    public function getBlockTypeName()
    {
        return t("Waiting for Me");
    }

    public function view()
    {
        $this->requireAsset('core/notification');

        $filterValues = ['' => t('** Show All')];

        $factory = $this->app->make(FilterListFactory::class);
        $filterList = $factory->createList();
        $filters = $filterList->getFilters();
        foreach($filters as $filter) {
            $filterValues[$filter->getKey()] = $filter->getName();
        }

        $u = new \User();
        $list = $this->app->make(AlertList::class, ['user' => $u]);
        $filter = (string) $this->request->query->get('filter');
        if ($filter !== '') {
            $filterObject = $filterList->getFilterByKey($filter);
            if ($filterObject) {
                $filterObject->filterAlertList($list);
            } else {
                $filter = '';
            }
        }
        $pagination = $list->getPagination();
        $alerts = $pagination->getCurrentPageResults();
        if (!$alerts) {
            $alerts = [];
        }

        $this->set('items', $alerts);
        $this->set('filterValues', $filterValues);
        $this->set('token', $this->app->make('token'));
        $this->set('pagination', $pagination);
        $this->set('filter', $filter);
    }

    public function action_reload_results()
    {
        $b = $this->getBlockObject();
        $bv = new BlockView($b);
        $bv->render('view');
        exit;
    }

}
