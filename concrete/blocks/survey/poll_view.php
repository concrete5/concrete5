<?php
defined('C5_EXECUTE') or die('Access Denied.');

$a2 = new Area('Poll');
$aBlocks = $a2->getAreaBlocksArray($c);
/*
$pollBlocks=array();
foreach($aBlocks as $bls){
    if($bls->getBlockTypeHandle() != 'poll') continue;
    $pollBlocks[]=$bls;
}
*/
if (count($aBlocks) > 0) {
    ?>
<div id="post-poll">
	<div class="aux">
	<?php
        $a2->display($c, $aBlocks);
    ?>	
	</div>
</div>
<?php 
} ?>