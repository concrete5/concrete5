<?php
namespace Concrete\Core\Http;

use Concrete\Controller\Frontend\PageForbidden;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Http\Service\Ajax;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Page\Collection\Collection;
use Concrete\Core\Page\Controller\PageController;
use Concrete\Core\Page\Event;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\Relation\Menu\Item\RelationListItem;
use Concrete\Core\Page\Theme\Theme;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Key\Key;
use Concrete\Core\Routing\RedirectResponse;
use Concrete\Core\Session\SessionValidator;
use Concrete\Core\Site\Menu\Item\SiteListItem;
use Concrete\Core\User\PostLoginLocation;
use Concrete\Core\User\User;
use Concrete\Core\View\View;
use Detection\MobileDetect;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class ResponseFactory implements ResponseFactoryInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    protected $session;

    /**
     * @var \Concrete\Core\Http\Request
     */
    protected $request;
    /**
     * @var \Concrete\Core\Localization\Localization
     */
    private $localization;
    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    public function __construct(Session $session, Request $request, Localization $localization, Repository $config)
    {
        $this->session = $session;
        $this->request = $request;
        $this->localization = $localization;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function create($content, $code = Response::HTTP_OK, array $headers = [])
    {
        return Response::create($content, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function json($data, $code = Response::HTTP_OK, array $headers = [])
    {
        return JsonResponse::create($data, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function notFound($content, $code = Response::HTTP_NOT_FOUND, $headers = [])
    {
        if ($this->app->make(Ajax::class)->isAjaxRequest($this->request)) {
            $this->localization->pushActiveContext(Localization::CONTEXT_SITE);
            $responseData = [
                'error' => t('Page not found'),
                'errors' => [t('Page not found')],
            ];
            $this->localization->popActiveContext();

            return $this->json($responseData, $code, $headers);
        }

        $item = '/page_not_found';
        $c = Page::getByPath($item);

        if (is_object($c) && !$c->isError()) {
            // Display not found
            $this->request->setCurrentPage($c);

            return $this->controller($c->getPageController(), $code, $headers);
        }

        $cnt = $this->app->make(PageForbidden::class);

        return $this->controller($cnt, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function error($content, $code = Response::HTTP_INTERNAL_SERVER_ERROR, $headers = [])
    {
        return $this->create($content, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function forbidden($requestUrl, $code = Response::HTTP_FORBIDDEN, $headers = [])
    {
        $this->app->make(PostLoginLocation::class)->setSessionPostLoginUrl($requestUrl);

        // load page forbidden
        $item = '/page_forbidden';
        $c = Page::getByPath($item);
        if (is_object($c) && !$c->isError()) {
            $this->request->setCurrentPage($c);

            return $this->controller($c->getPageController(), $code, $headers);
        }

        $cnt = $this->app->make(PageForbidden::class);
        $this->controller($cnt, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function redirect($to, $code = Response::HTTP_MOVED_PERMANENTLY, $headers = [])
    {
        return RedirectResponse::create($to, $code, $headers);
    }

    /**
     * {@inheritdoc}
     */
    public function view(View $view, $code = Response::HTTP_OK, $headers = [])
    {
        $this->localization->pushActiveContext(Localization::CONTEXT_SITE);
        try {
            $contents = $view->render();

            return $this->create($contents, $code, $headers);
        } finally {
            $this->localization->popActiveContext();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function controller(Controller $controller, $code = Response::HTTP_OK, $headers = [])
    {
        $this->localization->pushActiveContext(Localization::CONTEXT_SITE);
        try {
            $request = $this->request;

            if ($response = $controller->on_start()) {
                return $response;
            }

            if ($controller instanceof PageController) {
                if ($controller->isReplaced()) {
                    return $this->controller($controller->getReplacement(), $code, $headers);
                }
                $controller->setupRequestActionAndParameters($request);

                $response = $controller->validateRequest();
                // If validaterequest returned a response
                if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                    return $response;
                } else {
                    // If validateRequest did not return true
                    if ($response == false) {
                        return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
                    }
                }

                $requestTask = $controller->getRequestAction();
                $requestParameters = $controller->getRequestActionParameters();
                $response = $controller->runAction($requestTask, $requestParameters);
                if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
                    return $response;
                }
                if ($controller->isReplaced()) {
                    return $this->controller($controller->getReplacement(), $code, $headers);
                }
            } else {
                if ($response = $controller->runAction('view')) {
                    return $response;
                }
            }

            $view = $controller->getViewObject();

            // Mobile theme
            if ($this->config->get('concrete.misc.mobile_theme_id') > 0) {
                $md = $this->app->make(MobileDetect::class);
                if ($md->isMobile()) {
                    $mobileTheme = Theme::getByID($this->app->config->get('concrete.misc.mobile_theme_id'));
                    if ($mobileTheme instanceof Theme) {
                        $view->setViewTheme($mobileTheme);
                        $controller->setTheme($mobileTheme);
                    }
                }
            }

            return $this->view($view, $code, $headers);
        } finally {
            $this->localization->popActiveContext();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function collection(Collection $collection, $code = Response::HTTP_OK, $headers = [])
    {
        if (!$this->app) {
            throw new \RuntimeException('Cannot resolve collections without a reference to the application');
        }

        $dl = $this->app->make('multilingual/detector');
        $dl->setupSiteInterfaceLocalization($collection);

        $request = $this->request;

        if ($collection->isError() && $collection->getError() == COLLECTION_NOT_FOUND) {
            if ($response = $this->collectionNotFound($collection, $request, $headers)) {
                return $response;
            }
        }

        if ($collection->getCollectionPath() != '/page_not_found') {
            if (!isset($collection->cPathFetchIsCanonical) || !$collection->cPathFetchIsCanonical) {
                // Handle redirect URL (additional page paths)
                /** @var Url $url */
                $url = $this->app->make('url/manager')->resolve([$collection]);
                $query = $url->getQuery();
                $query->modify($request->getQueryString());

                $url = $url->setQuery($query);

                return $this->redirect($url, Response::HTTP_MOVED_PERMANENTLY, $headers);
            }
        }

        // maintenance mode
        if ($collection->getCollectionPath() != '/login') {
            $smm = $this->config->get('concrete.maintenance_mode');
            if ($smm == 1 && !Key::getByHandle('view_in_maintenance_mode')->validate() && ($_SERVER['REQUEST_METHOD'] != 'POST' || $this->app->make('token')->validate() == false)) {
                $v = new View('/frontend/maintenance_mode');
                $v->addScopeItems(['c' => $collection]);
                $request->setCurrentPage($collection);

                return $this->view($v, $code, $headers);
            }
        }

        if ($collection->getCollectionPointerExternalLink() != '') {
            return $this->redirect($collection->getCollectionPointerExternalLink());
        }

        $cp = new Checker($collection);

        if ($cp->isError() && $cp->getError() == COLLECTION_FORBIDDEN) {
            return $this->forbidden($request->getUri(), Response::HTTP_FORBIDDEN, $headers);
        }

        if (!$collection->isActive() && (!$cp->canViewPageVersions())) {
            return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
        }

        if ($cp->canEditPageContents() || $cp->canEditPageProperties() || $cp->canViewPageVersions()) {
            $collection->loadVersionObject('RECENT');
        } else {
            $collection->loadVersionObject('ACTIVE');
        }

        $vp = new Checker($collection->getVersionObject());

        // returns the $vp object, which we then check
        if (is_object($vp) && $vp->isError()) {
            switch ($vp->getError()) {
                case COLLECTION_NOT_FOUND:
                    return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
                    break;
                case COLLECTION_FORBIDDEN:
                    return $this->forbidden($request->getUri(), Response::HTTP_FORBIDDEN, $headers);
                    break;
            }
        }

        // Now that we've passed all permissions checks, and we have a page, we check to see if we
        // ought to redirect based on base url or trailing slash settings
        $cms = $this->app;
        $site = $this->app['site']->getSite();

        $response = $cms->handleCanonicalURLRedirection($request, $site);
        // Don't handle final URL slashes if it's a page not found to avoid infinite redirections
        if (!$response && $collection->getCollectionPath() !== '/page_not_found') {
            $response = $cms->handleURLSlashes($request, $site);
        }
        if (isset($response)) {
            return $response;
        }

        if (!$request->getPath()
            && $request->isMethod('GET')
            && !$request->query->has('cID')
        ) {
            // This is a request to the home page –http://www.mysite.com/

            // First, we check to see if we need to redirect to a default multilingual section.
            if ($dl->isEnabled() && $site->getConfigRepository()->get('multilingual.redirect_home_to_default_locale')) {
                // Redirect only if it's the first request, otherwise we can't browse to the root locale
                $sessionValidator = $cms->make(SessionValidator::class);
                if (!($sessionValidator->hasActiveSession() && $cms->make('session')->has('multilingual_default_locale'))) {
                    // Let's retrieve the default language
                    $ms = $dl->getPreferredSection();
                    if (is_object($ms) && !$ms->isDefaultMultilingualSection($site)) {
                        return $this->redirect(\URL::to($ms), Response::HTTP_FOUND);
                    }
                }
            }

            // Otherwise, let's check to see if our home page, which we have loaded already, has a path (like /en)
            // If it does, we'll redirect to the path.
            if ($collection->getCollectionPath() != '') {
                return $this->redirect(\URL::to($collection));
            }
        }

        $request->setCurrentPage($collection);
        $c = $collection; // process.php needs this
        require DIR_BASE_CORE . '/bootstrap/process.php';
        $u = $this->app->make(User::class);

        // On page view event.
        $pe = new Event($collection);
        $pe->setUser($u);
        $pe->setRequest($request);
        $this->app['director']->dispatch('on_page_view', $pe);

        // Core menu items
        $item = new RelationListItem();
        $menu = $this->app->make('helper/concrete/ui/menu');
        $menu->addMenuItem($item);

        // Multisite item
        $item = new SiteListItem();
        $menu->addMenuItem($item);

        $controller = $collection->getPageController();

        // we update the current page with the one bound to this controller.
        $collection->setController($controller);

        return $this->controller($controller);
    }

    private function collectionNotFound(Collection $collection, Request $request, array $headers)
    {
        // if we don't have a path and we're doing cID, then this automatically fires a 404.
        if (!$request->getPath() && $request->get('cID')) {
            return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
        }

        // let's test to see if this is, in fact, the home page,
        // and we're routing arguments onto it (which is screwing up the path.)
        $home = Page::getByID(Page::getHomePageID());
        $request->setCurrentPage($home);
        $homeController = $home->getPageController();
        $homeController->setupRequestActionAndParameters($request);

        $response = $homeController->validateRequest();
        if ($response instanceof \Symfony\Component\HttpFoundation\Response) {
            return $response;
        } elseif ($response === true) {
            return $this->controller($homeController);
        } else {
            return $this->notFound('', Response::HTTP_NOT_FOUND, $headers);
        }
    }
}
