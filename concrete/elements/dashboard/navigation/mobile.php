<?php defined('C5_EXECUTE') or die('Access Denied.');
/**
 * @var $navigation \Concrete\Core\Application\UserInterface\Dashboard\Navigation\Navigation
 */
?>
<?php
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface;
?>
<li class="float-end ccm-toolbar-mobile-menu-button d-block d-md-none">
  <i class="fa fa-bars"></i>
</li>

<?php
$walkNavigation = function(array $items) use (&$walkNavigation) {
    if (count($items)) { ?>
        <ul class="nav flex-column ccm-mobile-menu">
            <?php foreach($items as $item) { ?>
                <li 
                  <?php if ($item->isActiveParent()) {?> class="nav-path-selected"<?php } ?>
                  <?php if ($item->isActive()) {?> class="nav-selected"<?php } ?>
                  >
                    <a href="<?=$item->getURL()?>"
                    <?php if ($item->isActive()) { ?>class="ccm-panel-menu-item-active nav-selected nav-path-selected"<?php } ?>
                    <?php if ($item->isActiveParent()) { ?>class="ccm-panel-menu-parent-item-active"<?php } ?>>
                        <?=$item->getName()?>
                    </a>
                    <?php if ($item->getChildren()) {?>
                    <i class="fa drop-down-toggle fa-caret-down"></i>
                    <?php }?>
                    <?php $walkNavigation($item->getChildren());?>
                </li>
            <?php } ?>
        </ul>
    <?php }
}
?>


<?php 
$c = Page::getCurrentPage();
$permissions = new Permissions($c);
$app = Concrete\Core\Support\Facade\Facade::getFacadeApplication();


$dh = $app->make('helper/concrete/dashboard');
$valt = $app->make('helper/validation/token');
$vo = $c->getVersionObject();
if (!$dh->inDashboard()) {
    $resolver = $app->make(ResolverManagerInterface::class);
    $c = Page::getCurrentPage();
    $cID = $c->getCollectionID();
}
     
?>
            <div class="ccm-mobile-menu-overlay d-md-none" style="height: calc(100vh - 48px)">
                <div class="ccm-mobile-menu-main">
                    <ul class="ccm-mobile-menu-entries">
                    <?php if (!$dh->inDashboard()) {?>


                        <?php
                        if (!$pageInUseBySomeoneElse && $c->getCollectionPointerID() == 0) {
                            if ($c->isEditMode()) {
                                ?>
                                <li class="ccm-toolbar-page-edit-mode-active ccm-toolbar-page-edit">
                                    <i class="fas fa-pencil-alt mobile-leading-icon"></i>
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
                                    <i class="fas fa-pencil-alt mobile-leading-icon"></i>
                                    <a
                                        <?php if ($c->isMasterCollection()) { ?>data-disable-panel="check-in"<?php } ?>
                                        data-toolbar-action="check-out"
                                        href="<?= h($resolver->resolve(["/ccm/system/page/checkout/{$cID}/-/" . $valt->generate()])) ?>"
                                    ><?php echo t('Edit This Page') ?></a>
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
                                    if (is_object($pagetype) && $permissions->canEditPageContents()) {
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
                            }?>
                            <?php
                        }?>
                        <?php // dashboard menu ?>
                        <li class="parent-ul">
                          <i class="fa fa-sliders-h mobile-leading-icon"></i>
                          <a href="<?= URL::to('/dashboard');?>"><?= t('Dashboard');?></a>
                          <i class="fa fa-caret-down drop-down-toggle"></i>
                          <?php $walkNavigation($navigation->getItems());?>
                        </li>
                        <li>
                            <i class="fa fa-sign-out-alt mobile-leading-icon"></i>
                            <a href="<?= URL::to('/login', 'do_logout', $valt->generate('do_logout')); ?>"><?= t('Sign Out'); ?></a>
                        </li>
                    </ul>
                </div>
            </div>
