<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\Pager\PagerProviderInterface;
use Concrete\Core\Search\Pagination\Adapter\PagerAdapter;
use Concrete\Core\Search\Pagination\View\ViewRenderer;
use Concrete\Core\Support\Facade\Facade;
use Pagerfanta\Pagerfanta;

class PagerPagination extends Pagination
{
    protected $list;
    protected $app;
    protected $request;
    protected $cursor;
    protected $hasNextPage;
    protected $currentPageResults;

    public function __construct(PagerProviderInterface $itemList)
    {
        $adapter = new PagerAdapter($itemList);
        $this->list = $itemList;
        $this->app = Facade::getFacadeApplication();

        $manager = $itemList->getPagerManager();

        $factory = $itemList->getPagerVariableFactory();
        $start = $factory->getCurrentCursor();
        if ($start) {
            $this->cursor = $start;
            $manager->displaySegmentAtCursor($start, $itemList);
        }

        Pagerfanta::__construct($adapter);
    }

    public function getLastResult()
    {
        $currentPageResults = $this->getCurrentPageResults();

        return end($currentPageResults);
    }

    public function advanceToNextPage()
    {
        $results = $this->getCurrentPageResults();
        $lastResult = end($results);

        unset($this->currentPageResults);
        unset($this->hasNextPage);

        $manager = $this->list->getPagerManager();
        $adapter = $this->getAdapter();
        $this->cursor = $lastResult;
        $manager->displaySegmentAtCursor($lastResult, $this->list);
    }

    public function renderView($driver = 'application', $arguments = [])
    {
        $manager = $this->app->make('manager/view/pagination/pager');
        $driver = $manager->driver($driver);
        $renderer = new ViewRenderer($this, $driver);

        return $renderer->render($arguments);
    }

    public function getTotalPages()
    {
        return -1;
    }

    public function getRouteCollectionFunction()
    {
        $urlHelper = $this->app->make('helper/url');
        $list = $this->getItemListObject();
        /* @var PagerProviderInterface $list */
        $routeCollectionFunction = function ($page) use ($list, $urlHelper) {
            $args = [];
            if ($list->getActiveSortColumn()) {
                $args[$list->getQuerySortColumnParameter()] = $list->getActiveSortColumn();
            }

            if ($list->getActiveSortDirection()) {
                $args[$list->getQuerySortDirectionParameter()] = $list->getActiveSortDirection();
            }

            $factory = $list->getPagerVariableFactory();
            if ($page == 2) {
                // next page
                $args[$factory->getCursorVariableName()] = $factory->getNextCursorValue($this);
            } elseif ($page == 0) {
                $args[$factory->getCursorVariableName()] = $factory->getPreviousCursorValue($this);
            }

            if ($this->baseURL) {
                $url = $urlHelper->setVariable($args, false, $this->baseURL);
            } else {
                $url = $urlHelper->setVariable($args);
            }

            return h($url);
        };

        return $routeCollectionFunction;
    }

    public function haveToPaginate()
    {
        return $this->hasNextPage() || $this->hasPreviousPage();
    }

    public function hasNextPage()
    {
        if (isset($this->hasNextPage)) {
            return $this->hasNextPage;
        }

        if (!isset($this->currentPageResults)) {
            $this->currentPageResults = $this->getCurrentPageResults();
        }
        $manager = $this->list->getPagerManager();
        $lastResult = end($this->currentPageResults);

        if ($lastResult) {
            $manager->displaySegmentAtCursor($lastResult, $this->list);
            $next = $this->getAdapter()->getSlice(0, 1);

            // reset the cursor
            if ($this->cursor) {
                $manager->displaySegmentAtCursor($this->cursor, $this->list);
            }
            if ($next) {
                $this->hasNextPage = true;

                return $this->hasNextPage;
            }
        }
        $this->hasNextPage = false;

        return $this->hasNextPage;
    }

    public function hasPreviousPage()
    {
        return $this->cursor != null;
    }

    public function getCurrentPageResults()
    {
        if (!isset($this->currentPageResults)) {
            $length = $this->getMaxPerPage();
            $this->currentPageResults = $this->getAdapter()->getSlice(0, $length);
        }

        return $this->currentPageResults;
    }
}
