<?php
use Concrete\Core\Cookie\ResponseCookieJar;
use Concrete\Core\Url\SeoCanonical;
use Concrete\Core\User\User;
use Concrete\Core\Localization\Localization;
use Concrete\Core\Multilingual\Page\Section\Section;
use Concrete\Core\Support\Facade\Application;
use Symfony\Component\EventDispatcher\GenericEvent;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Arguments:
 *
 * @var string|null $pageTitle
 * @var string|null $pageDescription
 * @var string|null $pageMetaKeywords
 * @var bool|null $disableTrackingCode
 */

$c = Page::getCurrentPage();
$cp = false;
$isEditMode = false;
$isArrangeMode = false;
$scc = false;
if (!isset($pageTitle) || !is_string($pageTitle) || $pageTitle === '') {
    $pageTitle = null;
}
if (!isset($pageDescription)) {
    $pageDescription = null;
}
if (!isset($pageMetaKeywords)) {
    $pageMetaKeywords = null;
}
$defaultPageTitle = $pageTitle;
$app = Application::getFacadeApplication();
$site = $app->make('site')->getSite();
$config = $site->getConfigRepository();
$appConfig = $app->make('config');

if (is_object($c)) {
    $cp = new Permissions($c);
    $cID = $c->getCollectionID();
    $isEditMode = $c->isEditMode();
    $isArrangeMode = $c->isArrangeMode();
    $styleObject = false;

    /*
     * Handle page title
     *
     * We can set a title 3 ways:
     * 1. It comes through programmatically as $pageTitle. If this is the case then we pass it through, no questions asked
     * 2. It comes from meta title
     * 3. It comes from getCollectionName()
     * In the case of 3, we also pass it through page title format.
     */
    if ($pageTitle === null) {
        // we aren't getting it dynamically.
        $pageTitle = $c->getAttribute('meta_title');
        if (!is_string($pageTitle) || $pageTitle === '') {
            $seo = $app->make('helper/seo');
            if (!$seo->hasCustomTitle()) {
                $pageTitle = $c->getCollectionName();
                if ($c->isSystemPage()) {
                    $pageTitle = t($pageTitle);
                }
                $seo->addTitleSegmentBefore($pageTitle);
            }
            $seo->setSiteName(tc('SiteName', $site->getSiteName()));
            $seo->setTitleFormat($appConfig->get('concrete.seo.title_format'));
            $seo->setTitleSegmentSeparator($appConfig->get('concrete.seo.title_segment_separator'));
            $pageTitle = $seo->getTitle();
        }
    }

    if (!$pageDescription) {
        // we aren't getting it dynamically.
        $pageDescription = $c->getAttribute('meta_description');
        if (!$pageDescription) {
            $pageDescription = $c->getCollectionDescription();
        }
        $pageDescription = trim($pageDescription);
    }
    if (!$pageMetaKeywords) {
        $pageMetaKeywords = trim($c->getAttribute('meta_keywords'));
    }
    if ($c->hasPageThemeCustomizations()) {
        $styleObject = $c->getCustomStyleObject();
    } elseif (($pt = $c->getCollectionThemeObject()) && is_object($pt)) {
        $styleObject = $pt->getThemeCustomStyleObject();
    }
    if (isset($styleObject) && is_object($styleObject)) {
        $scc = $styleObject->getCustomCssRecord();
    }
} else {
    $cID = 1;
    $c = null;
}
$metaTags = [];
$metaTags['charset'] = sprintf('<meta http-equiv="content-type" content="text/html; charset=%s"/>', APP_CHARSET);
if ($pageDescription) {
    $metaTags['description'] = sprintf('<meta name="description" content="%s"/>', htmlspecialchars($pageDescription, ENT_COMPAT, APP_CHARSET));
}
if ($pageMetaKeywords) {
    $metaTags['keywords'] = sprintf('<meta name="keywords" content="%s"/>', htmlspecialchars($pageMetaKeywords, ENT_COMPAT, APP_CHARSET));
}
if ($c !== null && $c->getAttribute('exclude_search_index')) {
    $metaTags['robots'] = sprintf('<meta name="robots" content="%s"/>', 'noindex');
}
if ($appConfig->get('concrete.misc.generator_tag_display_in_header')) {
    $metaTags['generator'] = sprintf('<meta name="generator" content="%s"/>', 'concrete5');
}
if (($modernIconFID = (int) $config->get('misc.modern_tile_thumbnail_fid')) && ($modernIconFile = File::getByID($modernIconFID))) {
    $metaTags['msapplication-TileImage'] = sprintf('<meta name="msapplication-TileImage" content="%s"/>', $modernIconFile->getURL());
    $modernIconBGColor = (string) $config->get('misc.modern_tile_thumbnail_bgcolor');
    if ($modernIconBGColor !== '') {
        $metaTags['msapplication-TileColor'] = sprintf('<meta name="msapplication-TileColor" content="%s"/>', $modernIconBGColor);
    }
}
$linkTags = [];
if (($favIconFID = (int) $config->get('misc.favicon_fid')) && ($favIconFile = File::getByID($favIconFID))) {
    $favIconFileURL = $favIconFile->getURL();
    $linkTags['shortcut icon'] = sprintf('<link rel="shortcut icon" href="%s" type="image/x-icon"/>', $favIconFileURL);
    $linkTags['icon'] = sprintf('<link rel="icon" href="%s" type="image/x-icon"/>', $favIconFileURL);
}
if (($appleIconFID = (int) $config->get('misc.iphone_home_screen_thumbnail_fid')) && ($appleIconFile = File::getByID($appleIconFID))) {
    $linkTags['apple-touch-icon'] = sprintf('<link rel="apple-touch-icon" href="%s"/>', $appleIconFile->getURL());
}
$browserToolbarColor = (string) $config->get('misc.browser_toolbar_color');
if ($browserToolbarColor !== '') {
    $metaTags['browserToolbarColor'] = sprintf('<meta name="theme-color" content="%s"/>', h($browserToolbarColor));
}
if ($config->get('seo.canonical_tag.enabled')) {
    if (($canonicalLink = $app->make(SeoCanonical::class)->getPageCanonicalURLTag($c, Request::getInstance())) !== null) {
        $linkTags['canonical'] = (string) $canonicalLink;
    }
}
$alternateHreflangTags = [];
if ($c !== null && $config->get('multilingual.set_alternate_hreflang') && !$c->isAdminArea() && $app->make('multilingual/detector')->isEnabled()) {
    $multilingualSection = Section::getBySectionOfSite($c);
    if ($multilingualSection) {
        $urlManager = $app->make('url/manager');
        foreach (Section::getList($site) as $ms) {
            $relatedID = $ms->getTranslatedPageID($c);
            if ($relatedID) {
                $relatedPage = Page::getByID($relatedID);
                if ($relatedPage && !$relatedPage->isError()) {
                    $url = $urlManager->resolve([$relatedPage]);
                    $alternateHreflangTags[] = '<link rel="alternate" hreflang="'.str_replace('_', '-', $ms->getLocale()).'" href="'.$url.'" />';
                }
            }
        }
    }
}

// Generate and dispatch an event, to let other Add-Ons make use of the available (meta) tags/page title
$event = new GenericEvent();
$event->setArgument('metaTags', $metaTags);
$event->setArgument('linkTags', $linkTags);
$event->setArgument('pageTitle', $pageTitle);
$event->setArgument('defaultPageTitle', $defaultPageTitle);
$event->setArgument('alternateHreflangTags', $alternateHreflangTags);
$app->make('director')->dispatch('on_header_required_ready', $event);
$metaTags = $event->getArgument('metaTags');
$linkTags = $event->getArgument('linkTags');
$pageTitle = $event->getArgument('pageTitle');
$alternateHreflangTags = $event->getArgument('alternateHreflangTags');
?>

<title><?php echo htmlspecialchars($pageTitle, ENT_COMPAT, APP_CHARSET); ?></title>

<?php
echo implode(PHP_EOL, $metaTags).PHP_EOL;
if (!empty($linkTags)) {
    echo implode(PHP_EOL, $linkTags).PHP_EOL;
}
if (!empty($alternateHreflangTags)) {
    echo implode(PHP_EOL, $alternateHreflangTags).PHP_EOL;
}
?>
<script type="text/javascript">
    var CCM_DISPATCHER_FILENAME = <?= json_encode(DIR_REL . '/' . DISPATCHER_FILENAME, JSON_UNESCAPED_SLASHES) ?>;
    var CCM_CID = <?= (int) $cID ?>;
    var CCM_EDIT_MODE = <?= $isEditMode ? 'true' : 'false' ?>;
    var CCM_ARRANGE_MODE = <?= $isArrangeMode ? 'true' : 'false' ?>;
    var CCM_IMAGE_PATH = <?= json_encode(ASSETS_URL_IMAGES, JSON_UNESCAPED_SLASHES) ?>;
    var CCM_TOOLS_PATH = <?= json_encode(REL_DIR_FILES_TOOLS_REQUIRED, JSON_UNESCAPED_SLASHES) ?>;
    var CCM_APPLICATION_URL = <?= json_encode(rtrim((string) $app->make('url/canonical'), '/'), JSON_UNESCAPED_SLASHES) ?>;
    var CCM_REL = <?= json_encode((string) $app->make('app_relative_path'), JSON_UNESCAPED_SLASHES) ?>;
    var CCM_ACTIVE_LOCALE = <?= json_encode(Localization::activeLocale(), JSON_UNESCAPED_SLASHES) ?>;
    var CCM_USER_REGISTERED = <?= $app->make(User::class)->isRegistered() ? 'true ': 'false' ?>;
</script>

<?php
$v = View::getRequestInstance();
if ($cp) {
    View::element('page_controls_header', ['cp' => $cp, 'c' => $c]);
    if ($isEditMode) {
        $cookie = $app->make('cookie');
        if ($cookie->get('ccmLoadAddBlockWindow')) {
            $v->addFooterItem('<script type="text/javascript">$(function() { setTimeout(function() { $("a[data-launch-panel=add-block]").click()}, 100); });</script>');
            $app->make(ResponseCookieJar::class)->clear('ccmLoadAddBlockWindow');
        }
    }
}
$v->markHeaderAssetPosition();
if (empty($disableTrackingCode)) {
    echo $config->get('seo.tracking.code.header');
}
if ($scc) {
    ?>
    <style type="text/css"><?php echo $scc->getValue(); ?></style>
    <?php
}
if ($c !== null) {
    echo $c->getAttribute('header_extra_content');
}
