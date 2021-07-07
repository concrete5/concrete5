<?php
namespace Concrete\Core\Search\Pagination\View;

use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{
    protected function createApplicationDriver()
    {
        return new ConcreteBootstrap5View();
    }

    protected function createDashboardDriver()
    {
        return new ConcreteCMSView();
    }

    protected function createCmsDriver()
    {
        return new ConcreteCMSView();
    }

}
