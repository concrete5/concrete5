<?php
defined('C5_EXECUTE') or die("Access Denied.");

/**
 * @var $listView \Concrete\Core\Notification\View\StandardListViewInterface
 */

if ($listView->getMenu() instanceof \Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu) {

?>

    <button type="button" class="btn btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
        <i class="fa fa-chevron-down"></i>
    </button>

    <?php print $listView->getMenu()->getMenuElement() ?>


<?php } else { ?>

    <button type="button" data-notification-action="archive" class="btn btn-secondary btn-waiting-for-me-archive">
        <svg><use xlink:href="#icon-dialog-close" /></svg>
    </button>

<?php } ?>
