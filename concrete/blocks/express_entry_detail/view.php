<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php

if (isset($renderer) && isset($entry) && is_object($entry)) { ?>

<?php
echo $renderer->render($entry);
?>


<?php } ?>