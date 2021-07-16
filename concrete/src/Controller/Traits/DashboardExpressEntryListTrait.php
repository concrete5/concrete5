<?php

namespace Concrete\Core\Controller\Traits;

use Concrete\Core\Attribute\Category\ExpressCategory;
use Concrete\Core\Csv\WriterFactory;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Entity\Search\Query;
use Concrete\Core\Entity\Search\SavedExpressSearch;
use Concrete\Core\Error\UserMessageException;
use Concrete\Core\Express\Export\EntryList\CsvWriter;
use Concrete\Core\Express\Search\Field\SiteField;
use Concrete\Core\Express\Search\SearchProvider;
use Concrete\Core\Filesystem\Element;
use Concrete\Core\Filesystem\ElementManager;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\Navigation\Breadcrumb\Dashboard\DashboardExpressBreadcrumbFactory;
use Concrete\Core\Page\Page;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Search\Field\Field\KeywordsField;
use Concrete\Core\Search\Query\Modifier\AutoSortColumnRequestModifier;
use Concrete\Core\Search\Query\Modifier\ItemsPerPageRequestModifier;
use Concrete\Core\Search\Query\QueryFactory;
use Concrete\Core\Search\Query\QueryModifier;
use Concrete\Core\Search\Result\Result;
use Concrete\Core\Search\Result\ResultFactory;
use Concrete\Core\Site\InstallationService;
use Concrete\Core\Url\Url;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Adds listing of dashboard express entries to a page.
 */
trait DashboardExpressEntryListTrait
{

    /**
     * @return Page
     */
    abstract public function getPageObject();

    abstract protected function getHeaderSearchAction(Entity $entity);

    protected function getAdvancedSearchDialogPath()
    {
        return '/ccm/system/dialogs/express/advanced_search/';
    }

    /**
     * @var Element
     */
    protected $headerSearch;

    /**
     * @var Element
     */
    protected $headerMenu;

    protected function getHeaderMenu()
    {
        if (!isset($this->headerMenu)) {
            $this->headerMenu = $this->app->make(ElementManager::class)->get('express/search/menu');
        }
        return $this->headerMenu;
    }

    protected function getHeaderSearch()
    {
        if (!isset($this->headerSearch)) {
            $this->headerSearch = $this->app->make(ElementManager::class)->get('express/search/search');
        }
        return $this->headerSearch;
    }

    public function createBreadcrumbFactory()
    {
        return $this->app->make(DashboardExpressBreadcrumbFactory::class);
    }


    /**
     * @return QueryFactory
     */
    protected function getQueryFactory()
    {
        return $this->app->make(QueryFactory::class);
    }

    /**
     * @return SearchProvider
     */
    protected function getSearchProvider(Entity $entity)
    {
        $category = $this->app->make(ExpressCategory::class, ['entity' => $entity]);
        return $this->app->make(SearchProvider::class, [
            'entity' => $entity, 'category' => $category
        ]);
    }

    protected function createDefaultQuery(Entity $entity)
    {
        $installationService = app(InstallationService::class);
        $fields = [];
        if ($installationService->isMultisiteEnabled()) {
            $fields[] = new SiteField();
        }
        $keywordsField = $this->getSearchKeywordsField();
        if ($keywordsField) {
            $fields[] = $keywordsField;
        }
        $query = $this->getQueryFactory()->createQuery($this->getSearchProvider($entity), $fields);
        return $query;
    }

    /**
     * @param Query $query
     * @return Result
     */
    protected function createSearchResult(Entity $entity, Query $query)
    {
        $provider = $this->getSearchProvider($entity);
        $resultFactory = $this->app->make(ResultFactory::class);
        $queryModifier = $this->app->make(QueryModifier::class);

        $queryModifier->addModifier(new AutoSortColumnRequestModifier($provider, $this->request, Request::METHOD_GET));
        $queryModifier->addModifier(new ItemsPerPageRequestModifier($provider, $this->request, Request::METHOD_GET));
        $query = $queryModifier->process($query);

        return $resultFactory->createFromQuery($provider, $query);
    }

    protected function getSearchKeywordsField()
    {
        $keywords = null;
        if ($this->request->query->has('keywords')) {
            $keywords = $this->request->query->get('keywords');
            return new KeywordsField($keywords);
        }
    }

    /**
     * @param Result $result
     */
    protected function renderSearchResult(Result $result)
    {
        $entity = $result->getEntity();
        $query = $result->getQuery();
        $headerMenu = $this->getHeaderMenu();
        $headerSearch = $this->getHeaderSearch();
        $headerMenu->getElementController()->setQuery($query);
        $headerMenu->getElementController()->setEntity($entity);
        $headerSearch->getElementController()->setQuery($query);
        $headerSearch->getElementController()->setEntity($entity);

        $permissions = new Checker($entity);

        if ($permissions->canAddExpressEntries()) {
            $headerMenu->getElementController()->setCreateURL(
                $this->app->make('url/resolver/path')->resolve([
                                                                   $this->getPageObject()->getCollectionPath(), 'create_entry', $entity->getID()])
            );
        }

        $exportArgs = [$this->getPageObject()->getCollectionPath(), 'csv_export', $entity->getID()];
        if ($this->getAction() == 'advanced_search') {
            $exportArgs[] = 'advanced_search';
        }

        $this->headerSearch->getElementController()->setHeaderSearchAction($this->getHeaderSearchAction($entity));

        $exportURL = $this->app->make('url/resolver/path')->resolve($exportArgs);
        $query = Url::createFromServer($_SERVER)->getQuery();
        $exportURL = $exportURL->setQuery($query);
        $headerMenu->getElementController()->setExportURL($exportURL);

        $this->set('result', $result);
        $this->set('headerMenu', $headerMenu);
        $this->set('headerSearch', $headerSearch);

        $factory = $this->createBreadcrumbFactory();
        $breadcrumb = $factory->getBreadcrumb($this->getPageObject(), $entity->getEntityResultsNodeObject());
        $this->setBreadcrumb($breadcrumb);

        $this->setThemeViewTemplate('full.php');
        $this->render('/dashboard/express/entries/entries', false);
    }

    protected function renderExpressEntryDefaultResults(Entity $entity)
    {
        $this->set('entity', $entity);
        $query = $this->createDefaultQuery($entity);
        $result = $this->createSearchResult($entity, $query);
        $this->set(
            'pageTitle',
            tc(/*i18n: %s is an entity name*/ 'EntriesOfEntityName', '%s Entries', $entity->getEntityDisplayName())
        );
        $this->renderSearchResult($result);
    }

    protected function renderExpressEntryAdvancedSearchResults(Entity $entity)
    {
        $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
            $this->getSearchProvider($entity), $this->request, Request::METHOD_GET
        );
        $result = $this->createSearchResult($entity, $query);
        $this->renderSearchResult($result);
    }

    public function preset($presetID = null)
    {
        if ($presetID) {
            $preset = $this->entityManager->find(SavedExpressSearch::class, $presetID);
            if ($preset) {
                $query = $this->getQueryFactory()->createFromSavedSearch($preset);
                $result = $this->createSearchResult($preset->getEntity(), $query);
                $this->renderSearchResult($result);

                $factory = $this->createBreadcrumbFactory();
                $this->setBreadcrumb($factory->getBreadcrumb($this->getPageObject(), $preset));

                return;
            }
        }
        $this->view();
    }

    /**
     * Export Express entries into a CSV.
     *
     * @param Entity $entity
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function exportCsv(Entity $entity, $searchMethod = null)
    {
        set_time_limit(0);
        $permissions = new \Permissions($entity);
        if (!$permissions->canViewExpressEntries()) {
            throw new \Exception(t('Access Denied'));
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=' . $entity->getHandle() . '.csv',
        ];
        $config = $this->app->make('config');
        $bom = $config->get('concrete.export.csv.include_bom') ? $config->get('concrete.charset_bom') : '';
        $datetime_format = $config->get('concrete.export.csv.datetime_format');

        if ($searchMethod == 'advanced_search') {
            $query = $this->getQueryFactory()->createFromAdvancedSearchRequest(
                $this->getSearchProvider($entity),
                $this->request,
                Request::METHOD_GET
            );
        } else {
            $query = $this->createDefaultQuery($entity);
        }
        $result = $this->createSearchResult($entity, $query);
        $entryList = $result->getItemListObject();

        return StreamedResponse::create(function () use ($entity, $entryList, $bom, $datetime_format) {
            $writer = $this->app->make(CsvWriter::class, [
                $this->app->make(WriterFactory::class)->createFromPath('php://output', 'w'),
                new Date(),
                $datetime_format
            ]);
            echo $bom;
            $writer->insertHeaders($entity);
            $writer->insertEntryList($entryList);
        }, 200, $headers);
    }
}
