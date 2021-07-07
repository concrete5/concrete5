<?php

/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpDeprecationInspection */
/** @noinspection DuplicatedCode */
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\MenuInterface;
use Concrete\Core\Logging\LogEntry;
use Concrete\Core\Logging\Menu;
use Concrete\Core\Logging\Search\Result\Column;
use Concrete\Core\Logging\Search\Result\Item;
use Concrete\Core\Logging\Search\Result\ItemColumn;
use Concrete\Core\Logging\Search\Result\Result;

// @var MenuInterface $menu
// @var Result $result
// @var DropdownMenu $resultsBulkMenu

?>
<div id="ccm-search-results-table">
    <table class="ccm-search-results-table" data-search-results="pages">
        <thead>
        <tr>
            <th class="ccm-search-results-bulk-selector">
                <div class="btn-group dropdown">
                    <span class="btn btn-secondary" data-search-checkbox-button="select-all">
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input type="checkbox" data-search-checkbox="select-all"/>
                    </span>

                    <button
                            type="button"
                            disabled="disabled"
                            data-search-checkbox-button="dropdown"
                            class="btn btn-secondary dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown"
                            data-reference="parent">

                            <span class="sr-only">
                                <?php echo t('Toggle Dropdown'); ?>
                            </span>
                    </button>

                    <div data-search-menu="dropdown">
                        <?php echo $resultsBulkMenu->getMenuElement(); ?>
                    </div>
                </div>
            </th>

            <?php foreach ($result->getColumns() as $column) { ?>
                <?php /** @var Column $column */ ?>
                <th class="<?php echo $column->getColumnStyleClass(); ?>">
                    <?php if ($column->isColumnSortable()) { ?>
                        <a href="<?php echo $column->getColumnSortURL(); ?>">
                            <?php echo $column->getColumnTitle(); ?>
                        </a>
                    <?php } else { ?>
                        <span>
                            <?php echo $column->getColumnTitle(); ?>
                        </span>
                    <?php } ?>
                </th>
            <?php } ?>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($result->getItems() as $item) { ?>
            <?php
            /** @var Item $item */
            /** @var LogEntry $page */
            $logEntry = $item->getItem();
            ?>
            <tr data-details-url="javascript:void(0)">
                <td class="ccm-search-results-checkbox">
                    <?php if ($logEntry instanceof LogEntry) { ?>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input data-search-checkbox="individual"
                               type="checkbox"
                               data-item-id="<?php echo $logEntry->getId(); ?>"/>
                    <?php } ?>
                </td>

                <?php foreach ($item->getColumns() as $column) { ?>
                    <?php /** @var ItemColumn $column */ ?>
                    <td class="<?php echo $class; ?>">
                        <?php echo $column->getColumnValue(); ?>
                    </td>
                <?php } ?>

                <?php $menu = new Menu($logEntry); ?>

                <?php if ($menu) { ?>
                    <td class="ccm-search-results-menu-launcher">
                        <div class="dropdown" data-menu="search-result">

                            <button class="btn btn-icon"
                                    data-boundary="viewport"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-haspopup="true"
                                    aria-expanded="false">

                                <svg width="16" height="4">
                                    <use xlink:href="#icon-menu-launcher"/>
                                </svg>
                            </button>

                            <?php echo $menu->getMenuElement(); ?>
                        </div>
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<script>
    (function ($) {
        $(function () {
            let searchResultsTable = new window.ConcreteSearchResultsTable($("#ccm-search-results-table"));
            searchResultsTable.setupBulkActions();
        });
    })(jQuery);
</script>

<?php echo $result->getPagination()->renderView('dashboard'); ?>
