<?php
namespace Concrete\Core\Search\ItemList;

use Concrete\Core\Search\StickyRequest;
use Pagerfanta\Exception\OutOfRangeCurrentPageException;
use Pagerfanta\Exception\LessThan1CurrentPageException;

abstract class ItemList
{
    protected $sortColumnParameter = 'ccm_order_by';
    protected $sortDirectionParameter = 'ccm_order_by_direction';
    protected $paginationPageParameter = 'ccm_paging_p';
    protected $sortBy;
    protected $sortByDirection;

    // This still checks the auto sort columns if set to true –
    // we just turn it off to save processing in the attributed item list (so it doesn't have to instantiate
    // all those objects if it's not necessary)
    protected $enableAutomaticSorting = true;
    protected $autoSortColumns = [];

    protected $itemsPerPage = -1; // determined by the pagination object.
    protected $debug = false;

    abstract protected function executeSortBy($field, $direction = 'asc');

    protected function executeSanitizedSortBy($field, $direction)
    {
        $this->executeSortBy($field, $direction);
    }

    abstract public function executeGetResults();

    abstract public function getResult($mixed);

    abstract public function debugStart();

    abstract public function debugStop();

    /**
     * @return \Concrete\Core\Search\Pagination\Pagination
     */
    abstract protected function createPaginationObject();

    public function debug()
    {
        $this->debug = true;
    }

    public function isDebugged()
    {
        return $this->debug;
    }

    public function sortBy($field, $direction = 'asc')
    {
        $this->sortBy = $field;
        $this->sortByDirection = $direction;
        $this->executeSortBy($field, $direction);
    }

    public function sanitizedSortBy($field, $direction = 'asc')
    {
        $this->sortBy = $field;
        $this->sortByDirection = $direction;
        $this->executeSanitizedSortBy($field, $direction);
    }

    /** Returns a full array of results. */
    public function getResults()
    {
        $results = [];

        $this->debugStart();

        $executeResults = $this->executeGetResults();

        $this->debugStop();

        foreach ($executeResults as $result) {
            $r = $this->getResult($result);
            if ($r != null) {
                $results[] = $r;
            }
        }

        return $results;
    }

    public function getActiveSortColumn()
    {
        return $this->sortBy;
    }

    public function isActiveSortColumn($field)
    {
        return $this->sortBy == $field;
    }

    public function disableAutomaticSorting()
    {
        $this->enableAutomaticSorting = false;
    }

    public function getSortClassName($column)
    {
        $class = false;
        if ($this->isActiveSortColumn($column)) {
            $class = 'ccm-results-list-active-sort-';
            if ($this->getActiveSortDirection() == 'desc') {
                $class .= 'desc';
            } else {
                $class .= 'asc';
            }
        }

        return $class;
    }

    public function getSortURL($column, $dir = 'asc', $url = false)
    {
        $uh = \Core::make("helper/url");
        if ($this->isActiveSortColumn($column) && $this->getActiveSortDirection() == $dir) {
            $dir = ($dir == 'asc') ? 'desc' : 'asc';
        }

        $args = [
            $this->getQuerySortColumnParameter() => $column,
            $this->getQuerySortDirectionParameter() => $dir,
        ];

        $url = $uh->setVariable($args, false, $url);

        return strip_tags($url);
    }

    /** @var \Concrete\Core\Search\Pagination\Pagination */
    protected $pagination;

    public function getActiveSortDirection()
    {
        return $this->sortByDirection;
    }

    public function getQuerySortColumnParameter()
    {
        return $this->sortColumnParameter;
    }

    public function getQueryPaginationPageParameter()
    {
        return $this->paginationPageParameter;
    }

    public function getQuerySortDirectionParameter()
    {
        return $this->sortDirectionParameter;
    }

    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
    }

    /**
     * Returns the total results in this item list.
     *
     * @return int
     */
    abstract public function getTotalResults();

    public function getPagination()
    {
        $pagination = $this->createPaginationObject();
        if ($this->itemsPerPage > -1) {
            $pagination->setMaxPerPage($this->itemsPerPage);
        }
        $query = \Request::getInstance()->query;
        if ($query->has($this->getQueryPaginationPageParameter())) {
            $page = intval($query->get($this->getQueryPaginationPageParameter()));
            try {
                $pagination->setCurrentPage($page);
            } catch (LessThan1CurrentPageException $e) {
                $pagination->setCurrentPage(1);
            } catch (OutOfRangeCurrentPageException $e) {
                $pagination->setCurrentPage(1);
            }
        }

        return $pagination;
    }

    /**
     * @param StickyRequest $request
     */
    public function setupAutomaticSorting(StickyRequest $request = null)
    {
        if ($this->enableAutomaticSorting) {
            if ($request) {
                $data = $request->getSearchRequest();
            } else {
                $data = \Request::getInstance()->query->all();
            }
            $direction = 'asc';
            if (isset($data[$this->getQuerySortDirectionParameter()])) {
                $direction = $data[$this->getQuerySortDirectionParameter()];
            }
            if (isset($data[$this->getQuerySortColumnParameter()])) {
                $value = $data[$this->getQuerySortColumnParameter()];
                if (in_array($value, $this->autoSortColumns)) {
                    $this->sanitizedSortBy($value, $direction);
                }
            }
        }
    }

    /**
     * @deprecated
     */
    public function get()
    {
        return $this->getResults();
    }

    /**
     * Split a string into words and format them to be used in LIKE queries.
     *
     * @param string|mixed $keywords The string to be splitted
     * @param string $wordSeparators The regular expression pattern that represents potential word delimiters (eg '\s' for any whitespace character)
     *
     * @return string[]|null returns null if no word has been found, or an array of words enclosed by '%'
     *
     * @example for instance, with 'Hello world!', you'll get ['%Hello$', '%world!$']
     */
    protected function splitKeywords($keywords, $wordSeparators = '\s')
    {
        $result = null;
        if (is_string($keywords)) {
            $keywords = trim(preg_replace('/[' . $wordSeparators . ']+/', ' ', $keywords));
            if ($keywords !== '') {
                $result = [];
                foreach (explode(' ', $keywords) as $keyword) {
                    $result[] = '%' . addcslashes($keyword, '%_') . '%';
                }
            }
        }

        return $result;
    }
}
