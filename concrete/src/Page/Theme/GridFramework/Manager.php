<?php
namespace Concrete\Core\Page\Theme\GridFramework;

use Concrete\Core\Page\Theme\GridFramework\Type\Bootstrap2;
use Concrete\Core\Page\Theme\GridFramework\Type\Bootstrap3;
use Concrete\Core\Page\Theme\GridFramework\Type\Bootstrap4;
use Concrete\Core\Page\Theme\GridFramework\Type\Bootstrap5;
use Concrete\Core\Page\Theme\GridFramework\Type\NineSixty;
use Concrete\Core\Page\Theme\GridFramework\Type\Foundation;
use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{
    protected function createNineSixtyDriver()
    {
        return new NineSixty();
    }

    protected function createBootstrap2Driver()
    {
        return new Bootstrap2();
    }

    protected function createBootstrap3Driver()
    {
        return new Bootstrap3();
    }

    protected function createBootstrap4Driver()
    {
        return new Bootstrap4();
    }

    protected function createBootstrap5Driver()
    {
        return new Bootstrap5();
    }

    protected function createFoundationDriver()
    {
        return new Foundation();
    }
}
