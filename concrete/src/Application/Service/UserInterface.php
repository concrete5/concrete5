<?php

namespace Concrete\Core\Application\Service;

use Concrete\Core\Application\UserInterface\Dashboard\Navigation\FullNavigationFactory;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationCache;
use Concrete\Core\Application\UserInterface\Dashboard\Navigation\NavigationFactory;
use Concrete\Core\Http\Response;
use HtmlObject\Element;
use HtmlObject\Traits\Tag;
use PermissionKey;
use Concrete\Core\User\User as ConcreteUser;
use Concrete\Core\Support\Facade\Application;
use Loader;
use Core;
use Page;
use Config;
use Session;
use Concrete\Core\View\ErrorView;
use stdClass;

/**
 * Useful functions for generating elements on the Concrete interface.
 *
 * @subpackage Concrete
 * \@package Helpers
 *
 * @author Andrew Embler <andrew@concrete5.org>
 * @copyright  Copyright (c) 2003-2008 Concrete5. (http://www.concrete5.org)
 * @license    http://www.concrete5.org/license/     MIT License
 */
class UserInterface
{
    public static $menuItems = [];

    /**
     * Generates a submit button in the Concrete style.
     *
     * @param string $text The text of the button
     * @param bool|string $formID The form this button will submit
     * @param string $buttonAlign
     * @param string $innerClass
     * @param array $args Extra args passed to the link
     *
     * @return string
     */
    public function submit($text, $formID = false, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        if ('right' == $buttonAlign) {
            $innerClass .= ' float-end';
        } elseif ('left' == $buttonAlign) {
            $innerClass .= ' float-start';
        }

        if (!$formID) {
            $formID = 'button';
        }
        $argsstr = '';
        foreach ($args as $k => $v) {
            $argsstr .= $k . '="' . $v . '" ';
        }

        return '<input type="submit" class="btn ' . $innerClass . '" value="' . $text . '" id="ccm-submit-' . $formID . '" name="ccm-submit-' . $formID . '" ' . $argsstr . ' />';
    }

    /**
     * Generates a simple link button in the Concrete style.
     *
     * @param string $text The text of the button
     * @param string $href
     * @param string $buttonAlign
     * @param string $innerClass
     * @param array $args Extra args passed to the link
     *
     * @return string
     */
    public function button($text, $href, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        if ('right' == $buttonAlign) {
            $innerClass .= ' float-end';
        } elseif ('left' == $buttonAlign) {
            $innerClass .= ' float-start';
        }
        $argsstr = '';
        foreach ($args as $k => $v) {
            $argsstr .= $k . '="' . $v . '" ';
        }

        return '<a href="' . $href . '" class="btn btn-secondary ' . $innerClass . '" ' . $argsstr . '>' . $text . '</a>';
    }

    /**
     * Generates a JavaScript function button in the Concrete style.
     *
     * @param string $text The text of the button
     * @param string $onclick
     * @param string $buttonAlign
     * @param string $innerClass - no longer used
     * @param array $args Extra args passed to the link
     *
     * @return string
     */
    public function buttonJs($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        if ('right' == $buttonAlign) {
            $innerClass .= ' float-end';
        } elseif ('left' == $buttonAlign) {
            $innerClass .= ' float-start';
        }
        $argsstr = '';
        foreach ($args as $k => $v) {
            $argsstr .= $k . '="' . $v . '" ';
        }

        return '<input type="button" class="btn btn-secondary ' . $innerClass . '" value="' . $text . '" onclick="' . $onclick . '" ' . $buttonAlign . ' ' . $argsstr . ' />';
    }

    /**
     * @deprecated
     */
    public function button_js($text, $onclick, $buttonAlign = 'right', $innerClass = null, $args = [])
    {
        return self::buttonJs($text, $onclick, $buttonAlign, $innerClass, $args);
    }

    /**
     * Outputs button text passed as arguments with a special Concrete wrapper for positioning
     * <code>
     *    $bh->buttons($myButton1, $myButton2, $myButton3);
     * </code>.
     *
     * @param string $buttons
     *
     * @return string
     */
    public function buttons($buttons = null)
    {
        if (!is_array($buttons)) {
            $buttons = func_get_args();
        }
        $html = '<div class="ccm-buttons well">';
        foreach ($buttons as $_html) {
            $html .= $_html . ' ';
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * @param \Concrete\Core\Page\Page $c
     *
     * @return string
     */
    public function getQuickNavigationLinkHTML($c)
    {
        $cnt = Loader::controller($c);
        if (method_exists($cnt, 'getQuickNavigationLinkHTML')) {
            return $cnt->getQuickNavigationLinkHTML();
        } else {
            return '<a href="' . Core::make('helper/navigation')->getLinkToCollection($c) . '">' . $c->getCollectionName() . '</a>';
        }
    }

    /**
     * @return bool
     */
    public function showWhiteLabelMessage()
    {
        return Config::get('concrete.white_label.logo') || file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/logo_menu.png');
    }

    /**
     * @return string
     */
    public function getToolbarLogoSRC()
    {
        $alt = false;
        $src = false;
        if (Config::get('concrete.white_label.name')) {
            $alt = Config::get('concrete.white_label.name');
        }
        if (!$alt) {
            $alt = 'concrete5';
        }
        if (Config::get('concrete.white_label.logo')) {
            $src = Config::get('concrete.white_label.logo');
        }
        if (!$src) {
            $filename = 'logo';
            if (file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.svg')) {
                $src = REL_DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.svg';
            } elseif (file_exists(DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.png')) {
                $src = REL_DIR_APPLICATION . '/' . DIRNAME_IMAGES . '/' . $filename . '.png';
            } else {
                $src = ASSETS_URL_IMAGES . '/' . $filename . '.svg';
            }
        }

        return '<img id="ccm-logo" src="' . $src . '" alt="' . $alt . '" title="' . $alt . '">';
    }

    /**
     * @return bool
     * @deprecated The Newsflow Overlay feature has been removed
     *
     */
    public function showNewsflowOverlay()
    {
        return false;
    }

    /**
     * @deprecated There's no more an "Introduction" dialog: we now have a Help panel.
     *
     * @return false
     */
    public function showHelpOverlay()
    {
        return false;
    }

    /**
     * @deprecated There's no more an "Introduction" dialog: we now have a Help panel.
     */
    public function trackHelpOverlayDisplayed()
    {
    }

    /**
     * Clears the Interface Items Cache (clears the session).
     */
    public function clearInterfaceItemsCache()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(ConcreteUser::class);
        if ($u->isRegistered()) {
            Session::remove('dashboardMenus');
        }
    }

    /**
     * Cache the interface items.
     */
    public function cacheInterfaceItems()
    {
        $app = Application::getFacadeApplication();
        $u = $app->make(ConcreteUser::class);
        if ($u->isRegistered()) {
            if ($app->make(Dashboard::class)->canRead()) {
                $navigationFactory = $app->make(FullNavigationFactory::class);
                $navigation = $navigationFactory->createNavigation();
                $cache = $app->make(NavigationCache::class);
                $cache->set($navigation);
            }
        }
    }

    /**
     * @param \Concrete\Core\Page\Page[] $tabs
     *
     * @return string
     */
    public function pagetabs($tabs)
    {
        $tcn = rand(0, getrandmax());

        $html = '<ul class="nav-tabs nav-fill nav mb-3" id="ccm-tabs-' . $tcn . '">';
        $c = Page::getCurrentPage();
        foreach ($tabs as $t) {
            if (is_array($t)) {
                $name = $t[1];
                $_c = $t[0];
            } else {
                $_c = $t;
                $name = $t->getCollectionName();
            }

            $href = Core::make('helper/navigation')->getLinkToCollection($_c);
            $active = false;
            if (is_object($c) && $c->getCollectionID() == $_c->getCollectionID()) {
                $active = true;
            }
            $html .= '<li class="' . (($active) ? 'active' : '') . '"><a href="' . $href . '">' . $name . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }

    /**
     * @param $tabs
     *
     * @param null|string $id
     * @return string
     */
    public function tabs($tabs, $id = null)
    {
        $ul = new Element("ul");
        $ul->addClass("nav");
        $ul->addClass("nav-tabs mb-3 nav-fill");
        $ul->setAttribute("role", "tablist");

        if ($id !== null) {
            $ul->setAttribute("id", $id);
        }

        foreach ($tabs as $tab) {
            $a = new Element("a");
            $a->addClass("nav-link");

            if ((isset($tab[2]) && $tab[2])) {
                $a->addClass("active");
            }

            if (strpos($tab[0], "/") !== false) {
                $a->setAttribute("href", $tab[0]);
            } else {
                $a->setAttribute("href", "#" . $tab[0]);
                $a->setAttribute("data-bs-toggle", "tab");
            }

            $a->setAttribute("id", $tab[0] . "-tab");
            $a->setAttribute("aria-controls", $tab[0]);
            $a->setAttribute("role", "tab");
            $a->setAttribute("aria-selected", (isset($tab[2]) && $tab[2]) ? "true" : "false");
            $a->setValue($tab[1]);

            $li = new Element("li");
            $li->addClass("nav-item");
            $li->appendChild($a);
            $ul->appendChild($li);
        }

        return (string)$ul;
    }

    /**
     * @param string $title
     * @param string $error
     * @param bool|\Exception $exception
     */
    public function renderError($title, $error, $exception = false)
    {
        Response::closeOutputBuffers(1, false);
        $this->buildErrorResponse($title, $error, $exception)->send();
    }

    /**
     * @param string $title
     * @param string $error
     * @param bool|\Exception $exception
     *
     * @return Response;
     */
    public function buildErrorResponse($title, $error, $exception = false)
    {
        $o = new stdClass();
        $o->title = $title;
        $o->content = $error;
        if ($exception) {
            $o->content .= $exception->getTraceAsString();
        }

        $ve = new ErrorView($o);
        $contents = $ve->render($o);

        return Response::create($contents, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @param array $arguments
     *
     * @return string
     */
    public function notify($arguments)
    {
        $defaults = [
            'type' => 'success',
            'icon' => 'fa fa-check-mark',
            'title' => false,
            'text' => false,
            'form' => false,
            'hide' => false,
            'buttons' => [],
        ];

        // overwrite all the defaults with the arguments
        $arguments = array_merge($defaults, $arguments);

        $text = '';

        if ($arguments['form']) {
            $text .= $arguments['form'];
        }

        $text .= $arguments['text'];

        if (count($arguments['buttons']) > 0) {
            $text .= '<div class="ccm-notification-inner-buttons">';
            if (count($arguments['buttons']) === 1) {
                $singleButton = $arguments['buttons'][0];
                if ($singleButton instanceof Tag) {
                    $singleButton->addClass('btn btn-primary');
                }
                $text .= '<div>' . $singleButton . '</div>';
            } else {
                $text .= '<div class="dropup"><button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' . t('Action') . '</button><div class="dropdown-menu">';
                foreach ($arguments['buttons'] as $button) {
                    if ($button instanceof Tag) {
                        $button->addClass('dropdown-item');
                    }
                    $text .= $button;
                }
                $text .= '</div></div>';
            }
            $text .= '</div>';
        }

        if ($arguments['form']) {
            $text .= '</form>';
        }

        $arguments['text'] = $text;

        unset($arguments['buttons'], $arguments['form']);
        $string = json_encode($arguments);

        $content = '<script type="text/javascript">$(function() {';
        $content .= 'ConcretePageNotification.notify(' . $string . ');';
        $content .= '});</script>';

        return $content;
    }
}
