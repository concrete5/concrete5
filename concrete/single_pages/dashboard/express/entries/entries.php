<?php

/**
 * @var $resultsBulkMenu \Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu
 */

defined('C5_EXECUTE') or die('Access Denied.');

?>
    <div class="table-responsive">
        <table class="ccm-search-results-table" data-search-results="files">
            <thead>
            <tr>
                <?php foreach ($result->getColumns() as $column) { ?>
                    <th class="<?=$column->getColumnStyleClass()?>">
                        <?php if ($column->isColumnSortable()) { ?>
                            <a href="<?=$column->getColumnSortURL()?>"><?=$column->getColumnTitle()?></a>
                        <?php } else { ?>
                            <span><?=$column->getColumnTitle()?></span>
                        <?php } ?>
                    </th>
                <?php } ?>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($result->getItems() as $item) {
                $entry = $item->getItem();
                ?>
                <tr data-details-url="<?=$view->action('view_entry', $entry->getId())?>">
                    <?php
                    $i = 0;
                    foreach ($item->getColumns() as $column) {
                        $class = '';
                        if ($column->getColumn() instanceof \Concrete\Core\File\Search\ColumnSet\Column\NameColumn) {
                            $class = 'ccm-search-results-name';
                            $value = '<a href="' . $item->getDetailsURL() . '">' . $column->getColumnValue($item) . '</a>';
                        } else {
                            $value = $column->getColumnValue($item);
                        }
                        ?>
                        <td class="<?=$class?>"><?=$value?></td>
                        <?php
                        $i++;
                    }

                    $menu = $item->getItem()->getTreeNodeMenu();
                    if ($menu) {
                        ?>
                        <td class="ccm-search-results-menu-launcher">
                            <div class="dropdown" data-menu="search-result">
                                <button class="btn btn-icon" data-boundary="viewport" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg width="16" height="4"><use xlink:href="#icon-menu-launcher" /></svg>
                                </button>
                                <?php
                                echo $menu->getMenuElement();
                                ?>
                            </div>
                        </td>
                    <?php } ?>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <?=$result->getPagination()->renderView('dashboard')?>

