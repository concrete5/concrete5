<?php
namespace Concrete\Core\Page\Controller;
use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Page;
use Request;
use Loader;
use Controller;
use Core;
use \Concrete\Core\Page\View\PageView;
class PageController extends Controller {

    protected $supportsPageCache = false;
    protected $action;
    protected $passThruBlocks = array();
    protected $parameters = array();

    public function supportsPageCache() {
        return $this->supportsPageCache;
    }

    public function __construct(Page $c) {
        parent::__construct();
        $this->c = $c;
        $this->view = new PageView($this->c);
        $this->set('html', Core::make('\Concrete\Core\Html\Service\Html'));
    }

    /**
     * Given either a path or a Page object, this is a shortcut to
     * 1. Grab the controller of that page.
     * 2. Grab the view of that controller
     * 3. Render that view.
     * 4. Exit – so we immediately stop all other output in the controller that
     * called render().
     * @param @string|\Concrete\Core\Page\Page $var
     */
    public function render($var)
    {
        if (!($var instanceof \Concrete\Core\Page\Page)) {
            $var = \Page::getByPath($var);
        }

        $controller = $var->getPageController();
        $controller->on_start();
        $controller->runAction('view');
        $controller->on_before_render();
        $view = $controller->getViewObject();
        print $view->render();
        exit;
    }

    public function getPageObject() {
        return $this->c;
    }

    public function getTheme() {
        if (!$this->theme) {
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

    public function getRequestAction() {
        return $this->action;
    }

    public function getRequestActionParameters() {
        return $this->parameters;
    }

    public function getControllerActionPath() {
        if (isset($this->controllerActionPath)) {
            return $this->controllerActionPath;
        }

        if (is_object($this->view)) {
            return $this->view->getViewPath();
        }
    }

    public function setupRequestActionAndParameters(Request $request) {
        if (substr($request->getPath(), 0, strlen($this->c->getCollectionPath()) + 1) == $this->c->getCollectionPath()) {
            $task = substr($request->getPath(), strlen($this->c->getCollectionPath()) + 1);
        } else {
            // must be a sub path
            $possiblePaths = $this->c->getAdditionalPagePaths();
            $task = null;
            $iterator = new \ArrayIterator($possiblePaths);
            while ($task === null && $iterator->valid()) {
                $current = $iterator->current();
                if (substr($request->getPath(), 0, strlen($current->getPagePath()) + 1) == $current->getPagePath()) {
                    $task = substr($request->getPath(), strlen($current->getPagePath()) + 1);
                }

                $iterator->next();
            }
        }


        $task = str_replace('-/', '', $task);
        $taskparts = explode('/', $task);
        if (isset($taskparts[0]) && $taskparts[0] != '') {
            $method = $taskparts[0];
        }
        if ($method == '') {
            if (is_object($this->c) && is_callable(array($this, $this->c->getCollectionHandle()))) {
                $method = $this->c->getCollectionHandle();
            } else {
                $method = 'view';
            }
        }

        try {
            $r = new \ReflectionMethod(get_class($this), $method);
            $cl = $r->getDeclaringClass();
            if (is_object($cl)) {
                if ($cl->getName() != 'Concrete\Core\Controller\Controller' && strpos($method, 'on_') !== 0 && strpos($method, '__') !== 0 && $r->isPublic()) {
                    $foundTask = true;
                }
            }
        } catch(\Exception $e) {

        }

        if ($foundTask) {
            $this->action = $method;
            if (isset($taskparts[1])) {
                array_shift($taskparts);
                $this->parameters = $taskparts;
            }
        } else {
            $this->action = 'view';
            if ($taskparts[0]) {
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

    protected function setPassThruBlockController(Block $b, BlockController $controller)
    {
        $this->passThruBlocks[$b->getBlockID()] = $controller;
    }

    public function getPassThruBlockController(Block $b)
    {
        return $this->passThruBlocks[$b->getBlockID()];
    }

    public function validateRequest() {

        $valid = true;

        if (!$this->isValidControllerTask($this->action, $this->parameters)) {
            $valid = false;
            // we check the blocks on the page.
            $blocks = $this->getPageObject()->getBlocks();
            foreach($blocks as $b) {
                $controller = $b->getController();
                list($method, $parameters) = $controller->getPassThruActionAndParameters($this->parameters);
                if ($controller->isValidControllerTask($method, $parameters)) {
                    $controller->on_start();
                    $controller->runAction($method, $parameters);

                    // old school blocks have already terminated at this point. They are redirecting
                    // or exiting. But new blocks like topics, etc... can actually rely on their $set
                    // data persisting and being passed into the view.

                    // so if we make it down here we have to return true –so that we don't fire a 404.
                    $valid = true;

                    // then, we need to save the persisted data that may have been set.
                    $this->setPassThruBlockController($b, $controller);
                }
            }
        }
        return $valid;
    }
}
