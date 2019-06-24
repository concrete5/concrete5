<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var $listView \Concrete\Core\Notification\View\StandardListViewInterface
 */

?>

    <div class="ccm-block-desktop-waiting-for-me-description">
        <?php print $listView->getActionDescription() ?>
    </div>

    <?php
    $author = $listView->getInitiatorUserObject();
?>
    <div class="ccm-block-desktop-waiting-for-me-about">

        <?php print $listView->renderInitiatorActionDescription() ?>

        <?php print $listView->renderInitiatorCommentDescription() ?>

    </div>
