<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $listView \Concrete\Core\Notification\View\StandardListViewInterface
 */

if ($listView->getMenu() instanceof \Concrete\Core\Application\UserInterface\ContextMenu\Menu) {

?>

    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
        <i class="fa fa-chevron-down"></i>
    </button>

    <?php print $listView->getMenu()->getMenuElement() ?>


<?php } else { ?>

    <button type="button" data-notification-action="archive" class="btn btn-default btn-waiting-for-me-archive">
        <i></i>
    </button>

<?php } ?>