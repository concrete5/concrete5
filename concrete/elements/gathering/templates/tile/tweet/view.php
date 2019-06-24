<?php defined('C5_EXECUTE') or die('Access Denied.');
?>
<div class="ccm-gathering-twitter">
	<img class="twitter-logo" src="https://abs.twimg.com/a/1373252541/images/resources/twitter-bird-light-bgs.png" />
	<div class="tweet">
		<span class="tweet-body"><?=$description?></span>
		<div style="clear: both;"></div>
		<p class="tweet-info"><span class="elapsed"><?=Core::make('date')->formatDate($date_time)?></span><span class="who-from"><a href="https://twitter.com/<?php echo $author ?>"><?php echo $author ?></a></span></p>
	</div>
</div>
