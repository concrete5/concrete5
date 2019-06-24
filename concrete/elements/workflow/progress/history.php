<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

$records = \Concrete\Core\Workflow\Progress\History::getList($wp);
foreach ($records as $r) {
    ?>
	
	<div>
		<strong><?=$dh->formatDateTime($r->getWorkflowProgressHistoryTimestamp(), true)?></strong>. 
		<?=$r->getWorkflowProgressHistoryDescription();
    ?>
	</div>	
	
<?php 
} ?>