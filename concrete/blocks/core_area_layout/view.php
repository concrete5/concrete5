<?php
    defined('C5_EXECUTE') or die('Access Denied.');
    $a = $b->getBlockAreaObject();

    $container = $formatter->getLayoutContainerHtmlObject();
    foreach ($columns as $column) {
        $html = $column->getColumnHtmlObject();
        if (! empty($container)) {
            $container->appendChild($html);
        } else {
            print $html;
        }
    }
    echo $container;
