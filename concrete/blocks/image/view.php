<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\Html\Image;
use Concrete\Core\Html\Object\Picture;
use Concrete\Core\Support\Facade\Application;
use HtmlObject\Element;
use HtmlObject\Image as HtmlImage;

$app = Application::getFacadeApplication();

/** @var array $themeResponsiveImageMap */
/** @var array $selectedThumbnailTypes */
/** @var string $altText */
/** @var bool $openLinkInNewWindow */
/** @var string $linkURL */
/** @var bool $cropImage */
/** @var int $maxWidth */
/** @var int $maxHeight */
/** @var Version $f */
/** @var Version $foS */
/** @var string $sizingOption */
/** @var array $imgPaths */

if (is_object($f) && $f->getFileID()) {
    $imageTag = new HtmlImage();

    if ($f->getTypeObject()->isSVG()) {
        $imageTag->setAttribute("src", $f->getRelativePath());

        if ($maxWidth > 0) {
            $imageTag->setAttribute("width", $maxWidth);
        }

        if ($maxHeight > 0) {
            $imageTag->setAttribute("height", $maxHeight);
        }

        $imageTag->addClass('ccm-svg');
    } else {
        switch ($sizingOption) {
            case "thumbnails_default":
                /** @var Image $image */
                $image = $app->make('html/image', [$f]);
                $imageTag = $image->getTag();
                break;

            case "thumbnails_configurable":
                $sources = [];

                $fallbackSrc = $f->getRelativePath();

                if (!$fallbackSrc) {
                    $fallbackSrc = $f->getURL();
                }

                foreach ($selectedThumbnailTypes as $breakpointHandle => $ftTypeID) {
                    $type = Type::getByID($ftTypeID);

                    $width = 0;

                    foreach ($themeResponsiveImageMap as $themeBreakpointHandle => $themeWidth) {
                        if ($breakpointHandle == $themeBreakpointHandle) {
                            $width = $themeWidth;
                            break;
                        }
                    }

                    if ($type instanceof \Concrete\Core\Entity\File\Image\Thumbnail\Type\Type && $width > 0) {
                        $src = $f->getThumbnailURL($type->getBaseVersion());

                        $sources[] = ['src' => $src, 'width' => $width];
                    }
                }

                $imageTag = Picture::create($sources, $fallbackSrc);

                break;

            case "full_size":
                $imageTag->setAttribute("src", $f->getRelativePath());
                break;

            case "constrain_size":
                /** @var BasicThumbnailer $im */
                $im = $app->make('helper/image');

                $thumb = $im->getThumbnail($f, $maxWidth, $maxHeight, $cropImage);

                $imageTag->setAttribute("src", $thumb->src);
                $imageTag->setAttribute("width", $thumb->width);
                $imageTag->setAttribute("height", $thumb->height);

                break;
        }
    }

    $imageTag->addClass('ccm-image-block img-fluid bID-' . $bID);

    if ($altText) {
        $imageTag->alt(h($altText));
    } else {
        $imageTag->alt('');
    }

    if ($title) {
        $imageTag->title(h($title));
    }

    // add data attributes for hover effect
    if (is_object($foS) && !$f->getTypeObject()->isSVG() && !$foS->getTypeObject()->isSVG()) {
        $imageTag->addClass('ccm-image-block-hover');
        $imageTag->setAttribute('data-default-src', $imgPaths['default']);
        $imageTag->setAttribute('data-hover-src', $imgPaths['hover']);
    }

    if ($linkURL) {
        $linkTag = new Element("a");
        $linkTag->setAttribute("href", $linkURL);
        $linkTag->setChild($imageTag);

        if ($openLinkInNewWindow) {
            $linkTag->setAttribute("target", "_blank");
            $linkTag->setAttribute("rel", "noopener noreferrer");
        }

        echo $linkTag;
    } else {
        echo $imageTag;
    }

} elseif ($c->isEditMode()) { ?>
    <div class="ccm-edit-mode-disabled-item">
        <?php echo t('Empty Image Block.'); ?>
    </div>
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
<?php }
