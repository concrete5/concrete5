<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Support\Facade\Application;
use Concrete\Theme\Concrete\PageTheme;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use HtmlObject\Element;
use HtmlObject\Image;

$app = Application::getFacadeApplication();

/** @var Type $thumbnailTypeService */
$thumbnailTypeService = $app->make(Type::class);

/** @var int $maxWidth */
/** @var int $maxHeight */
/** @var int $cropImage */
/** @var string $altText */
/** @var string $linkURL */
/** @var bool $hasImageLink */
/** @var bool $openLinkInNewWindow */
/** @var bool $openLinkInLightbox */
/** @var Version $foS */
/** @var Version $f */
/** @var array $imgPaths */
/** @var array $thumbnails */

if (is_object($f) && $f->getFileID()) {
    $tag = new Image();

    if ($f->getTypeObject()->isSVG()) {
        $tag->setAttribute("src", $f->getRelativePath());

        if ($maxWidth > 0) {
            $tag->setAttribute("width", $maxWidth);
        }

        if ($maxHeight > 0) {
            $tag->setAttribute("height", $maxHeight);
        }

        $tag->addClass('ccm-svg');
    } elseif (count($thumbnails) > 0) {
        $siteTheme = PageTheme::getSiteTheme();
        $responsiveImageMap = $siteTheme->getThemeResponsiveImageMap();

        arsort($responsiveImageMap);

        $sources = [];

        $fallbackSrc = $f->getApprovedVersion()->getURL();

        foreach ($responsiveImageMap as $breakpointHandle => $breakpointSize) {
            $thumbnailTypeId = $thumbnails[$breakpointHandle];
            $thumbnailType = $thumbnailTypeService::getByID($thumbnailTypeId);

            if ($thumbnailType instanceof \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type) {
                $thumbnailUrl = $f->getApprovedVersion()->getThumbnailURL($thumbnailType->getBaseVersion());

                if (!is_null($thumbnailUrl) && (int)$breakpointSize > 0) {
                    $sources[] = ["width" => $breakpointSize, "src" => $thumbnailUrl];
                }
            }
        }


        $tag = Picture::create($sources, $fallbackSrc);
    } elseif ($maxWidth > 0 || $maxHeight > 0) {
        /** @var BasicThumbnailer $im */
        $im = $app->make(BasicThumbnailer::class);
        $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $cropImage);
        $tag->setAttribute("src", $thumb->src);
        $tag->setAttribute("width", $thumb->width);
        $tag->setAttribute("height", $thumb->height);
    } else {
        /** @var \Concrete\Core\Html\Image $image */
        $image = $app->make(\Concrete\Core\Html\Image::class, [$f]);
        $tag = $image->getTag();
    }

    $tag->addClass('ccm-image-block img-fluid bID-' . $bID);

    if ($altText) {
        $tag->setAttribute("alt", h($altText));
    } else {
        $tag->setAttribute('alt', '');
    }

    if ($title) {
        $tag->setAttribute("title", h($title));
    }

    // add data attributes for hover effect
    if (is_object($foS) && !$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
        $tag->addClass('ccm-image-block-hover');
        $tag->setAttribute('data-default-src', $imgPaths['default']);
        $tag->setAttribute('data-hover-src', $imgPaths['hover']);
    }

    if (strlen($linkURL) > 0) {
        $a = new Element("a");

        $a->addClass('ccm-image-block-link bID-' . $bID);
        $a->setAttribute("href", $linkURL);

        if ($openLinkInNewWindow) {
            $a->setAttribute("target", "_blank");
            $a->setAttribute("rel", "noopener noreferrer");
        }

        $a->setChild($tag);

        echo $a;
    } else {
        echo $tag;
    }

} elseif ($c->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item">
        <?php echo t('Empty Image Block.'); ?>
    </div>
<?php } ?>

<?php if ($openLinkInLightbox) { ?>
    <script>
        $(function () {
            $('.ccm-image-block-link.bID-<?php echo $bID ?> ').magnificPopup({
                type: '<?php echo $hasImageLink ? "image" : "iframe"; ?>',
                gallery: {
                    enabled: true
                }
            });
        })
    </script>
<?php } ?>

<?php if (isset($foS) && $foS) { ?>
    <script type="text/javascript">
        var images = document.getElementsByClassName('ccm-image-block-hover');

        for (var i = 0; i < images.length; i++) {
            var image = images[i],
                hoverSrc = image.getAttribute('data-hover-src'),
                defaultSrc = image.getAttribute('data-default-src');
            image.onmouseover = function () {
                image.setAttribute('src', hoverSrc);
            };
            image.onmouseout = function () {
                image.setAttribute('src', defaultSrc);
            };
        }
    </script>
<?php } ?>