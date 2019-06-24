<?php  defined('C5_EXECUTE') or die('Access Denied.');
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
$page = Page::getCurrentPage();
$date = $dh->formatDate($page->getCollectionDatePublic(), true);
$user = UserInfo::getByID($page->getCollectionUserID());
?>
<div class="ccm-block-page-title-byline">
    <h1 class="page-title"><?=h($title)?></h1>

    <span class="page-date">
    <?php echo $date; ?>
    </span>

    <?php if (is_object($user)): ?>
    <span class="page-author">
    <?php echo $user->getUserDisplayName(); ?>
    </span>
    <?php endif; ?>
</div>
