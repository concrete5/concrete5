<?php

defined('C5_EXECUTE') or die('Access Denied.');

?>

<div class="ccm-dashboard-content-full">
    <div class="table-responsive">
        <table class="ccm-search-results-table">
            <thead>
            <tr>
                <th></th>
                <th class=""><span><?=t('Name')?></span></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($nodes as $node) {
                $formatter = $node->getListFormatter();
                $np = new Permissions($node);
                if ($np->canViewExpressEntries()) {
                ?>
                <tr data-details-url="<?=$view->action('view', $node->getTreeNodeID())?>"
                    class="<?=$formatter->getSearchResultsClass()?>">
                    <td class="ccm-search-results-icon"><?=$formatter->getIconElement()?></td>
                    <td class="ccm-search-results-name"><?=$node->getTreeNodeDisplayName()?></td>
                    <td></td>
                </tr>
            <?php }
            } ?>
            </tbody>
        </table>
    </div>
</div>