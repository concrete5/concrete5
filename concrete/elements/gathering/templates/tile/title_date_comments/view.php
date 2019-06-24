<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-gathering-tile-headline"><a href="<?=$link?>"><?=$title?></a></div>
<div><?=Core::make('helper/date')->formatDateTime($date_time, true)?></div>

<div>
<br/>
<i class="icon-bullhorn"></i> <?=$totalPosts?>
</div>



