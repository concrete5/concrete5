<?php

namespace Concrete\Core\Routing;

use Concrete\Core\Page\Theme\ThemeRouteCollection;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Router implements RouterInterface
{
    /**
     * @var RouteActionFactoryInterface
     */
    protected $actionFactory;

    /**
     * @var RouteCollection
     */
    protected $routes;

    public function __construct(
        RouteCollection $routes,
        RouteActionFactoryInterface $actionFactory
    ) {
        $this->routes = $routes;
        $this->actionFactory = $actionFactory;
    }

    public function buildGroup()
    {
        return new RouteGroupBuilder($this);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function get($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['GET'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function head($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['HEAD'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function post($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['POST'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function put($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['PUT'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function patch($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['PATCH'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function delete($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['DELETE'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function options($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, ['OPTIONS'], $name);
    }

    /**
     * @param string $path
     * @param string $action
     * @param string $name
     *
     * @since 8.5.0a2
     *
     * @return \Concrete\Core\Routing\RouteBuilder
     */
    public function all($path, $action, $name = '')
    {
        return $this->createRouteBuilder($path, $action, [
            'GET', 'POST', 'PUT', 'PATCH', 'HEAD', 'DELETE', 'OPTIONS',
        ], $name);
    }

    /**
     * @param Route $route
     *
     * @return RouteActionInterface
     */
    public function resolveAction(Route $route)
    {
        return $this->actionFactory->createAction($route);
    }

    /**
     * @return RouteCollection
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @return RouteActionFactoryInterface
     */
    public function getActionFactory()
    {
        return $this->actionFactory;
    }

    public function addRoute(Route $route)
    {
        $this->routes->add($route->getName(), $route);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Routing\RouterInterface::getRouteByPath()
     */
    public function getRouteByPath($path, RequestContext $context, array &$routeAttributes = [])
    {
        $path = $this->normalizePath($path);
        $potentialRoutes = $this->filterRouteCollectionForPath($this->getRoutes(), $path);
        $matcher = new UrlMatcher($potentialRoutes, $context);
        $routeAttributes = $matcher->match($path);

        return $potentialRoutes->get($routeAttributes['_route']);
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Routing\RouterInterface::matchRoute()
     */
    public function matchRoute(Request $request)
    {
        $attributes = [];
        $route = $this->getRouteByPath($request->getPathInfo(), (new RequestContext())->fromRequest($request), $attributes);
        $request->attributes->add($attributes);
        $request->attributes->set('_route', $route);

        return new MatchedRoute($route, $attributes);
    }

    public function loadRouteList(RouteListInterface $list)
    {
        $list->loadRoutes($this);
    }

    /**
     * @deprecated. Use the verb methods instead.
     *
     * @param $path
     * @param $callback
     * @param null $handle
     * @param array $requirements
     * @param array $options
     * @param string $host
     * @param array $schemes
     * @param array $methods
     * @param null $condition
     *
     * @return Route
     */
    public function register(
        $path,
        $callback,
        $handle = null,
        array $requirements = [],
        array $options = [],
        $host = '',
        $schemes = [],
        $methods = [],
        $condition = null)
    {
        $route = new Route($this->normalizePath($path), [], $requirements, $options, $host, $schemes, $methods, $condition);
        $route->setAction($callback);
        if ($handle) {
            $route->setCustomName($handle);
        }
        $this->addRoute($route);

        return $route;
    }

    /**
     * Registers routes from a config array. This is deprecated. Use the $router object
     * directly in an included file.
     *
     * @deprecated.
     *
     * @param array $routes
     */
    public function registerMultiple(array $routes)
    {
        foreach ($routes as $route => $route_settings) {
            array_unshift($route_settings, $route);
            call_user_func_array([$this, 'register'], $route_settings);
        }
    }

    /**
     * Returns a route string based on data. DO NOT USE THIS.
     *
     * @deprecated
     *
     * @param $data
     *
     * @return string
     */
    public function route($data)
    {
        if (is_array($data)) {
            $path = $data[0];
            $pkg = $data[1];
        } else {
            $path = $data;
        }

        $path = trim($path, '/');
        $pkgHandle = null;
        if ($pkg) {
            if (is_object($pkg)) {
                $pkgHandle = $pkg->getPackageHandle();
            } else {
                $pkgHandle = $pkg;
            }
        }

        $route = '/ccm';
        if ($pkgHandle) {
            $route .= "/{$pkgHandle}";
        } else {
            $route .= '/system';
        }

        $route .= "/{$path}";

        return $route;
    }

    /**
     * @deprecated Use $app->make(\Concrete\Core\Page\Theme\ThemeRouteCollection::class)->setThemeByRoute() with the same arguments
     *
     * {@inheritdoc}
     * @see \Concrete\Core\Page\Theme\ThemeRouteCollection::setThemeByRoute()
     */
    public function setThemeByRoute($path, $theme = null, $wrapper = FILENAME_THEMES_VIEW)
    {
        $app = Application::getFacadeApplication();
        $app->make(ThemeRouteCollection::class)->setThemeByRoute($path, $theme, $wrapper);
    }

    private function normalizePath($path)
    {
        $path = trim($path, '/');
        return $path === '' ? '/' : "/{$path}/";
    }

    private function createRouteBuilder($path, $action, $methods, $name = '')
    {
        $route = new Route($this->normalizePath($path));
        $route->setMethods($methods);
        $route->setAction($action);
        if ($name !== '') {
            $route->setCustomName($name);
        }

        return new RouteBuilder($this, $route);
    }

    /**
     * @param \Symfony\Component\Routing\RouteCollection $routes
     * @param string $path
     *
     * @return \Symfony\Component\Routing\RouteCollection
     */
    private function filterRouteCollectionForPath(RouteCollection $routes, $path)
    {
        $result = new RouteCollection();
        foreach ($routes->getResources() as $resource) {
            $result->addResource($resource);
        }
        foreach ($routes->all() as $name => $route) {
            $routePath = $route->getPath();
            $p = strpos($routePath, '{');
            $skip = false;
            if ($p === false) {
                if ($routePath !== $path) {
                    $skip = true;
                }
            } elseif ($p > 0) {
                $routeFixedPath = substr($routePath, 0, $p);
                if (strpos($path, $routeFixedPath) !== 0) {
                    $skip = true;
                }
            }
            if ($skip === false) {
                $result->add($name, $route);
            }
        }

        return $result;
    }

}
