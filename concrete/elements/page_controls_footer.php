<?php
defined('C5_EXECUTE') or die('Access Denied.');

$app = Concrete\Core\Support\Facade\Facade::getFacadeApplication();

$dh = $app->make('helper/concrete/dashboard');
$sh = $app->make('helper/concrete/dashboard/sitemap');

if (isset($cp) && $cp->canViewToolbar() && (!$dh->inDashboard())) {
    $cih = $app->make('helper/concrete/ui');
    $ihm = $app->make('helper/concrete/ui/menu');
    $valt = $app->make('helper/validation/token');
    $config = $app->make('config');
    $dateHelper = $app->make('helper/date');
    $token = '&' . $valt->getParameter();
    $cID = $c->getCollectionID();
    $permissions = new Permissions($c);

    $workflowList = \Concrete\Core\Workflow\Progress\PageProgress::getList($c);

    $show_titles = (bool) $config->get('concrete.accessibility.toolbar_titles');
    $show_tooltips = (bool) $config->get('concrete.accessibility.toolbar_tooltips');
    $large_font = (bool) $config->get('concrete.accessibility.toolbar_large_font');

    $canApprovePageVersions = $cp->canApprovePageVersions();
    $vo = $c->getVersionObject();
    $pageInUseBySomeoneElse = false;

    if ($c->isCheckedOut()) {
        if (!$c->isCheckedOutByMe()) {
            $pageInUseBySomeoneElse = true;
        }
    }

    if (!$c->isEditMode()) {
        echo $app->make('helper/concrete/ui/help')->displayHelpDialogLauncher();
    }


    if ($cih->showHelpOverlay()) {
        print '<div style="display: none">';
        View::element('help/dialog/introduction');
        print '</div>';
        $v = View::getInstance();
        $v->addFooterItem('<script type="text/javascript">$(function() { new ConcreteHelpDialog().open(); });</script>');
        $cih->trackHelpOverlayDisplayed();
    }

    ?>
    <div id="ccm-page-controls-wrapper" class="ccm-ui">
        <div id="ccm-toolbar" class="<?= $show_titles ? 'titles' : '' ?> <?= $large_font ? 'large-font' : '' ?>">
            <div class="ccm-mobile-menu-overlay" style="height: calc(100vh - 48px)">
                <div class="ccm-mobile-menu-main">
                    <ul class="ccm-mobile-menu-entries">
                        <?php
                        if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) {
                            if ($c->isEditMode()) {
                                ?>
                                <li class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit">
                                    <i class="fa fa-pencil mobile-leading-icon"></i>
                                    <a
                                        <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                        data-toolbar-action="check-in"
                                        <?php
                                        if ($vo->isNew() && !$c->isMasterCollection()) {
                                            ?>
                                            href="javascript:void(0)"
                                            data-launch-panel="check-in"
                                            ><?php echo t('Save Changes') ?><?php
                                        } else {
                                            ?>
                                            href="<?= URL::to('/ccm/system/page/check_in', $cID, $valt->generate()) ?>"
                                            data-panel-url="<?= URL::to('/ccm/system/panels/page/check_in') ?>"
                                            ><?php echo t('Save Changes') ?><?php
                                        }
                                        ?>
                                    </a>
                                </li>
                                <?php
                            } elseif ($permissions->canEditPageContents()) {
                                ?>
                                <li class="ccm-toolbar-page-edit">
                                    <i class="fa fa-pencil mobile-leading-icon"></i>
                                    <a
                                        <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                        data-toolbar-action="check-out"
                                        href="<?= DIR_REL ?>/<?= DISPATCHER_FILENAME ?>?cID=<?= $cID ?>&ctask=check-out<?= $token ?>"
                                    ><?php echo t('Edit this Page') ?></a>
                                </li>
                                <?php
                            }
                            ?>
                            <li class="parent-ul">
                                <i class="fa fa-cog mobile-leading-icon"></i>
                                <a href="#"><?php echo t('Page Properties') ?></a><i class="fa fa-caret-down drop-down-toggle"></i>
                                <ul class="list-unstyled">
                                    <?php
                                    $pagetype = PageType::getByID($c->getPageTypeID());
                                    if (is_object($pagetype) && $cp->canEditPageContents()) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="640"
                                                dialog-height="640"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Composer') ?>"
                                                href="<?= URL::to('/ccm/system/panels/details/page/composer') ?>?cID=<?= $cID ?>"
                                            ><?= t('Composer') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if (
                                        $permissions->canEditPageProperties() ||
                                        $permissions->canEditPageTheme() ||
                                        $permissions->canEditPageTemplate() ||
                                        $permissions->canDeletePage() ||
                                        $permissions->canEditPagePermissions()
                                    ) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="640"
                                                dialog-height="360"
                                                dialog-modal="false"
                                                dialog-title="<?= t('SEO') ?>"
                                                href="<?= URL::to('/ccm/system/panels/details/page/seo') ?>?cID=<?= $cID ?>"
                                            ><?= t('SEO') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($permissions->canEditPageProperties()) {
                                        if ($cID > 1) {
                                            ?>
                                            <li>
                                                <a
                                                    class="dialog-launch"
                                                    dialog-width="500"
                                                    dialog-height="500"
                                                    dialog-modal="false"
                                                    dialog-title="<?= t('Location') ?>"
                                                    href="<?= URL::to('/ccm/system/panels/details/page/location') ?>?cID=<?= $cID ?>"
                                                ><?= t('Location'); ?></a>
                                            </li>
                                            <?php
                                        }
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="90%"
                                                dialog-height="70%"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Attributes') ?>"
                                                href="<?= URL::to('/ccm/system/dialogs/page/attributes') ?>?cID=<?= $cID ?>"
                                            ><?= t('Attributes') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($permissions->canEditPageSpeedSettings()) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="550"
                                                dialog-height="280"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Caching') ?>"
                                                href="<?= URL::to('/ccm/system/panels/details/page/caching') ?>?cID=<?= $cID ?>"
                                            ><?= t('Caching') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($permissions->canEditPagePermissions()) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="500"
                                                dialog-height="630"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Permissions') ?>"
                                                href="<?= URL::to('/ccm/system/panels/details/page/permissions') ?>?cID=<?= $cID ?>"
                                            ><?= t('Permissions') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($permissions->canEditPageTheme() || $permissions->canEditPageTemplate()) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="350"
                                                dialog-height="250"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Design') ?>"
                                                href="<?= URL::to('/ccm/system/dialogs/page/design') ?>?cID=<?= $cID ?>"
                                            ><?= t('Design') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($permissions->canViewPageVersions()) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="640"
                                                dialog-height="340"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Versions') ?>"
                                                href="<?= URL::to('/ccm/system/panels/page/versions') ?>?cID=<?= $cID ?>"
                                            ><?= t('Versions') ?></a>
                                        </li>
                                        <?php
                                    }
                                    if ($permissions->canDeletePage()) {
                                        ?>
                                        <li>
                                            <a
                                                class="dialog-launch"
                                                dialog-width="360"
                                                dialog-height="250"
                                                dialog-modal="false"
                                                dialog-title="<?= t('Delete') ?>"
                                                href="<?= URL::to('/ccm/system/dialogs/page/delete') ?>?cID=<?= $cID ?>"
                                            ><?php echo t('Delete') ?></a>
                                        </li>
                                        <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                            <?php
                        }
                        if ($dh->canRead()) {
                            ?>
                            <li class="parent-ul">
                                <?php
                                $dashboardMenu = new \Concrete\Controller\Element\Navigation\DashboardMobileMenu();
                                $dashboardMenu->render();
                                ?>
                            </li>
                            <?php
                        }
                        ?>
                        <li>
                            <i class="fa fa-sign-out mobile-leading-icon"></i>
                            <a href="<?= URL::to('/login', 'do_logout', $valt->generate('do_logout')); ?>"><?= t('Sign Out'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
            <ul class="ccm-toolbar-item-list">
                <li class="ccm-logo pull-left"><span><?= $cih->getToolbarLogoSRC() ?></span></li>
                <?php
                if ($c->isMasterCollection()) {
                    ?>
                    <li class="pull-left">
                        <a href="<?php echo URL::to('/dashboard/pages/types/output', $c->getPageTypeID()); ?>">
                            <i class="fa fa-arrow-left"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode"><?php echo tc('toolbar', 'Exit Edit Defaults'); ?></span>
                         </a>
                     </li>
                     <?php
                }
                if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) {
                    if ($c->isEditMode()) {
                        ?>
                        <li data-guide-toolbar-action="check-in" class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit pull-left hidden-xs">
                            <a
                                <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                data-toolbar-action="check-in"
                                <?php
                                if ($vo->isNew() || $c->isPageDraft()) {
                                    ?>href="javascript:void(0)" data-launch-panel="check-in"<?php
                                } else {
                                    ?>href="<?= URL::to('/ccm/system/page/check_in', $cID, $valt->generate()) ?>"<?php
                                }
                                ?>
                                data-panel-url="<?= URL::to('/ccm/system/panels/page/check_in') ?>"
                                title="<?= t('Exit Edit Mode') ?>"
                            >
                                <i class="fa fa-pencil"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode"><?= tc('toolbar', 'Exit Edit Mode') ?></span>
                            </a>
                        </li>
                        <?php
                    } elseif ($permissions->canEditPageContents()) {
                        ?>
                        <li data-guide-toolbar-action="edit-page" class="ccm-toolbar-page-edit pull-left hidden-xs">
                            <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }'
                                <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                data-toolbar-action="check-out"
                                href="<?= DIR_REL ?>/<?= DISPATCHER_FILENAME ?>?cID=<?= $cID ?>&ctask=check-out<?= $token ?>"
                                title="<?= t('Edit This Page') ?>"
                            >
                                <i class="fa fa-pencil"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode"><?= tc('toolbar', 'Edit Mode') ?></span>
                            </a>
                        </li>
                        <?php
                    }
                    if (
                        !$c->isMasterCollection() && (
                            $permissions->canEditPageProperties() ||
                            $permissions->canEditPageTheme() ||
                            $permissions->canEditPageTemplate() ||
                            $permissions->canDeletePage() ||
                            $permissions->canEditPagePermissions()
                        )
                    ) {
                        $hasComposer = is_object($pagetype) && $cp->canEditPageContents();
                        ?>
                        <li data-guide-toolbar-action="page-settings" class="pull-left hidden-xs">
                            <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }'
                                href="#"
                                data-launch-panel="page"
                                data-panel-url="<?= URL::to('/ccm/system/panels/page') ?>"
                                <?php
                                if ($hasComposer) {
                                    ?>title="<?= t('Composer, Page Design, Location, Attributes and Settings') ?>"><?php
                                } else {
                                    ?>title="<?= t('Page Design, Location, Attributes and Settings') ?>"><?php
                                }
                                ?>

                                <i class="fa fa-cog"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-settings"><?php
                                    if ($hasComposer) {
                                        ?><?= tc('toolbar', 'Composer') ?> / <?php
                                    }
                                    ?><?= tc('toolbar', 'Page Settings') ?></span>
                            </a>
                        </li>
                        <?php
                    }
                }

                if ($cp->canEditPageContents() && (!$pageInUseBySomeoneElse)) {
                    ?>
                    <li data-guide-toolbar-action="add-content" class="ccm-toolbar-add pull-left hidden-xs">
                        <?php if ($c->isEditMode()) { ?>
                            <a href="#" data-launch-panel="add-block" data-panel-url="<?= URL::to('/ccm/system/panels/add') ?>" title="<?= t('Add Content to The Page') ?>">
                                <i class="fa fa-plus"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add"><?= tc('toolbar', 'Add Content') ?></span>
                            </a>
                        <?php } else { ?>
                            <a href="<?= DIR_REL ?>/<?= DISPATCHER_FILENAME ?>?cID=<?= $cID ?>&ctask=check-out-add-block<?= $token ?>" <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' title="<?= t('Add Content to The Page') ?>">
                                <i class="fa fa-plus"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add"><?= tc('toolbar', 'Add Content') ?></span>
                            </a>
                        <?php } ?>
                    </li>
                    <?php
                }

                $items = $ihm->getPageHeaderMenuItems('left');
                foreach ($items as $ih) {
                    $cnt = $ih->getController();
                    if ($cnt->displayItem()) {
                        $cnt->registerViewAssets();
                        ?>
                        <li class="pull-left hidden-xs"><?= $cnt->getMenuItemLinkElement() ?></li>
                        <?php
                    }
                }

                if ($cih->showWhiteLabelMessage()) {
                    ?>
                    <li class="pull-left visible-xs visible-lg" id="ccm-white-label-message"><?= t('Powered by <a href="%s">concrete5</a>.', $config->get('concrete.urls.concrete5')) ?></li>
                    <?php
                }
                ?>
                <li class="pull-right ccm-toolbar-mobile-menu-button visible-xs hidden-sm hidden-md hidden-lg<?=$c->isEditMode() ? ' ccm-toolbar-mobile-menu-button-active' : ''?>">
                    <i class="fa fa-bars fa-2"></i>
                </li>
                <?php
                if ($dh->canRead()) {
                    ?>
                    <li data-guide-toolbar-action="dashboard" class="pull-right hidden-xs ">
                        <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' href="<?= URL::to('/dashboard') ?>" data-launch-panel="dashboard" title="<?= t('Dashboard – Change Site-wide Settings') ?>">
                            <i class="fa fa-sliders"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?= tc('toolbar', 'Dashboard') ?></span>
                        </a>
                    </li>
                    <?php
                } else {
                    ?>
                    <li class="pull-right hidden-xs">
                        <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip" data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' href="<?=URL::to('/login', 'logout', $valt->generate('logout'))?>" title="<?=t('Sign Out')?>">
                            <i class="fa fa-sign-out"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?= tc('toolbar', 'Sign Out') ?></span>
                        </a>
                    </li>
                    <?php
                }
                if ($sh->canViewSitemapPanel()) {
                    ?>
                    <li data-guide-toolbar-action="sitemap" class="pull-right hidden-xs">
                        <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-toggle="tooltip"
                           data-placement="bottom" data-delay='{ "show": 500, "hide": 0 }' href="#"
                           data-panel-url="<?= URL::to('/ccm/system/panels/sitemap') ?>"
                           title="<?= t('Add Pages and Navigate Your Site') ?>" data-launch-panel="sitemap">
                            <i class="fa fa-files-o"></i><span
                                    class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page"><?= tc('toolbar', 'Pages') ?></span>
                        </a>
                    </li>
                    <?php
                }
                
                $items = $ihm->getPageHeaderMenuItems('right');
                foreach ($items as $ih) {
                    $cnt = $ih->getController();
                    if ($cnt->displayItem()) {
                        $cnt->registerViewAssets();
                        ?>
                        <li class="pull-right hidden-xs"><?= $cnt->getMenuItemLinkElement() ?></li>
                        <?php
                    }
                }
                ?>
                <li data-guide-toolbar-action="intelligent-search" class="ccm-toolbar-search pull-right hidden-xs">
                    <i class="fa fa-search"></i>
                    <input type="search" id="ccm-nav-intelligent-search" autocomplete="off" tabindex="1"/>
                </li>
            </ul>
        </div>
        <?php

        echo $dh->getIntelligentSearchMenu();

        if ($pageInUseBySomeoneElse) {
            $buttons = array();
            if ($canApprovePageVersions) {
                $buttons[] = '<a onclick="$.get(\'' . REL_DIR_FILES_TOOLS_REQUIRED . '/dashboard/sitemap_check_in?cID=' . $c->getCollectionID() . $token . '\', function() { window.location.reload(); })" href="javascript:void(0)" class="btn btn-xs btn-default">' . t('Force Exit Edit Mode') . '</a>';
            }

            echo $cih->notify(array(
                'title' => t('Editing Unavailable.'),
                'text' => t("%s is currently editing this page.", $c->getCollectionCheckedOutUserName()),
                'type' => 'info',
                'icon' => 'fa fa-exclamation-circle',
                'buttons' => $buttons
            ));
        } else {
            if ($c->getCollectionPointerID() > 0) {
                $buttons = array();
                $buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '" class="btn btn-default btn-xs">' . t('View/Edit Original') . '</a>';
                if ($canApprovePageVersions) {
                    $url = URL::to('/ccm/system/dialogs/page/delete_alias?cID=' . $c->getCollectionPointerOriginalID());
                    $buttons[] = '<a href="' . $url . '" dialog-title="' . t('Remove Alias') . '" class="dialog-launch btn btn-xs btn-danger">' . t('Remove Alias') . '</a>';
                }
                echo $cih->notify(array(
                    'title' => t('Page Alias.'),
                    'text' => t("This page is an alias of one that actually appears elsewhere."),
                    'type' => 'info',
                    'icon' => 'fa fa-info-circle',
                    'buttons' => $buttons,
                ));
            }
            $hasPendingPageApproval = false;

            if (is_array($workflowList)) {
                View::element('workflow/notifications', [
                    'workflowList' => $workflowList,
                ]);
            }

            if (!$c->getCollectionPointerID() && (!is_array($workflowList) || empty($workflowList))) {
                if (is_object($vo)) {
                    if (!$vo->isApproved() && !$c->isEditMode()) {
                        if ($c->isPageDraft()) {
                            echo $cih->notify(array(
                                'title' => t('Page Draft.'),
                                'text' => t("This is an un-published draft."),
                                'type' => 'info',
                                'icon' => 'fa fa-exclamation-circle',
                            ));
                        } else {
                            $buttons = array();
                            if ($canApprovePageVersions && !$c->isCheckedOut()) {
                                $pk = \Concrete\Core\Permission\Key\PageKey::getByHandle('approve_page_versions');
                                $pk->setPermissionObject($c);
                                $pa = $pk->getPermissionAccessObject();
                                $workflows = array();
                                if (is_object($pa)) {
                                    $workflows = $pa->getWorkflows();
                                }
                                $canApproveWorkflow = true;
                                foreach ($workflows as $wf) {
                                    if (!$wf->canApproveWorkflow()) {
                                        $canApproveWorkflow = false;
                                    }
                                }
                                if (!empty($workflows) && !$canApproveWorkflow) {
                                    $appLabel = t('Submit to Workflow');
                                }
                                if (!isset($appLabel) || !$appLabel) {
                                    $appLabel = t('Approve Version');
                                }
                                $buttons[] = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&ctask=approve-recent' . $token . '" class="btn btn-primary btn-xs">' . $appLabel . '</a>';
                            }
                            echo $cih->notify(array(
                                'title' => t('Page is Pending Approval.'),
                                'text' => t("This page is newer than what appears to visitors on your live site."),
                                'type' => 'info',
                                'icon' => 'fa fa-cog',
                                'buttons' => $buttons,
                            ));
                        }
                    } else {
                        $publishDate = $vo->getPublishDate();
                        if ($publishDate) {
                            $date = $dateHelper->formatDate($publishDate);
                            $time = $dateHelper->formatTime($publishDate);
                            $message = t(/*i18n: %1$s is a date, %2$s is a time */'This version of the page is scheduled to be published on %1$s at %2$s.', $date, $time);
                            if ($canApprovePageVersions && !$c->isCheckedOut()) {
                                $button = '<a href="' . DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID . '&ctask=publish-now' . $token . '" class="btn btn-primary btn-xs">' . t('Publish Now') . '</a>';
                            } else {
                                $button = '';
                            }
                            echo $cih->notify(array(
                                'title' => t('Publish Pending.'),
                                'text' => $message,
                                'type' => 'info',
                                'icon' => 'fa fa-cog',
                                'buttons' => array($button),
                            ));
                        }
                    }
                }
            }
        }
    ?>
    </div>
    <?php
}
