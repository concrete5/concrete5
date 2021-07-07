<?php
namespace Concrete\Core\Search\Pagination;

use Concrete\Core\Search\ItemList\ItemList;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

/**
 * Processes a thousand requests and builds pagination out of them.
 * This is slow on larger sites, but will yield accurate pagination even with permissions
 * Caveat: The most you can process in one result set is 1000 results. Otherwise, use PagerPagination
 * or disable permissions.
 */
class PermissionablePagination extends Pagination
{
    protected $maxResultsToProcessAtOnce = 1000;

    public function __construct(ItemList $itemList)
    {
        $itemList->getQueryObject()->setMaxResults($this->maxResultsToProcessAtOnce);
        $results = $itemList->getResults();
        $adapter = new ArrayAdapter($results);
        $this->list = $itemList;

        Pagerfanta::__construct($adapter);
    }

    public function getCurrentPageResults()
    {
        return Pagerfanta::getCurrentPageResults();
    }
}
