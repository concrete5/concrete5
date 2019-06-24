<?php defined('C5_EXECUTE') or die('Access Denied.');

$navItems = $controller->getNavItems(true); // Ignore exclude from nav
$c = Page::getCurrentPage();

if (count($navItems) > 0) {
    echo '<nav role="navigation" aria-label="breadcrumb">'; //opens the top-level menu
    echo '<ol class="breadcrumb">';

    foreach ($navItems as $ni) {
        if ($ni->isCurrent) {
            echo '<li class="active">' . $ni->name . '</li>';
        } else {
            echo '<li><a href="' . $ni->url . '" target="' . $ni->target . '">' . $ni->name . '</a></li>';
        }
    }

    echo '</ol>';
    echo '</nav>'; //closes the top-level menu
} elseif (is_object($c) && $c->isEditMode()) {
    ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Auto-Nav Block.')?></div>
<?php 
}
