<?php
defined('C5_EXECUTE') or die('Access Denied.');
if ($controller->getTask() == 'view_detail') {
    ?>


    <div class="ccm-marketplace-detail-theme-slideshow-wrapper">
        <div class="ccm-marketplace-detail-theme-slideshow">
            <?php
            $screenshots = $item->getSlideshow();
    $detailShots = $item->getScreenshots();
    ?>
            <ul data-slideshow="marketplace-theme">
                <?php foreach ($screenshots as $i => $image) {
    $detail = $detailShots[$i];
    ?>
                    <li><a href="<?=$detail->src?>"><img src="<?=$image->src?>" /></a></li>
                <?php 
}
    ?>
            </ul>
        </div>
        <div class="ccm-marketplace-detail-theme-slideshow-nav">
            <nav>
                <li><a href="#" data-navigation="marketplace-slideshow-previous"><i class="fa fa-chevron-left"></i></a></li>
                <li><a href="#" data-navigation="marketplace-slideshow-next"><i class="fa fa-chevron-right"></i></a></li>
                <li><a href="#" data-launch="marketplace-slideshow-gallery"><i class="fa fa-image"></i></a></li>
            </nav>
        </div>

        <div class="ccm-marketplace-detail-theme-details">
            <h2><?=$item->getName()?></h2>
            <div><i class="<?=$item->getSkillLevelClassName()?>"></i> <?=$item->getSkillLevelDisplayName()?></div>

            <div class="ccm-marketplace-detail-theme-buy">
                <div class="btn-group">
                    <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$item->getMarketplaceItemID()?>})" class="btn btn-price" style="background-color: #1888d3"><?=$item->getDisplayPrice()?></button>
                    <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$item->getMarketplaceItemID()?>})" class="btn btn-description"><?php if ($item->purchaseRequired()) {
    ?><?=t('Purchase')?><?php 
} else {
    ?><?=t('Download')?><?php 
}
    ?></button>
                </div>
            </div>
        </div>
    </div>

    <div class="ccm-marketplace-detail-columns">
        <div class="row">
            <div class="col-md-4">
                <ul class="list-group">
                    <li class="list-group-item"><?=Loader::helper('rating')->outputDisplay($item->getAverageRating())?>
                    <?php if ($item->getTotalRatings() > 0) {
    ?>
                        <a href="<?=$item->getRemoteReviewsURL()?>" target="_blank" class="ccm-marketplace-detail-reviews-link">
                    <?php 
}
    ?>
                    <?=t2('%d review', '%d reviews', $item->getTotalRatings(), $item->getTotalRatings())?>
                    <?php if ($item->getTotalRatings() > 0) {
    ?>
                        </a>
                    <?php 
}
    ?>
                    </li>
                    <?php if ($item->getExampleURL()) {
    ?>
                        <li class="list-group-item"><a href="<?=$item->getExampleURL()?>" target="_blank"><?=t('Live Example')?></a></li>
                    <?php 
}
    ?>
                    <li class="list-group-item"><a href="<?=$item->getRemoteHelpURL()?>" target="_blank"><i class="fa fa-comment"></i> <?=t('Get Help')?></a></li>
                </ul>
            </div>
            <div class="col-md-7 col-md-offset-1">
                <?=$item->getBody()?>
            </div>
        </div>
    </div>

    <script type="text/javascript">
    $(function () {
        $("ul[data-slideshow=marketplace-theme]").responsiveSlides({
            prevText: "",   // String: Text for the "previous" button
            nextText: "",
            nav: true
        });

        $('a[data-navigation=marketplace-slideshow-previous]').on('click', function(e) {
            e.preventDefault();
            $('.rslides_nav.prev').trigger('click');
        });

        $('a[data-navigation=marketplace-slideshow-next]').on('click', function(e) {
            e.preventDefault();
            $('.rslides_nav.next').trigger('click');
        });

        $('a[data-launch=marketplace-slideshow-gallery]').on('click', function(e) {
            e.preventDefault();
            $('ul[data-slideshow=marketplace-theme] li:first-child a').trigger('click');
        });

        $('ul[data-slideshow=marketplace-theme]').magnificPopup({
          delegate: 'a',
          type: 'image',
          closeOnContentClick: false,
          closeBtnInside: false,
          mainClass: 'mfp-zoom-in mfp-img-mobile',
          image: {
            verticalFit: true
          },
          gallery: {
            enabled: true
          },
          callbacks: {
              open: function() {
                $('.mfp-content').addClass('ccm-ui');
              }
          },
          zoom: {
            enabled: true,
            duration: 300,
            opener: function(element) {
              return element.find('img');
            }
          }

        });
    });
    </script>

<?php 
} elseif (count($items)) {
    ?>

    <?php foreach ($items as $mi) {
    ?>

        <div class="ccm-marketplace-list-item">
            <div class="ccm-marketplace-list-item-theme-thumbnail"><a href="<?=$mi->getLocalURL()?>"><?php
                $thumb = $mi->getLargeThumbnail();
    printf('<img src="%s">', $thumb->src);
    ?></a></div>
            <div class="ccm-marketplace-list-item-theme-description">
                <h2><a href="<?=$mi->getLocalURL()?>"><?=$mi->getName()?></a></h2>
                    <p><?=$mi->getDescription()?></p>
                    <a href="<?=$mi->getLocalURL()?>"><?=t('Learn More')?></a>
            </div>
            <div class="ccm-marketplace-list-item-theme-price">
                <div class="btn-group">
                    <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$mi->getMarketplaceItemID()?>})" class="btn btn-price" style="background-color: #1888d3"><?=$mi->getDisplayPrice()?></button>
                    <button onclick="ConcreteMarketplace.purchaseOrDownload({mpID: <?=$mi->getMarketplaceItemID()?>})" class="btn btn-description"><?php if ($mi->purchaseRequired()) { ?><?=t('Purchase')?><?php } else { ?><?=t('Download')?><?php } ?></button>
                </div>
            </div>
        </div>

    <?php 
}
    ?>

    <?=$list->displayPagingV2()?>

<?php 
} else {
    ?>
   <div class="ccm-marketplace-list-item">
   <div class="ccm-marketplace-list-item-theme-description">
    <p><?=t('No themes found.')?></p>
    </div>
    </div>
<?php 
} ?>