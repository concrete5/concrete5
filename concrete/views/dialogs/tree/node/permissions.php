<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-ui">
    <?php Loader::element('permission/lists/tree/node', array(
        'node' => $node,
    ))?>
</div>
