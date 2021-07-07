<?php defined('C5_EXECUTE') or die("Access Denied."); ?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="<?=$view->getThemePath()?>/main.css" />
    <meta name="viewport" content="width=device-width, initial-scale=1">

<?php
$showLogo = true;
if (isset($c) && is_object($c)) {
    $cp = new Permissions($c);
    if ($cp->canViewToolbar()) {
        $showLogo = false;
    }

    Loader::element('header_required', array('pageTitle' => isset($pageTitle) ? $pageTitle : ''));
} else {
    $this->markHeaderAssetPosition();
    if (isset($pageTitle)) {
        echo '<title>' . h($pageTitle) . '</title>';
        echo '<script>var CCM_DISPATCHER_FILENAME = "' . DIR_REL . '/' . DISPATCHER_FILENAME . '";</script>';
    }
}

$showAccount = false;
if (Core::isInstalled()) {
$site = Core::make("site")->getSite();
$config = $site->getConfigRepository();
    if (is_object($site) && $config->get('user.profiles_enabled')) {
        $account = Page::getByPath('/account');
        if (is_object($account) && !$account->isError()) {
            $cp = new Permissions($account);
            if ($cp->canRead()) {
                $request = Request::getInstance();
                if ($request->matches('/account*')) {
                    $showAccount = true;
                }
            }
        }
    }
}
?>
</head>
<body class="min-vh-100">

<div class="ccm-ui min-vh-100">

<?php if ($showLogo) {
    ?>
<div id="ccm-toolbar">
    <ul>
        <li class="ccm-logo float-start"><span><?=Loader::helper('concrete/ui')->getToolbarLogoSRC()?></span></li>
        <?php if ($showAccount) {
    ?>
        <li class="float-end">
            <a href="<?=URL::to('/login', 'do_logout', Loader::helper('validation/token')->generate('do_logout'))?>" title="<?=t('Sign Out')?>"><i class="fas fa-sign-out-alt"></i>
            <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                <?= tc('toolbar', 'Sign Out') ?>
            </span>
            </a>
        </li>
        <li class="float-end">
            <a href="<?=URL::to('/')?>">
                <i class="fa fa-home"></i><span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings"><?=tc('toolbar', 'Return to Website') ?></span>
            </a>
        </li>
        <li class="float-end">
            <a href="<?=URL::to('/account')?>">
                <i class="fa fa-user"></i>
                <span class="ccm-toolbar-accessibility-title ccm-toolbar-accessibility-title-site-settings">
                    <?=t('My Account') ?>
                </span>
            </a>
        </li>
        <?php
}
    ?>
    </ul>
</div>
<?php
} ?>
