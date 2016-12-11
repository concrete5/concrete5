<?php
namespace Concrete\Core\Page\Controller;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\Controller\Controller;
use Concrete\Core\Foundation\Environment;
use Concrete\Core\Html\Service\Html;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Redirect;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Page\View\PageView;
use Symfony\Component\HttpFoundation\Response;

class PageController extends Controller
{
    protected $supportsPageCache = false;
    protected $action;
    protected $passThruBlocks = array();
    protected $parameters = array();
    protected $replacement = null;

    /**
     * array of method names that can't be called through the url
     * @var array
     */
    protected $restrictedMethods = array();

    /**
     * Custom request path - overrides Request::getPath() (useful when replacing controllers).
     * @var string|null
     */
    protected $customRequestPath = null;

    /** @var \Concrete\Core\Page\Page The current page */
    public $c;

    public function supportsPageCache()
    {
        return $this->supportsPageCache;
    }

    public function __construct(Page $c)
    {
        parent::__construct();
        $this->c = $c;
        $this->view = new PageView($this->c);
        $this->set('html', Application::getFacadeApplication()->make(HTML::class));
    }

    /**
     * Given either a path or a Page object, this is a shortcut to
     * 1. Grab the controller of THAT page.
     * 2. Grab the view of THAT controller
     * 3. Render that view.
     * 4. Exit – so we immediately stop all other output in the controller that
     * called render().
     *
     * @param @string|\Concrete\Core\Page\Page $var
     */
    public function replace($var)
    {
        if ($var instanceof Page) {
            $page = $var;
            $path = $var->getCollectionPath();
        } else {
            $path = (string) $var;
            $page = Page::getByPath($path);
        }

        $request = Request::getInstance();
        $controller = $page->getPageController();
        $request->setCurrentPage($page);
        if (is_callable([$controller, 'setCustomRequestPath'])) {
            $controller->setCustomRequestPath($path);
        }
        $this->replacement = $controller;
    }

    /**
     * Set the custom request path (useful when replacing controllers).
     *
     * @param string|null $requestPath Set to null to use the default request path
     */
    public function setCustomRequestPath($requestPath)
    {
        $this->customRequestPath = ($requestPath === null) ? null : (string) $requestPath;
    }

    /**
     * Get the custom request path (useful when replacing controllers).
     *
     * @return string|null Returns null if no custom request path, a string otherwise
     */
    public function getCustomRequestPath()
    {
        return $this->customRequestPath;
    }

    public function isReplaced()
    {
        return !!$this->replacement;
    }

    public function getReplacement()
    {
        return $this->replacement;
    }

    public function getSets()
    {
        $sets = parent::getSets();
        $session = Application::getFacadeApplication()->make('session');
        if ($session->getFlashBag()->has('page_message')) {
            $value = $session->getFlashBag()->get('page_message');
            foreach ($value as $message) {
                $sets[$message[0]] = $message[1];
                $sets[$message[0].'IsHTML'] = isset($message[2]) && $message[2];
            }
        }

        return $sets;
    }

    /**
     * Given a path to a single page, this command uses the CURRENT controller and renders
     * the contents of the single page within this request. The current controller is not
     * replaced, and has already fired (since it is meant to be called from within a view() or
     * similar method).
     *
     * @param @string
     */
    public function render($path, $pkgHandle = null)
    {
        $view = $this->getViewObject();

        $env = Environment::get();
        $path = trim($path, '/');
        $a = $path . '/' . FILENAME_COLLECTION_VIEW;
        $b = $path . '.php';

        $r = $env->getRecord(DIRNAME_PAGES . '/' . $a);

        if ($r->exists()) {
            $view->renderSinglePageByFilename($a, $pkgHandle);
        } else {
            $view->renderSinglePageByFilename($b, $pkgHandle);
        }
    }

    public function getPageObject()
    {
        return $this->c;
    }

    public function flash($key, $value, $isHTML = false)
    {
        $session = Application::getFacadeApplication()->make('session');
        $session->getFlashBag()->add('page_message', array($key, $value, $isHTML));
    }

    public function getTheme()
    {
        if ($this->theme === null) {
            $theme = parent::getTheme();
            if (!$theme) {
                $theme = $this->c->getCollectionThemeObject();
                if (is_object($theme)) {
                    $this->theme = $theme->getThemeHandle();
                }
            } else {
                $this->theme = $theme;
            }
        }

        return $this->theme;
    }

    public function getRequestAction()
    {
        return $this->action;
    }

    public function getRequestActionParameters()
    {
        return $this->parameters;
    }

    public function getControllerActionPath()
    {
        if (isset($this->controllerActionPath)) {
            return $this->controllerActionPath;
        }

        if (is_object($this->view)) {
            return $this->view->getViewPath();
        }
    }

    public function setupRequestActionAndParameters(Request $request)
    {
        $requestPath = $this->getCustomRequestPath();
        if ($requestPath === null) {
            $requestPath = $request->getPath();
        }
        $task = substr($requestPath, strlen($this->c->getCollectionPath()) + 1);
        $task = str_replace('-/', '', $task);
        $taskparts = explode('/', $task);
        if (isset($taskparts[0]) && $taskparts[0] !== '') {
            $method = $taskparts[0];
        } elseif (is_object($this->c) && is_callable(array($this, $this->c->getCollectionHandle()))) {
            $method = $this->c->getCollectionHandle();
        } else {
            $method = 'view';
        }

        $foundTask = false;
        $restrictedControllers = array(
            'Concrete\Core\Controller\Controller',
            'Concrete\Core\Controller\AbstractController',
            'Concrete\Core\Page\Controller\PageController'

        );
        try {
            $r = new \ReflectionMethod(get_class($this), $method);
            $cl = $r->getDeclaringClass();
            if (is_object($cl)) {
                if (
                    !in_array($cl->getName(), $restrictedControllers)
                    && strpos($method, 'on_') !== 0
                    && strpos($method, '__') !== 0
                    && $r->isPublic()
                    && !$r->isConstructor()
                    && (is_array($this->restrictedMethods) && !in_array($method, $this->restrictedMethods))
                ) {
                    $foundTask = true;
                }
            }
        } catch (\Exception $e) {
        }

        if ($foundTask) {
            $this->action = $method;
            if (isset($taskparts[1])) {
                array_shift($taskparts);
                $this->parameters = $taskparts;
            }
        } else {
            $this->action = 'view';
            if ($taskparts[0] !== '') {
                $this->parameters = $taskparts;
            }
        }
    }

    public function isValidControllerTask($action, $parameters = array())
    {
        $valid = true;
        if (!is_callable(array($this, $this->action)) && count($this->parameters) > 0) {
            $valid = false;
        }

        if (is_callable(array($this, $this->action))  && (get_class($this) != '\Concrete\Controller\PageForbidden')) {
            // we use reflection to see if the task itself, which now much exist, takes fewer arguments than
            // what is specified
            $r = new \ReflectionMethod(get_class($this), $this->action);
            if ($r->getNumberOfParameters() < count($this->parameters)) {
                $valid = false;
            }
        }

        return $valid;
    }

    /**
     * @param Block $b
     * @param BlockController $controller
     */
    public function setPassThruBlockController(Block $b, BlockController $controller)
    {
        $this->passThruBlocks[$b->getBlockID()] = $controller;
    }

    public function getPassThruBlockController(Block $b)
    {
        $bID = $b->getBlockID();

        return isset($this->passThruBlocks[$bID]) ? $this->passThruBlocks[$bID] : null;
    }

    public function validateRequest()
    {
        $valid = true;
        if (!$this->isValidControllerTask($this->action, $this->parameters)) {
            // This is not a page tas let's check blocks'
            $valid = false;
            
            // check if we are dealing with a task called by a block's add or edit mode
            // 2 checks are used: presence of a specific path OR presence of a specific parameter
            $blockAddRequest = ((strpos($this->request->getPath(), '/ccm/system/block/action/add') !== false) || (array_slice($this->parameters, -1)[0] === "blockAdd"));
            $blockEditRequest = ((strpos($this->request->getPath(), '/ccm/system/block/action/edit') !== false) || (array_slice($this->parameters, -1)[0] === "blockEdit"));
            
            // If we are dealing with a block in add or edit mode, let's do this
            if ($blockAddRequest || $blockEditRequest) {
                // in Add mode, let's grab the block type's controller
                if ($blockAddRequest) {
                    $controller = BlockType::getByID($this->parameters[7])->controller;
                }
                // In edit mode, let's grab the block's controller
                if ($blockEditRequest) {
                    $controller = Block::getByID($this->parameters[7])->getController();
                }
                // let's remove everything in this->parameters that is not what we need. We need the task and its parameters
                // everything before that is just the different components of the path
                list($method, $parameters) = $controller->getPassThruActionAndParameters(array_slice($this->parameters, 8));

                // let's check that we're dealing with a valid task
                if ($controller->isValidControllerTask($method, $parameters)) {
                        // pretty good. Let's get rid of the strings we added in BlockView.php ("blockAdd" or "blockEdit")
                        // in both the local $this->parameters variable and $parameters variable we're sending to the task function
                        // then run the task
                        array_pop($this->parameters);
                        array_pop($parameters);
                        $controller->on_start();
                        $response = $controller->runAction($method, $parameters);
                        if ($response instanceof Response) {
                            return $response;
                        }
                        // old school blocks have already terminated at this point. They are redirecting
                        // or exiting. But new blocks like topics, etc... can actually rely on their $set
                        // data persisting and being passed into the view.

                        // so if we make it down here we have to return true –so that we don't fire a 404.
                        $valid = true;

                        // then, we need to save the persisted data that may have been set.
                        $controller->setPassThruBlockController($this);
                    }
            } else {
                // We're not in add or edit mode so it must be a view for a block from the page
                $blocks = array_merge($this->getPageObject()->getBlocks(), $this->getPageObject()->getGlobalBlocks());
                foreach ($blocks as $b) {
                    $controller = $b->getController();
                    list($method, $parameters) = $controller->getPassThruActionAndParameters($this->parameters);
                    // if it's a proxy let's grab that
                    if (is_object($b->getProxyBlock())) {
                        $b = $b->getProxyBlock();
                    }
                    // we add the bID as the last value in our parameters array for checking purposes
                    $parameters[] = $b->getBlockID();

                    if ($controller->isValidControllerTask($method, $parameters)) {
                        // it's a valid task for this block's instance
                        // let's remove the bID we added to parameters and run the task
                        array_pop($parameters);
                        $controller->on_start();
                        $response = $controller->runAction($method, $parameters);
                        if ($response instanceof Response) {
                            return $response;
                        }
                        // old school blocks have already terminated at this point. They are redirecting
                        // or exiting. But new blocks like topics, etc... can actually rely on their $set
                        // data persisting and being passed into the view.

                        // so if we make it down here we have to return true –so that we don't fire a 404.
                        $valid = true;

                        // then, we need to save the persisted data that may have been set.
                        $controller->setPassThruBlockController($this);
                    }
                }
            }
            

            if (!$valid) {
                // finally, we check additional page paths.
                $paths = $this->getPageObject()->getAdditionalPagePaths();
                foreach ($paths as $path) {
                    if ($path->getPagePath() == $this->request->getPath()) {
                        // This is an additional page path to a page. We 301 redirect.
                        return Redirect::page($this->getPageObject(), 301);
                    }
                }
            }
        }

        return $valid;
    }
}
