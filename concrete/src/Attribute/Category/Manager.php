<?php
namespace Concrete\Core\Attribute\Category;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die('Access Denied.');

class Manager extends CoreManager
{
    public function createPageDriver()
    {
        return $this->createCollectionDriver();
    }

    public function createCollectionDriver()
    {
        return $this->app->make('Concrete\Core\Attribute\Category\PageCategory');
    }

    public function createFileDriver()
    {
        return $this->app->make('Concrete\Core\Attribute\Category\FileCategory');
    }

    public function createUserDriver()
    {
        return $this->app->make('Concrete\Core\Attribute\Category\UserCategory');
    }

    public function createSiteDriver()
    {
        return $this->app->make('Concrete\Core\Attribute\Category\SiteCategory');
    }

    public function createEventDriver()
    {
        return $this->app->make('Concrete\Core\Attribute\Category\EventCategory');
    }

    public function createLegacyDriver()
    {
        return $this->app->make('Concrete\Core\Attribute\Category\LegacyCategory');
    }

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }
}
