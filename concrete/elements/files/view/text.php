<?php defined('C5_EXECUTE') or die('Access Denied.'); ?> 
<div style="text-align: left">
    <pre style="font-size: 11px; font-family: Courier">
        <?=Core::make('helper/text')->entities($fv->getFileContents())?>
    </pre>
</div>
