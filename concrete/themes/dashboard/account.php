<?php

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Page\Page;
use Concrete\Core\View\View;

/** @var View $view */
/** @var Page $c */
/** @var string $innerContent */

/** @noinspection PhpUnhandledExceptionInspection */
$view->inc('elements/header.php', ['bodyClass' => 'ccm-dashboard-account']);
?>

<div id="ccm-dashboard-content-regular">
    <div class="ccm-dashboard-desktop-content">
        <nav class="ccm-dashboard-desktop-navbar navbar navbar-dark navbar-expand-md">
            <span class="navbar-text">
                <?php echo $c->getCollectionName() ?>
            </span>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarWelcomeBack"
                    aria-controls="navbarWelcomeBack" aria-expanded="false"
                    aria-label="<?= h(t('Toggle navigation')) ?>">
                <span class="navbar-toggler-icon"></span>
            </button>
        </nav>

        <?php print $innerContent; ?>
    </div>
</div>

<!--suppress CssUnusedSymbol -->
<style>
    .ccm-dashboard-account div#ccm-dashboard-content div#ccm-dashboard-content-regular {
        padding-top: 0;
    }
</style>
<?php /** @noinspection PhpUnhandledExceptionInspection */
$view->inc('elements/footer.php'); ?>
