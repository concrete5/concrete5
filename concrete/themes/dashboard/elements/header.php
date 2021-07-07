<?php
use Concrete\Core\Support\Facade\Application;
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

$view->addFooterItem('<script type="text/javascript">$(function() { ConcreteToolbar.start(); });</script>');
$view->addHeaderItem('<meta name="viewport" content="width=device-width, initial-scale=1">');

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
<html<?= $hideDashboardPanel ? '' : ' class="ccm-panel-open ccm-panel-right"'; ?>>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$this->getThemePath(); ?>/main.css" />
    <?php View::element('header_required', ['disableTrackingCode' => true, 'pageTitle' => isset($pageTitle) ? $pageTitle : null]); ?>
</head>
<body <?php if (isset($bodyClass)) {
    ?>class="<?=$bodyClass; ?>"<?php
} ?>>
    <div id="ccm-dashboard-page" class="<?php if ($view->section('/account')) {
        ?>ccm-dashboard-my-account<?php
    } ?> ccm-ui">
        <?=View::element('icons'); ?>
        <div id="ccm-toolbar" class="<?= $show_titles ? 'titles' : ''; ?> <?= $large_font ? 'large-font' : ''; ?>">
            <ul>
                <li class="ccm-logo float-start"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC(); ?></span></li>
                <li class="float-start ccm-toolbar-button-with-text">
                    <a href="<?=$backLink; ?>">
                        <svg><use xlink:href="#icon-arrow-left" /></svg>
                        <span><?=t('To My Website'); ?></span>
                    </a>
                </li>
								<?php
								$mobileMenu = Element::get('dashboard/navigation/mobile');
								$mobileMenu->render();
								?>
                <?php
                $ihm = $app->make('helper/concrete/ui/menu');
                $cih = $app->make('helper/concrete/ui');
                $items = $ihm->getPageHeaderMenuItems('left');
                foreach ($items as $ih) {
                    $cnt = $ih->getController();
                    if ($cnt->displayItem()) {
                        $cnt->registerViewAssets(); ?>
                        <li class="float-start"><?= $cnt->getMenuItemLinkElement(); ?></li>
                        <?php
                    }
                }
                ?>
                <li class="float-end d-none d-sm-none d-md-block">
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
                    <a class="<?=$dashboardPanelClass; ?>" data-bs-placement="bottom" href="<?= URL::to('/dashboard'); ?>" title="<?= t('Dashboard – Change Site-wide Settings'); ?>"
                        data-launch-panel="dashboard"
                        data-panel-url="<?=URL::to('/system/panels/dashboard'); ?>"
                    >
                        <svg><use xlink:href="#icon-dashboard" /></svg>
                        <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?= tc('toolbar', 'Dashboard'); ?></span>
                    </a>
                </li>
                <li class="float-end d-none d-sm-none d-md-block">
                    <a <?php if ($show_tooltips) {
                        ?>class="launch-tooltip"<?php
                    } ?>  data-bs-toggle="tooltip" data-bs-placement="bottom" href="#" data-panel-url="<?= URL::to('/ccm/system/panels/sitemap'); ?>" title="<?= t('Add Pages and Navigate Your Site'); ?>" data-launch-panel="sitemap">
                        <svg><use xlink:href="#icon-sitemap" /></svg>
                        <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page"><?= tc('toolbar', 'Pages'); ?></span>
                    </a>
                </li>
                <li data-guide-toolbar-action="help" class="float-end d-none d-sm-block">
                    <a <?php if ($show_tooltips) {
                        ?>class="launch-tooltip"<?php
                    } ?> data-bs-toggle="tooltip"
                       data-bs-placement="bottom" href="#"
                       data-panel-url="<?= URL::to('/ccm/system/panels/help'); ?>"
                       title="<?= t('View help about the CMS.'); ?>" data-launch-panel="help">
                        <svg><use xlink:href="#icon-help" /></svg><span
                                class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page"><?= tc('toolbar', 'Help'); ?></span>
                    </a>
                </li>
                <li class="ccm-toolbar-search float-end d-none d-sm-none d-lg-block">
                    <?php
                    $menu = Element::get('navigation/intelligent_search');
                    $menu->render();
                    ?>
                </li>
                <?php
                $items = $ihm->getPageHeaderMenuItems('right');
                foreach ($items as $ih) {
                    $cnt = $ih->getController();
                    if ($cnt->displayItem()) {
                        $cnt->registerViewAssets(); ?>
                        <li class="float-end"><?= $cnt->getMenuItemLinkElement(); ?></li>
                        <?php
                    }
                }
                ?>
            </ul>
        </div>
        <?php
        $dh = $app->make('helper/concrete/dashboard');
        if (!$hideDashboardPanel) {
            ?>
            <div id="ccm-panel-dashboard" class="d-none d-md-block ccm-panel ccm-panel-right ccm-panel-transition-slide ccm-panel-active ccm-panel-loaded">
                <div class="ccm-panel-content-wrapper ccm-ui">
                    <div class="ccm-panel-content ccm-panel-content-visible">
                      <?php
            View::element(
                          'panels/dashboard',
                          [
                              'c' => $c,
                          ]
                      ); ?>
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
        <div id="ccm-dashboard-content">
