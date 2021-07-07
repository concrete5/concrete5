<?php

/** @noinspection PhpUndefinedMethodInspection */
/** @noinspection PhpDeprecationInspection */
/** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\MenuInterface;
use Concrete\Core\User\Search\Result\Column;
use Concrete\Core\User\Search\Result\Result;
use Concrete\Core\User\Search\Result\Item;
use Concrete\Core\User\Search\Result\ItemColumn;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\Menu;
use Concrete\Core\User\UserInfo;
use Concrete\Core\User\Search\ColumnSet\Column\UsernameColumn;

/** @var MenuInterface $menu */
/** @var Result $result */
/** @var DropdownMenu $resultsBulkMenu */

?>
<div id="ccm-search-results-table">
    <table class="ccm-search-results-table" data-search-results="users">
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
                                <?php echo t("Toggle Dropdown"); ?>
                            </span>
                    </button>

                    <div data-search-menu="dropdown">
                        <?php echo $resultsBulkMenu->getMenuElement(); ?>
                    </div>
                </div>
            </th>

            <?php foreach ($result->getColumns() as $column): ?>
                <?php /** @var Column $column */ ?>
                <th class="<?php echo $column->getColumnStyleClass() ?>">
                    <?php if ($column->isColumnSortable()): ?>
                        <a href="<?php echo $column->getColumnSortURL() ?>">
                            <?php echo $column->getColumnTitle() ?>
                        </a>
                    <?php else: ?>
                        <span>
                            <?php echo $column->getColumnTitle() ?>
                        </span>
                    <?php endif; ?>
                </th>
            <?php endforeach; ?>
        </tr>
        </thead>

        <tbody>
        <?php foreach ($result->getItems() as $item) { ?>
            <?php
            /** @var Item $item */
            /** @var UserInfo $user */
            $user = $item->getItem();
            ?>
            <tr data-details-url="javascript:void(0)">
                <td class="ccm-search-results-checkbox">
                    <?php if ($user instanceof UserInfo) { ?>
                        <!--suppress HtmlFormInputWithoutLabel -->
                        <input data-search-checkbox="individual"
                               type="checkbox"
                               data-item-id="<?php echo $user->getUserID() ?>"/>
                    <?php } ?>
                </td>

                <?php foreach ($item->getColumns() as $column) { ?>
                    <?php /** @var ItemColumn $column */ ?>
                    <?php /** @noinspection PhpUndefinedMethodInspection */

                    if ($column->getColumnKey() ==  'u.uName') { ?>
                        <td class="ccm-search-results-name">
                            <?php echo $column->getColumnValue(); ?>
                        </td>
                    <?php } else { ?>
                        <td class="<?php echo $class ?>">
                            <?php echo $column->getColumnValue(); ?>
                        </td>
                    <?php } ?>
                <?php } ?>

                <?php $menu = new Menu($user); ?>

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

            $('#ccm-search-results-table').on('click', 'a[data-user-id]', function () {
                window.location.href = '<?=rtrim(URL::to('/dashboard/users/search', 'edit'), '/')?>/' + $(this).attr('data-user-id');
                return false;
            });

        });
    })(jQuery);
</script>

<?php echo $result->getPagination()->renderView('dashboard'); ?>
