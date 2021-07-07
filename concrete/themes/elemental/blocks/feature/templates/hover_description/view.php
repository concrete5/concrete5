<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php if ($linkURL) {
    ?>
    <a href="<?=$linkURL?>">
<?php 
} ?>
<div class="ccm-block-feature-item-hover-wrapper" data-bs-toggle="tooltip" data-bs-placement="bottom" title="<?=h(strip_tags($paragraph))?>">
    <div class="ccm-block-feature-item-hover">
        <div class="ccm-block-feature-item-hover-icon"><i class="fa fa-<?=$icon?>"></i></div>
    </div>
    <div class="ccm-block-feature-item-hover-title"><?=h($title)?></div>
</div>

<?php if ($linkURL) {
    ?>
    </a>
<?php 
} ?>
