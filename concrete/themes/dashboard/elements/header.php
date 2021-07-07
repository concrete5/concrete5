<?php
use Concrete\Core\Support\Facade\Application;
use Concrete\Controller\Element\Navigation\DashboardMobileMenu;
use Concrete\Controller\Panel\Dashboard as DashboardPanel;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Application::getFacadeApplication();

$html = $app->make('helper/html');
/* @var Concrete\Core\Html\Service\Html $html */

$valt = $app->make('helper/validation/token');
/* @var Concrete\Core\Validation\CSRF\Token $valt */

$config = $app->make('config');

if (!isset($hideDashboardPanel)) {
    $hideDashboardPanel = false;
}

$view->requireAsset('dashboard');
$view->requireAsset('javascript-localized', 'core/localization');
$view->addFooterItem('<script type="text/javascript">$(function() { ConcreteToolbar.start(); });</script>');
if ($config->get('concrete.misc.enable_progressive_page_reindex') && $config->get('concrete.misc.do_page_reindex_check')) {
    $view->addFooterItem('<script type="text/javascript">$(function() { ccm_doPageReindexing(); });</script>');
}
$view->addHeaderItem('<meta name="viewport" content="width=device-width, initial-scale=1">');
$view->addFooterItem('<script type="text/javascript">$(function() { ConcreteDashboard.start(); });</script>');

$u = $app->make(Concrete\Core\User\User::class);
$frontendPageID = $u->getPreviousFrontendPageID();
if (!$frontendPageID) {
    $backLink = DIR_REL . '/';
} else {
    $backLink = DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $frontendPageID;
}

$show_titles = (bool) $config->get('concrete.accessibility.toolbar_titles');
$show_tooltips = (bool) $config->get('concrete.accessibility.toolbar_tooltips');
$large_font = (bool) $config->get('concrete.accessibility.toolbar_large_font');

?><!DOCTYPE html>
<html<?= $hideDashboardPanel ? '' : ' class="ccm-panel-open ccm-panel-right"'; ?> lang="<?php echo Localization::activeLanguage() ?>">
<head>
    <link rel="stylesheet" type="text/css" href="<?=$this->getThemePath()?>/main.css" />
    <?php View::element('header_required', ['disableTrackingCode' => true, 'pageTitle' => isset($pageTitle) ? $pageTitle : null]); ?>
    <link href='https://fonts.googleapis.com/css?family=Roboto:900' rel='stylesheet' type='text/css'>
</head>
<body <?php if (isset($bodyClass)) { ?>class="<?=$bodyClass?>"<?php } ?>>
    <div id="ccm-dashboard-page" class="<?php if ($view->section('/account')) { ?>ccm-dashboard-my-account<?php } ?> ccm-ui">
        <div class="ccm-mobile-menu-overlay ccm-mobile-menu-overlay-dashboard hidden-md hidden-lg" style="height: calc(100vh - 48px);">
            <div class="ccm-mobile-menu-main">
                <ul class="ccm-mobile-menu-entries">
                    <li>
                        <?php
                        $dashboardMenu = $app->make(DashboardMobileMenu::class, [$c]);
                        $dashboardMenu->render();
                        ?>
                    </li>
                    <li>
                        <i class="fa fa-sign-out mobile-leading-icon"></i><a href="<?= URL::to('/login', 'do_logout', $valt->generate('do_logout')); ?>"><?= t('Sign Out'); ?></a>
                    </li>
                </ul>
            </div>
        </div>
        <div id="ccm-toolbar" class="<?= $show_titles ? 'titles' : '' ?> <?= $large_font ? 'large-font' : '' ?>">
            <ul>
                <li class="ccm-logo pull-left"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
                <li class="ccm-toolbar-account pull-left">
                    <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' title="<?= t('Back to Website') ?>" href="<?=$backLink?>">
                        <i class="fa fa-arrow-left"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-return"><?= tc('toolbar', 'Return to Website') ?></span>
                    </a>
                </li>
                <?php
                $ihm = $app->make("helper/concrete/ui/menu");
                $cih = $app->make('helper/concrete/ui');
                $items = $ihm->getPageHeaderMenuItems('left');
                foreach ($items as $ih) {
                    $cnt = $ih->getController();
                    if ($cnt->displayItem()) {
                        $cnt->registerViewAssets();
                        ?>
                        <li class="pull-left"><?= $cnt->getMenuItemLinkElement() ?></li>
                        <?php
                    }
                }
                if ($cih->showWhiteLabelMessage()) {
                    ?>
                    <li class="pull-left visible-xs visible-lg" id="ccm-white-label-message"><?= t('Powered by <a href="%s">concrete5</a>.', $config->get('concrete.urls.concrete5')) ?></li>
                    <?php
                }
                ?>
                <li class="pull-right hidden-xs hidden-sm">
                    <?php
                    $dashboardPanelClasses = [];
                    if ($show_tooltips) {
                        $dashboardPanelClasses[] = 'launch-tooltip';
                    }
                    if (!$hideDashboardPanel) {
                        $dashboardPanelClasses[] = 'ccm-launch-panel-active';
                    }
                    $dashboardPanelClass = implode(' ', $dashboardPanelClasses);
                    ?>
                    <a class="<?=$dashboardPanelClass?>" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' href="<?= URL::to('/dashboard') ?>" title="<?= t('Dashboard – Change Site-wide Settings') ?>"
                        data-launch-panel="dashboard"
                        data-panel-url="<?=URL::to('/system/panels/dashboard')?>"
                    >
                        <i class="fa fa-sliders"></i>
                        <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?= tc('toolbar', 'Dashboard') ?></span>
                    </a>
                </li>
                <li class="pull-right hidden-xs hidden-sm">
                    <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?>  data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' href="#" data-panel-url="<?= URL::to('/ccm/system/panels/sitemap') ?>" title="<?= t('Add Pages and Navigate Your Site') ?>" data-launch-panel="sitemap">
                        <i class="fa fa-files-o"></i>
                        <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page"><?= tc('toolbar', 'Pages') ?></span>
                    </a>
                </li>
                <li class="ccm-toolbar-search pull-right hidden-xs hidden-sm">
                    <i class="fa fa-search"></i>
                    <input type="search" autocomplete="off" id="ccm-nav-intelligent-search" tabindex="1" />
                </li>
                <li class="pull-right ccm-toolbar-mobile-menu-button visible-xs visible-sm hidden-md hidden-lg">
                    <i class="fa fa-bars"></i>
                </li>
                <?php
                $items = $ihm->getPageHeaderMenuItems('right');
                foreach ($items as $ih) {
                    $cnt = $ih->getController();
                    if ($cnt->displayItem()) {
                        $cnt->registerViewAssets();
                        ?>
                        <li class="pull-right"><?= $cnt->getMenuItemLinkElement() ?></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <?php
        $dh = $app->make('helper/concrete/dashboard');
        echo $dh->getIntelligentSearchMenu();
        if (!$hideDashboardPanel) {
            ?>
            <div id="ccm-panel-dashboard" class="hidden-xs hidden-sm ccm-panel ccm-panel-right ccm-panel-transition-slide ccm-panel-active ccm-panel-loaded">
                <div class="ccm-panel-content-wrapper ccm-ui">
                    <div class="ccm-panel-content ccm-panel-content-visible">
                        <?php
                        $cnt = $app->make(DashboardPanel::class);
                        $cnt->setPageObject($c);
                        $cnt->view();
                        $nav = $cnt->get('nav');
                        $tab = $cnt->get('tab');
                        $ui = $cnt->get('ui');
                        View::element(
                            'panels/dashboard',
                            [
                                'nav' => $nav,
                                'tab' => $tab,
                                'ui' => $ui,
                                'c' => $c,
                            ]
                        );
                        ?>
                    </div>
                </div>
            </div>
            <script>
            (function() {
                var ls = window.localStorage;
                if (!(ls && ls.getItem && ls.setItem && ls.removeItem)) {
                    return;
                }
                var KEY = 'ccm-dashboard-dashboarpanel-scrollTop',
                    $panel = $('.ccm-panel-content'),
                    scrollTop = window.localStorage.getItem(KEY);
                if (scrollTop) {
                    $panel.scrollTop(scrollTop);
                }
                ls.removeItem(KEY);
                $panel.find('a').on('click', function() {
                    scrollTop = $panel.scrollTop();
                    if (scrollTop) {
                        ls.setItem(KEY, $panel.scrollTop());
                    }
                });
            })();
            </script>
            <?php
        }
        ?>
        <div id="ccm-dashboard-content" class="container-fluid">
