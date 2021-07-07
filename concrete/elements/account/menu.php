<?php
use Concrete\Core\Page\Desktop\DesktopList;
use Concrete\Core\Support\Facade\Application;

defined('C5_EXECUTE') or die('Access Denied.');

$app = Application::getFacadeApplication();

$u = $app->make(Concrete\Core\User\User::class);
if (!$u->isRegistered()) {
    return;
}

$ui = $u->getUserInfoObject();

$account = Page::getByPath('/account');
if (!is_object($account) || $account->isError()) {
    return;
}

$desktop = DesktopList::getMyDesktop();
if (!is_object($desktop) || $desktop->isError()) {
    return;
}
$cp = new Permissions($desktop);
if (!$cp->canRead()) {
    return;
}
$url = $app->make('url/manager');
?>
<div style="display: none">
    <div class="btn-group" id="ccm-account-menu">
        <a class="btn btn-secondary" href="<?=$desktop->getCollectionLink()?>"><i class="fa fa-user"></i> <?=$ui->getUserDisplayName()?></a>
        <button class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown" role="button" aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu float-end" role="menu">
            <li><a href="<?=$url->resolve([$desktop])?>"><?=t('My Account')?></a></li>
            <li class="divider"></li>
            <?php
                $categories = [];
                $children = $account->getCollectionChildrenArray(true);
                foreach ($children as $cID) {
                    $nc = Page::getByID($cID, 'ACTIVE');
                    $ncp = new Permissions($nc);
                    if ($ncp->canRead() && (!$nc->getAttribute('exclude_nav'))) {
                        $categories[] = $nc;
                    }
                }
                foreach ($categories as $cc) {
                    ?><li><a href="<?=$url->resolve([$cc])?>"><?=h(t($cc->getCollectionName()))?></a></li><?php
                }
            ?>
            <li class="divider"></li>
            <li><a href="<?=$url->resolve(['/'])?>"><i class="fa fa-home"></i> <?=t('Home')?></a></li>
            <li><a href="<?=$url->resolve(['/login', 'do_logout', $app->make('token')->generate('do_logout')])?>"><i class="fa fa-sign-out"></i> <?=t('Sign Out')?></a></li>
        </ul>
    </div>
</div>

