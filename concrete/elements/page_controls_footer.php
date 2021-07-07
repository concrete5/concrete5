<?php

use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;

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
    $resolver = $app->make(ResolverManagerInterface::class);

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

    ?>
    <?=View::element('icons')?>
    <div id="ccm-page-controls-wrapper" class="ccm-ui">
        <div id="ccm-toolbar" class="<?= $show_titles ? 'titles' : '' ?> <?= $large_font ? 'large-font' : '' ?>">
						<?php
              $mobileMenu = Element::get('dashboard/navigation/mobile');
              $mobileMenu->render();
            ?> 
            <ul class="ccm-toolbar-item-list">
                <li class="ccm-logo float-start"><span><?= $cih->getToolbarLogoSRC() ?></span></li>
                <?php
                if ($c->isMasterCollection()) {
                    ?>
                    <li class="float-start">
                        <a href="<?php echo URL::to('/dashboard/pages/types/output', $c->getPageTypeID()); ?>">
                            <i class="fa fa-arrow-left"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode"><?php echo tc('toolbar', 'Exit Edit Defaults'); ?></span>
                         </a>
                     </li>
                     <?php
                }
                if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) {
                    if ($c->isEditMode()) {
                        ?>
                        <li data-guide-toolbar-action="check-in" class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit float-start d-none d-md-block">
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
                                <svg><use xlink:href="#icon-pencil" /></svg><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode"><?= tc('toolbar', 'Exit Edit Mode') ?></span>
                            </a>
                        </li>
                        <?php
                    } elseif ($permissions->canEditPageContents()) {
                        ?>
                        <li data-guide-toolbar-action="edit-page" class="ccm-toolbar-page-edit float-start d-none d-md-block">
                            <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip" data-bs-placement="bottom"
                                <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                data-toolbar-action="check-out"
                                href="<?= h($resolver->resolve(["/ccm/system/page/checkout/{$cID}/-/" . $valt->generate()])) ?>"
                                title="<?= t('Edit This Page') ?>"
                            >
                                <svg><use xlink:href="#icon-pencil" /></svg><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-edit-mode"><?= tc('toolbar', 'Edit Mode') ?></span>
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
                        $hasComposer = isset($pagetype) && is_object($pagetype) && $cp->canEditPageContents();
                        ?>
                        <li data-guide-toolbar-action="page-settings" class="float-start d-none d-md-block">
                            <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip" data-bs-placement="bottom"
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

                                <svg><use xlink:href="#icon-cog" /></svg><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-settings"><?php
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
                    <li data-guide-toolbar-action="add-content" class="ccm-toolbar-add float-start d-none d-md-block">
                        <?php if ($c->isEditMode()) { ?>
                            <a href="#" data-launch-panel="add-block" data-panel-url="<?= URL::to('/ccm/system/panels/add') ?>" title="<?= t('Add Content to The Page') ?>">
                                <svg><use xlink:href="#icon-plus" /></svg><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add"><?= tc('toolbar', 'Add Content') ?></span>
                            </a>
                        <?php } else { ?>
                            <a href="<?= h($resolver->resolve(["/ccm/system/page/checkout/{$cID}/add-block/" . $valt->generate()])) ?>" <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?= t('Add Content to The Page') ?>">
                                <svg><use xlink:href="#icon-plus" /></svg><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add"><?= tc('toolbar', 'Add Content') ?></span>
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
                        <li class="float-start d-none d-md-block"><?= $cnt->getMenuItemLinkElement() ?></li>
                        <?php
                    }
                }
                ?>
                <?php
                if ($dh->canRead()) {
                    ?>
                    <li data-guide-toolbar-action="dashboard" class="float-end d-none d-md-block ">
                        <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip" data-bs-placement="bottom" href="<?= URL::to('/dashboard') ?>" data-launch-panel="dashboard" title="<?= t('Dashboard – Change Site-wide Settings') ?>">
                            <svg><use xlink:href="#icon-dashboard" /></svg><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?= tc('toolbar', 'Dashboard') ?></span>
                        </a>
                    </li>
                    <?php
                } else {
                    ?>
                    <li class="float-end d-none d-md-block">
                        <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip" data-bs-placement="bottom" href="<?=URL::to('/login', 'logout', $valt->generate('logout'))?>" title="<?=t('Sign Out')?>">
                            <i class="fa fa-sign-out"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?= tc('toolbar', 'Sign Out') ?></span>
                        </a>
                    </li>
                    <?php
                } ?>
                <?php
                if ($sh->canViewSitemapPanel()) {
                    ?>
                    <li data-guide-toolbar-action="sitemap" class="float-end d-none d-md-block">
                        <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip"
                           data-bs-placement="bottom" href="#"
                           data-panel-url="<?= URL::to('/ccm/system/panels/sitemap') ?>"
                           title="<?= t('Add Pages and Navigate Your Site') ?>" data-launch-panel="sitemap">
                            <svg><use xlink:href="#icon-sitemap" /></svg><span
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
                        <li class="float-end d-none d-md-block"><?= $cnt->getMenuItemLinkElement() ?></li>
                        <?php
                    }
                }
                ?>
                <li data-guide-toolbar-action="help" class="float-end d-none d-md-block">
                    <a <?php if ($show_tooltips) { ?>class="launch-tooltip"<?php } ?> data-bs-toggle="tooltip"
                       data-bs-placement="bottom" href="#"
                       data-panel-url="<?= URL::to('/ccm/system/panels/help') ?>"
                       title="<?= t('View help about the CMS.') ?>" data-launch-panel="help">
                        <svg><use xlink:href="#icon-help" /></svg><span
                                class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-add-page"><?= tc('toolbar', 'Help') ?></span>
                    </a>
                </li>
                <li data-guide-toolbar-action="intelligent-search" class="ccm-toolbar-search float-end d-none d-lg-block">
                    <?php
                    $menu = Element::get('navigation/intelligent_search');
                    $menu->render();
                    ?>
                </li>
            </ul>
        </div>
        <?php

        if ($pageInUseBySomeoneElse) {
            $buttons = [];
            if ($canApprovePageVersions) {
                $buttons[] = '<a onclick="' . h('$.get(CCM_DISPATCHER_FILENAME + "/ccm/system/backend/dashboard/sitemap_check_in?cID=' . $c->getCollectionID() . $token . '", function() { window.location.reload(); })') . '" href="javascript:void(0)" class="btn btn-secondary">' . t('Force Exit Edit Mode') . '</a>';
            }

            echo $cih->notify([
                'title' => t('Editing Unavailable.'),
                'text' => t('%s is currently editing this page.', $c->getCollectionCheckedOutUserName()),
                'type' => 'info',
                'icon' => 'fas fa-exclamation-circle',
                'buttons' => $buttons,
            ]);
        } else {
            if ($c->getCollectionPointerID() > 0) {
                $buttons = [];
                $buttons[] = \HtmlObject\Link::create(
                    DIR_REL . '/' . DISPATCHER_FILENAME . '?cID=' . $cID,
                    t('View/Edit Original'),
                    ['class' => 'btn btn-secondary btn-sm']
                );
                if ($canApprovePageVersions) {
                    $url = URL::to('/ccm/system/dialogs/page/delete_alias?cID=' . $c->getCollectionPointerOriginalID());
                    $buttons[] = \HtmlObject\Link::create(
                        $url,
                        t('Remove Alias'),
                        [
                            'class' => 'dialog-launch btn btn-sm btn-danger',
                            'dialog-title' => t('Remove Alias')
                        ]
                    );
                }
                echo $cih->notify([
                    'title' => t('Page Alias.'),
                    'text' => t('This page is an alias of one that actually appears elsewhere.'),
                    'type' => 'info',
                    'icon' => 'fas fa-info-circle',
                    'buttons' => $buttons,
                ]);
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
                            echo $cih->notify([
                                'title' => t('Page Draft.'),
                                'text' => t('This is an un-published draft.'),
                                'type' => 'info',
                                'icon' => 'fas fa-exclamation-circle',
                            ]);
                        } else {
                            $buttons = [];
                            if ($canApprovePageVersions && !$c->isCheckedOut()) {
                                $pk = \Concrete\Core\Permission\Key\PageKey::getByHandle('approve_page_versions');
                                $pk->setPermissionObject($c);
                                $pa = $pk->getPermissionAccessObject();
                                $workflows = [];
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
                                $buttons[] = '<a href="' . h($resolver->resolve(["/ccm/system/page/approve_recent/{$cID}/" . $valt->generate()])) . '" class="btn btn-primary">' . $appLabel . '</a>';
                            }
                            echo $cih->notify([
                                'title' => t('Page is Pending Approval.'),
                                'text' => t('This page is newer than what appears to visitors on your live site.'),
                                'type' => 'info',
                                'icon' => 'fas fa-cog',
                                'buttons' => $buttons,
                            ]);
                        }
                    } else {
                        $publishDate = $vo->getPublishDate();
                        if ($publishDate && $dateHelper->toDateTime() < $dateHelper->toDateTime($publishDate)) {
                            $date = $dateHelper->formatDate($publishDate);
                            $time = $dateHelper->formatTime($publishDate);
                            $message = t(/*i18n: %1$s is a date, %2$s is a time */'This version of the page is scheduled to be published on %1$s at %2$s.', $date, $time);
                            $buttons = [];
                            if ($canApprovePageVersions && !$c->isCheckedOut()) {
                                $button1 = new \HtmlObject\Link($resolver->resolve(["/ccm/system/page/publish_now", $cID, $valt->generate()]), t('Publish Now'));
                                $button2 = new \HtmlObject\Link($resolver->resolve(["/ccm/system/page/cancel_schedule", $cID, $valt->generate()]), t('Cancel Scheduled Publish'));
                                $buttons[] = $button1;
                                $buttons[] = $button2;
                            }
                            echo $cih->notify([
                                'title' => t('Publish Pending.'),
                                'text' => $message,
                                'type' => 'info',
                                'icon' => 'fas fa-cog',
                                'buttons' => $buttons,
                            ]);
                        }
                    }
                }
            }
        }
    ?>
    </div>

    <?php
}
