<?php
defined('C5_EXECUTE') or die('Access Denied.');
$th = Loader::helper('text');
$c = Page::getCurrentPage();
?>

<div class="ccm-block-page-list-thumbnail-grid-wrapper">

    <?php if (isset($pageListTitle) && $pageListTitle): ?>
        <div class="ccm-block-page-list-header">
            <h5><?=h($pageListTitle)?></h5>
        </div>
    <?php endif; ?>

    <?php foreach ($pages as $page):

        $title = $th->entities($page->getCollectionName());
        $url = $nh->getLinkToCollection($page);
        $target = ($page->getCollectionPointerExternalLink() != '' && $page->openCollectionPointerExternalLinkInNewWindow()) ? '_blank' : $page->getAttribute('nav_target');
        $target = empty($target) ? '_self' : $target;
        $thumbnail = $page->getAttribute('thumbnail');
        $hoverLinkText = $title;
        $description = $page->getCollectionDescription();
        $description = $controller->truncateSummaries ? $th->wordSafeShortText($description, $controller->truncateChars) : $description;
        $description = $th->entities($description);
        if ($useButtonForLink) {
            $hoverLinkText = $buttonLinkText;
        }

        ?>

        <div class="ccm-block-page-list-page-entry-grid-item">

        <?php if (is_object($thumbnail)): ?>
            <div class="ccm-block-page-list-page-entry-grid-thumbnail">
                <a href="<?php echo $url ?>" target="<?php echo $target ?>"><?php
                $img = Core::make('html/image', array($thumbnail));
                $tag = $img->getTag();
                $tag->addClass('img-responsive');
                echo $tag;
                ?>
                    <div class="ccm-block-page-list-page-entry-grid-thumbnail-hover">
                        <div class="ccm-block-page-list-page-entry-grid-thumbnail-title-wrapper">
                        <div class="ccm-block-page-list-page-entry-grid-thumbnail-title">
                            <i class="ccm-block-page-list-page-entry-grid-thumbnail-icon"></i>
                            <?=$hoverLinkText?>
                        </div>
                        </div>
                    </div>
                </a>

                <?php if ($useButtonForLink) {
    ?>
                <div class="ccm-block-page-list-title">
                    <?php echo $title;
    ?>
                </div>
                <?php
} ?>

                <?php if ($includeDate): ?>
                    <div class="ccm-block-page-list-date"><?=$date?></div>
                <?php endif; ?>

                <?php if ($includeDescription): ?>
                    <div class="ccm-block-page-list-description">
                        <?php echo $description ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php endif; ?>

        </div>

	<?php endforeach; ?>

    <?php if (count($pages) == 0): ?>
        <div class="ccm-block-page-list-no-pages"><?=h($noResultsMessage)?></div>
    <?php endif;?>

</div>

<?php if ($showPagination): ?>
    <?php echo $pagination;?>
<?php endif; ?>

<?php if ($c->isEditMode() && $controller->isBlockEmpty()): ?>
    <div class="ccm-edit-mode-disabled-item"><?=t('Empty Page List Block.')?></div>
<?php endif; ?>