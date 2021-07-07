<?php
defined('C5_EXECUTE') or die("Access Denied.");
?>

<h3 class="fw-light"><?=t('Filter Pages')?></h3>
<p><small class="text-muted"><?=t("Add search fields below to limit the pages added.")?></small></p>
<?php
$fieldSelector->render();
?>
