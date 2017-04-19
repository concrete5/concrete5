<?php
/**
 * Created by PhpStorm.
 * User: andrewembler
 * Date: 1/27/15
 * Time: 6:24 AM.
 */
class URLTest extends PHPUnit_Framework_TestCase
{
    /**
     * Here's the expected behavior.
     * All URLs generated should have index.php in front of them if
     * concrete.seo.url_rewriting is false.
     * If concrete.seo.url_rewriting is true, then all URLs should have
     * no index.php, unless they're in the Dashboard.
     * If concrete.seo.url_rewriting_all is true, then all URLs (including Dashboard)
     * should be free of index.php.
     *
     * This should be the case whether something is being called via URL::to, URL::page,
     * or Page::getCollectionLink or \Concrete\Core\Html\Service\Navigation::getLinkToCollection
     */
    public function setUp()
    {
        $locale = new \Concrete\Core\Entity\Site\Locale();
        $locale->setCountry('US');
        $locale->setLanguage('en');
        $siteTree = new \Concrete\Core\Entity\Site\SiteTree();
        $siteTree->setLocale($locale);
        $service = Core::make('helper/navigation');
        $page = new Page();
        $page->siteTree = $siteTree;
        $page->cPath = '/path/to/my/page';
        $page->error = false;
        $dashboard = new Page();
        $dashboard->cPath = '/dashboard/my/awesome/page';
        $dashboard->error = false;
        $this->page = $page;
        $this->dashboard = $dashboard;
        $page->siteTree = $siteTree;
        $dashboard->siteTree = $siteTree;
        $this->service = $service;
        Config::set('concrete.seo.url_rewriting', false);
        Config::set('concrete.seo.url_rewriting_all', false);
        $this->oldUrl = Config::get('concrete.seo.canonical_url');
        Config::set('concrete.seo.canonical_url', 'http://dummyurl.com');
        Core::clearCaches();

        parent::setUp();
    }

    public function tearDown()
    {
        Config::set('concrete.seo.canonical_url', $this->oldUrl);
        $this->clearCanonicalUrl();

        parent::tearDown();
    }

    public function testPathToSiteInApplication()
    {
        $this->assertEquals('/path/to/server', \Core::getApplicationRelativePath());
    }

    public function testNoUrlRewriting()
    {
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', (string) URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/path/to/my/page', (string) URL::page($this->page));
    }

    public function testNoUrlRewritingNoRelativePath()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '';
        $app->instance('app', $app);

        $app->make('Concrete\Core\Url\Resolver\CanonicalUrlResolver')->clearCached();

        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', (string) URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/index.php/path/to/my/page', (string) URL::page($this->page));
    }

    public function testUrlRewriting()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '/path/to/server';
        $app->instance('app', $app);

        Config::set('concrete.seo.url_rewriting', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) URL::page($this->page));
    }

    public function testCanonicalURLRedirection()
    {
        $app = Core::make("app");
        Config::set('concrete.seo.redirect_to_canonical_url', true);
        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/path/to/site/index.php/dashboard?bar=1&foo=1');

        $site = $this->getMockBuilder(Concrete\Core\Entity\Site\Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison = $this->getMockBuilder(\Concrete\Core\Config\Repository\Liaison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('seo.canonical_url', null, 'https://www2.myawesomesite.com:8080'),
                array('seo.trailing_slash', null, false),
                array('seo.canonical_ssl_url', null, 'https://www2.myawesomesite.com:8080'),
            )));

        $site->expects($this->once())
            ->method('getConfigRepository')
            ->will($this->returnValue($liaison));

        $response = $app->handleCanonicalURLRedirection($request, $site);

        $this->assertEquals('https://www2.myawesomesite.com:8080/path/to/site/index.php/dashboard?bar=1&foo=1', $response->getTargetUrl());
    }

    public function testCanonicalURLRedirectionSameDomain()
    {
        $app = Core::make("app");

        $site = $this->getMockBuilder(Concrete\Core\Entity\Site\Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison = $this->getMockBuilder(\Concrete\Core\Config\Repository\Liaison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('seo.canonical_url', null, 'http://concrete5.dev')
            )));

        $site->expects($this->any())
            ->method('getConfigRepository')
            ->will($this->returnValue($liaison));

        Config::set('concrete.seo.redirect_to_canonical_url', true);
        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/login');
        $response = $app->handleCanonicalURLRedirection($request, $site);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/index.php?cID=1');
        $response = $app->handleCanonicalURLRedirection($request, $site);
        $this->assertNull($response);
    }

    public function testCanonicalUrlRedirectionSslUrl()
    {
        $app = Core::make("app");

        $site = $this->getMockBuilder(Concrete\Core\Entity\Site\Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison = $this->getMockBuilder(\Concrete\Core\Config\Repository\Liaison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('seo.canonical_url', null, 'http://mysite.com'),
                array('seo.canonical_ssl_url', null, 'https://secure.mysite.com:8080')
            )));

        $site->expects($this->once())
            ->method('getConfigRepository')
            ->will($this->returnValue($liaison));

        Config::set('concrete.seo.redirect_to_canonical_url', true);

        $request = \Concrete\Core\Http\Request::create('https://secure.mysite.com:8080/path/to/page');
        $response = $app->handleCanonicalURLRedirection($request, $site);
        $this->assertNull($response);
        Config::set('concrete.seo.redirect_to_canonical_url', false);

    }

    public function testPathSlashesRedirection()
    {
        $app = Core::make("app");

        $site = $this->getMockBuilder(Concrete\Core\Entity\Site\Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison = $this->getMockBuilder(\Concrete\Core\Config\Repository\Liaison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('seo.trailing_slash', null, false),
            )));

        $site->expects($this->any())
            ->method('getConfigRepository')
            ->will($this->returnValue($liaison));

        $request = \Concrete\Core\Http\Request::create('http://xn--mgbh0fb.xn--kgbechtv/services');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://xn--fsqu00a.xn--0zwm56d/services/');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertEquals('http://例子.测试/services', $response->getTargetUrl());

        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/derp');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://concrete5.dev/index.php?cID=1');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/about-us/now');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/about-us/now/');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertInstanceOf('\Concrete\Core\Routing\RedirectResponse', $response);
        $this->assertEquals('http://www.awesome.com/about-us/now', $response->getTargetUrl());

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com/index.php/about-us/now/?bar=1&foo=2');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertInstanceOf('\Concrete\Core\Routing\RedirectResponse', $response);
        $this->assertEquals('http://www.awesome.com/index.php/about-us/now?bar=1&foo=2', $response->getTargetUrl());

        $site = $this->getMockBuilder(Concrete\Core\Entity\Site\Site::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison = $this->getMockBuilder(\Concrete\Core\Config\Repository\Liaison::class)
            ->disableOriginalConstructor()
            ->getMock();

        $liaison->expects($this->any())
            ->method('get')
            ->will($this->returnValueMap(array(
                array('seo.trailing_slash', null, true),
            )));

        $site->expects($this->any())
            ->method('getConfigRepository')
            ->will($this->returnValue($liaison));

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com:8080/index.php/about-us/now/?bar=1&foo=2');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertNull($response);

        $request = \Concrete\Core\Http\Request::create('http://www.awesome.com:8080/index.php/about-us/now?bar=1&foo=2');
        $response = $app->handleURLSlashes($request, $site);
        $this->assertEquals('http://www.awesome.com:8080/index.php/about-us/now/?bar=1&foo=2', $response->getTargetUrl());

    }

    public function testUrlRewritingAll()
    {
        Config::set('concrete.seo.url_rewriting', true);
        Config::set('concrete.seo.url_rewriting_all', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', (string) $this->page->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page',
                            (string) $this->service->getLinkToCollection($this->page)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::to('/path/to/my/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/path/to/my/page', URL::page($this->page));
    }

    public function testNoUrlRewritingDashboard()
    {
        $app = Core::make("app");
        $app['app_relative_path'] = '/path/to/server';
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',
                            (string) $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testPagesWithNoPaths()
    {
        $locale = new \Concrete\Core\Entity\Site\Locale();
        $locale->setCountry('US');
        $locale->setLanguage('en');
        $siteTree = new \Concrete\Core\Entity\Site\SiteTree();
        $siteTree->setLocale($locale);

        $home = new Page();
        $home->cID = 1;
        $home->cPath = '';
        $home->siteTree = $siteTree;

        $url = \URL::to($home);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php?cID='.$home->cID, (string) $url);

        $url = \URL::to('/');
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php', (string) $url);

        $page = new Page();
        $page->cPath = null;
        $page->cID = 777;
        $page->siteTree = $siteTree;

        $url = \URL::to($page);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php?cID=777', (string) $url);
    }

    public function testUrlRewritingDashboard()
    {
        Config::set('concrete.seo.url_rewriting', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page',
                            (string) $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testUrlRewritingAllDashboard()
    {
        Config::set('concrete.seo.url_rewriting', true);
        Config::set('concrete.seo.url_rewriting_all', true);
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', (string) $this->dashboard->getCollectionLink());
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page',
                            (string) $this->service->getLinkToCollection($this->dashboard)
        );
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.dummyco.com/path/to/server/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testCanonicalUrl()
    {
        $this->markTestIncomplete('This needs to be updated to use the new site-based canonical url');

        Config::set('concrete.seo.canonical_url', 'http://www.derpco.com');
        $this->clearCanonicalUrl();

        $this->assertEquals('http://www.derpco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.derpco.com/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testCanonicalUrlWithPort()
    {
        $this->markTestIncomplete('This needs to be updated to use the new site-based canonical url');

        Config::set('concrete.seo.canonical_url', 'http://www.derpco.com:8080');
        $this->clearCanonicalUrl();
        $this->assertEquals('http://www.derpco.com:8080/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::to('/dashboard/my/awesome/page'));
        $this->assertEquals('http://www.derpco.com:8080/path/to/server/index.php/dashboard/my/awesome/page', (string) URL::page($this->dashboard));
    }

    public function testURLFunctionWithCanonicalURL()
    {
        $this->markTestIncomplete('This needs to be updated to use the new site-based canonical url');

        Config::set('concrete.seo.canonical_url', 'http://concrete5');

        $this->clearCanonicalUrl();

        $url = URL::to('/dashboard/system/test', 'outstanding');
        $this->assertEquals('http://concrete5/path/to/server/index.php/dashboard/system/test/outstanding', (string) $url);
    }

    public function testURLFunctionWithoutCanonicalURL()
    {
        $this->markTestIncomplete('This needs to be updated to use the new site-based canonical url');

        Config::set('concrete.seo.canonical_url', '');

        $this->clearCanonicalUrl();

        $url = URL::to('/dashboard/system/test', 'outstanding');
        $this->assertEquals('/path/to/server/index.php/dashboard/system/test/outstanding', (string) $url);
    }

    private function clearCanonicalUrl()
    {
        $app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
        $app->make('Concrete\Core\Url\Resolver\CanonicalUrlResolver')->clearCached();
    }

}
