<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<div class="row mb-3">
    <div class="col-sm-4 d-flex">
        <img class="img-fluid ms-auto me-auto" src="<?=$thumbnail?>">
    </div>
    <div class="col-sm-8">
        <a href="<?=$link?>"><?=$title?></a>
        <?php if ($date) { ?>
            <div><small class="text-muted"><?=date('F d, Y', (string) $date)?></small></div>
        <?php } ?>
        <?php if ($description) { ?>
            <div><?=$description?></div>
        <?php } ?>
    </div>
</div>
