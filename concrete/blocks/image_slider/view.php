<?php defined('C5_EXECUTE') or die('Access Denied.');
$navigationTypeText = (0 == $navigationType) ? 'arrows' : 'pages';
$c = Page::getCurrentPage();
if ($c->isEditMode()) {
    $loc = Localization::getInstance();
    $loc->pushActiveContext(Localization::CONTEXT_UI); ?>
    <div class="ccm-edit-mode-disabled-item" style="<?php echo isset($width) ? "width: $width;" : ''; ?><?php echo isset($height) ? "height: $height;" : ''; ?>">
        <i style="font-size:40px; margin-bottom:20px; display:block;" class="fa fa-picture-o" aria-hidden="true"></i>
        <div style="padding: 40px 0px 40px 0px"><?php echo t('Image Slider disabled in edit mode.'); ?>
			<div style="margin-top: 15px; font-size:9px;">
				<i class="fa fa-circle" aria-hidden="true"></i>
				<?php if (count($rows) > 0) {
        ?>
					<?php foreach (array_slice($rows, 1) as $row) {
            ?>
						<i class="fa fa-circle-thin" aria-hidden="true"></i>
						<?php
        }
    } ?>
			</div>
        </div>
    </div>
    <?php
    $loc->popActiveContext();
} else {
    ?>
<script>
$(document).ready(function(){
    $(function () {
        $("#ccm-image-slider-<?php echo $bID; ?>").responsiveSlides({
            prevText: "",   // String: Text for the "previous" button
            nextText: "",
		<?php if (0 == $navigationType) {
        ?>
		nav:true,
		<?php
    } elseif (1 == $navigationType) {
        ?> 
		pager: true,
		<?php
    } elseif (2 == $navigationType) {
        ?>
        nav: true,
        pager: true,
        <?php
    } else {
        ?>
		nav:false,
		pager: false,
		<?php
    } ?>
            <?php if ($timeout) {
        echo "timeout: $timeout,";
    } ?>
            <?php if ($speed) {
        echo "speed: $speed,";
    } ?>
            <?php if ($pause) {
        echo "pause: true,";
    } ?>
            <?php if ($noAnimate) {
        echo "auto: false,";
    } ?>
            <?php if ($maxWidth) {
        echo "maxwidth: $maxWidth,";
    } ?>
        });
    });
});
</script>

<div class="ccm-image-slider-container ccm-block-image-slider-<?=$navigationTypeText; ?>" >
    <div class="ccm-image-slider">
        <div class="ccm-image-slider-inner">

        <?php if (count($rows) > 0) {
        ?>
        <ul class="rslides" id="ccm-image-slider-<?php echo $bID; ?>">
            <?php foreach ($rows as $row) {
            ?>
                <li>
                <?php if ($row['linkURL']) {
                ?>
                    <a href="<?php echo $row['linkURL']; ?>" class="mega-link-overlay"></a>
                <?php
            } ?>
                <?php
                $f = File::getByID($row['fID']); ?>
                <?php if (is_object($f)) {
                    $tag = Core::make('html/image', [$f, false])->getTag();
                    if ($row['title']) {
                        $tag->alt($row['title']);
                    } else {
                        $tag->alt("slide");
                    }
                    echo $tag; ?>
                <?php
                } ?>
                <div class="ccm-image-slider-text">
                    <?php if ($row['title']) {
                    ?>
                    	<h2 class="ccm-image-slider-title"><?php echo $row['title']; ?></h2>
                    <?php
                } ?>
                    <?php echo $row['description']; ?>
                </div>
                </li>
            <?php
        } ?>
        </ul>
        <?php
    } else {
        ?>
        <div class="ccm-image-slider-placeholder">
            <p><?php echo t('No Slides Entered.'); ?></p>
        </div>
        <?php
    } ?>
        </div>

    </div>
</div>
<?php
} ?>
