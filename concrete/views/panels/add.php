<?php

use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Validation\CSRF\Token;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Permission\Checker $cp */
/* @var Concrete\Core\Page\Page $c */

/* @var string $tab */
/* @var Concrete\Core\Application\Service\Urls $ci */
?>
<section>
    <?php if ($tab == 'containers' || $tab == 'blocks') { ?>
        <div class="ccm-panel-header-search">
            <svg>
                <use xlink:href="#icon-search"/>
            </svg>
            <input type="text" data-input="search-blocks" placeholder="<?= t('Search') ?>" autocomplete="false"/>
        </div>
    <?php } ?>

    <header class="pl-0 pr-0">
        <div id="dropdown-menu" class="dropdown" data-panel-menu="dropdown">
            <div class="ccm-panel-header-list-grid-view-switcher"><i class="fa fa-list fa-xs fa-fw"></i></div>
            <h4 data-toggle="dropdown" data-panel-header="dropdown-menu" class="dropdown-toggle">
                <?php
                switch ($tab) {
                    case 'containers':
                        print t('Containers');
                        break;
                    case 'orphaned_blocks':
                        print t('Orphaned Blocks');
                        break;
                    case 'stacks':
                        print t('Stacks');
                        break;
                    case 'clipboard':
                        print t('Clipboard');
                        break;
                    case 'blocks':
                        print t('Blocks');
                        break;
                }
                ?>
            </h4>
            <div class="dropdown-menu">
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="blocks">
                    <?php echo t("Blocks"); ?>
                </a>
                <?php if ($showOrphanedBlockOption) { ?>
                    <a class="dropdown-item" href="#" data-panel-dropdown-tab="orphaned_blocks">
                        <?php echo t("Orphaned Blocks"); ?>
                    </a>
                <?php } ?>
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="clipboard">
                    <?php echo t("Clipboard"); ?>
                </a>
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="stacks">
                    <?php echo t("Stacks"); ?>
                </a>
                <a class="dropdown-item" href="#" data-panel-dropdown-tab="containers">
                    <?php echo t("Containers"); ?>
                </a>
            </div>
        </div>
    </header>

    <?php
    switch ($tab) {
    case 'containers':
        /* @var Concrete\Core\Entity\Page\Container[] $containers */
        ?>
        <div class="ccm-panel-content-inner ccm-stacked-list" id="ccm-panel-add-blocktypes-list"
             data-hide-grid-view-switcher>
            <ul class="ccm-stacked-list">
                <?php
                foreach ($containers as $container) {
                    ?>
                    <li>
                        <a
                                href="#"
                                class="ccm-panel-add-container-item"
                                data-panel-add-block-drag-item="container"
                                data-cID="<?= (int)$c->getCollectionID() ?>"
                                data-container-id="<?= $container->getContainerID() ?>"
                                data-block-type-handle="core_container"
                                data-has-add-template="0"
                                data-supports-inline-add="0"
                                data-btID="0"
                                data-dragging-avatar="<?= h('<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center">' . $container->getContainerIconImage() . '</div><p><span>' . $container->getContainerDisplayName() . '</span></p>') ?>"
                        >
                            <!-- <span class="handle"> -->
                            <div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img
                                        src="<?= $container->getContainerIconImage(false) ?>"/></div>
                            <p><span><?= h($container->getContainerDisplayName()) ?></span></p>
                            <!-- </span> -->
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <script>

        </script>
        <?php
        break;

    case 'stacks':
        /* @var Concrete\Core\Page\Stack\Stack[] $stacks */
        ?>
        <div id="ccm-panel-add-block-stack-list">
            <?php
            View::element('panels/add/stack_list', ['stacks' => $stacks, 'c' => $c]);
            ?>
        </div>
        <script>
            $('#ccm-panel-add-block-stack-list').on('click', 'div.ccm-panel-add-block-stack-item', function () {
                var $stack = $(this);
                if ($stack.data('ccm-stack-content-loaded')) {
                    $stack.toggleClass('ccm-panel-add-block-stack-item-expanded');
                    $stack.data('ccm-stack-content-loaded').toggle($stack.hasClass('ccm-panel-add-block-stack-item-expanded'));
                    return;
                }
                if ($stack.hasClass('ccm-panel-add-block-stack-item-expanded')) {
                    return;
                }
                $.concreteAjax({
                    dataType: 'html',
                    type: 'POST',
                    data: {
                        'cID': $(this).attr('data-cID'),
                        'stackID': $(this).attr('data-sID'),
                        'ccm_token': $(this).attr('data-token')
                    },
                    url: '<?=URL::to('/ccm/system/panels/add/get_stack_contents')?>',
                    success: function (r) {
                        var $content = $(r);
                        $stack.append($content);
                        $stack.data('ccm-stack-content-loaded', $content);
                        $stack.addClass('ccm-panel-add-block-stack-item-expanded');
                        $content.find('div.block').each(function () {
                            var block, me = $(this), dragger = me.find('div.block-name');
                            var stack = new Concrete.Stack($stack, Concrete.getEditMode(), null);
                            block = new Concrete.StackBlock($(this), stack, stack, dragger);

                            block.setPeper(dragger);
                        });
                    }
                })
            });

            $('#ccm-panel-add-block-stack-list').on('click', 'div.ccm-panel-add-folder-stack-item', function () {
                var $stackFolder = $(this);
                if ($stackFolder.data('ccm-folder-stack-content-loaded')) {
                    $stackFolder.toggleClass('ccm-panel-add-folder-stack-item-expanded');
                    $stackFolder.data('ccm-folder-stack-content-loaded').toggle($stackFolder.hasClass('ccm-panel-add-folder-stack-item-expanded'));
                    return;
                }
                if ($stackFolder.hasClass('ccm-panel-add-folder-stack-item-expanded')) {
                    return;
                }
                $.concreteAjax({
                    dataType: 'html',
                    type: 'POST',
                    data: {'cID': $(this).attr('data-cID'), 'stackFolderID': $(this).attr('data-sfID')},
                    url: '<?=URL::to('/ccm/system/panels/add/get_stack_folder_contents')?>',
                    success: function (r) {
                        var $content = $(r);
                        $stackFolder.after($content);
                        $stackFolder.data('ccm-folder-stack-content-loaded', $content);
                        $stackFolder.addClass('ccm-panel-add-folder-stack-item-expanded');
                        $content.find('div.ccm-panel-add-block-stack-item').each(function () {
                            var stack;
                            var me = $(this);
                            var dragger = me.find('div.ccm-panel-add-block-stack-item-handle');
                            stack = new Concrete.Stack($(this), Concrete.getEditMode(), dragger);

                            stack.setPeper(dragger)
                        });
                    }
                })
            });
        </script>
        <?php
        break;

    case 'clipboard':
        ?>
        <div id="ccm-panel-add-clipboard-block-list">
            <?php
            $app = Application::getFacadeApplication();
            /** @var Token $token */
            $token = $app->make(Token::class);
            $pileToken = $token->generate('tools/clipboard/from');
            ?>

            <div id="ccm-clipboard-container">
                <?php echo t("Loading..."); ?>
            </div>

            <script type="text/template" id="ccm-clipboard-template">
                <%_.forEach(results, function (result) {%>
                <div
                        class="ccm-panel-add-clipboard-block-item"
                        data-event="duplicate"
                        data-panel-add-block-drag-item="clipboard-item"
                        data-name="<%=result.name%>"
                        data-cID="<?php echo $c->getCollectionID() ?>"
                        data-token="<?php echo $pileToken ?>"
                        data-block-type-handle="<%=result.handle%>"
                        data-dialog-title="<%=result.dialogTitle%>"
                        data-dialog-width="<%=result.dialogWidth%>"
                        data-dialog-height="<%=result.dialogHeight%>"
                        data-has-add-template="<%=result.hasAddTemplate%>"
                        data-supports-inline-add="<%=result.supportsInlineAdd%>"
                        data-btID="<%=result.blockTypeId%>"
                        data-pcID="<%=result.pileContentId%>"
                        data-dragging-avatar="<%=result.draggingAvatar%>"
                        data-block-id="<%=result.blockId%>"
                >
                    <div class="delete float-right">
                        <button class="ccm-delete-clipboard-item btn btn-sm btn-link text-danger">
                            <?php echo t('Delete') ?>
                        </button>
                    </div>

                    <div class="block-content">
                        <div class="block-name float-left">
                                <span class="handle">
                                    <%=result.name%>
                                </span>
                        </div>

                        <div class="blocks">
                            <div class="block ccm-panel-add-block-draggable-block-type" title="<%=result.name%>">
                                <div class="block-content-inner">
                                    <%=result.blockContent%>
                                </div>

                                <div class="block-handle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <%})%>

                <% if (displayPagination) { %>
                <div class="d-flex"">
                <nav class="mx-auto">
                    <ul class="pagination">
                        <li class="page-item<% if (!hasPrev) { %> disabled<% } %>">
                            <a class="prev page-link<% if (!hasPrev) { %> disabled<% } %>" href="javascript:void(0);"
                               aria-label="<?php echo t("Previous"); ?>">
                                <span aria-hidden="true">&laquo;</span>

                                <span class="sr-only">
                                            <?php echo t("Previous"); ?>
                                        </span>
                            </a>
                        </li>

                        <li class="page-item<% if (!hasNext) { %> disabled<% } %>">
                            <a class="next page-link<% if (!hasNext) { %> disabled<% } %>" href="javascript:void(0);"
                               aria-label="<?php echo t("Next"); ?>">
                                <span aria-hidden="true">&raquo;</span>

                                <span class="sr-only">
                                            <?php echo t("Next"); ?>
                                        </span>
                            </a>
                        </li>
                    </ul>
                </nav>
                </div>
                <% } %>
            </script>

            <script>
                (function ($) {
                    $(function () {
                        var curPage = 0;

                        var loadClipboardItems = function () {
                            $.fn.dialog.showLoader();

                            $.get(CCM_DISPATCHER_FILENAME + '/ccm/system/panels/add/get_clipboard_contents', {
                                cID: <?php echo $c->getCollectionID(); ?>,
                                curPage: curPage
                            }, function (json) {
                                var templateHtml = $("#ccm-clipboard-template").html();
                                var html = _.template(templateHtml)(json);
                                var $container = $("#ccm-clipboard-container");

                                $container.html(html);

                                $container.find("a.prev").on("click", function () {
                                    curPage--;
                                    loadClipboardItems();
                                });

                                $container.find("a.next").on("click", function () {
                                    curPage++;
                                    loadClipboardItems();
                                });

                                $container.find('div.ccm-panel-add-clipboard-block-item').each(function () {
                                    new Concrete.DuplicateBlock($(this), window.concreteEditMode, window.concreteEditMode.getNextBlockArea())
                                })

                                $container.find('.ccm-delete-clipboard-item').click(function (e) {
                                    e.preventDefault();

                                    var me = $(this),
                                        item = me.closest('.ccm-panel-add-clipboard-block-item');

                                    $.post(CCM_DISPATCHER_FILENAME + '/ccm/system/block/process/remove_from_clipboard/' + item.data('pcid') + '/' + item.data('cid'), {
                                        ccm_token: item.data('token')
                                    }, function () {
                                        loadClipboardItems();
                                    }).fail(function (data) {
                                        ConcreteAlert.error("<?php echo t('An error occurred while deleting this item:') ?>\n" + data.responseJSON.errors.join("\n"));
                                    });
                                    return false;
                                });

                                $.fn.dialog.hideLoader();
                            });
                        };

                        loadClipboardItems();
                    });
                })(jQuery);
            </script>
        </div>

        <?php
        break;

    case 'orphaned_blocks':
        ?>
        <div id="ccm-panel-add-orphaned-block-list">
            <?php
            $app = Application::getFacadeApplication();
            /** @var Token $token */
            $token = $app->make(Token::class);
            $removeToken = $token->generate('remove_orphaned_block');
            ?>

            <div id="ccm-orphaned-block-container">
                <?php echo t("Loading..."); ?>
            </div>

            <script type="text/template" id="ccm-orphaned-block-template">
                <%_.forEach(results, function (result) {%>
                <div
                        class="ccm-panel-add-orphaned-block-item"
                        data-cID="<?php echo $c->getCollectionID() ?>"
                        data-token="<?php echo $removeToken ?>"
                        data-source-area-handle="<%=result.arID%>"
                        data-dragging-avatar="<%=result.draggingAvatar%>"
                        data-block-id="<%=result.bID%>"
                        data-name="<%=result.name%>"
                        data-block-type-handle="<%=result.handle%>"
                        data-dialog-title="<%=result.dialogTitle%>"
                        data-dialog-width="<%=result.dialogWidth%>"
                        data-dialog-height="<%=result.dialogHeight%>"
                        data-has-add-template="<%=result.hasAddTemplate%>"
                        data-supports-inline-add="<%=result.supportsInlineAdd%>"
                        data-btID="<%=result.blockTypeId%>"
                >
                    <div class="delete float-right">
                        <button class="ccm-delete-orphaned-block-item btn btn-sm btn-link text-danger">
                            <?php echo t('Delete') ?>
                        </button>
                    </div>

                    <div class="block-content">
                        <div class="block-name float-left">
                            <span class="handle">
                                <%=result.arHandle%>
                            </span>
                        </div>

                        <div class="blocks">
                            <div class="block ccm-panel-add-block-draggable-block-type" title="<%=result.arHandle%>">
                                <div class="block-content-inner">
                                    <%=result.blockContent%>
                                </div>

                                <div class="block-handle"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <%})%>

                <% if (displayPagination) { %>
                <div class="d-flex"">
                <nav class="mx-auto">
                    <ul class="pagination">
                        <li class="page-item<% if (!hasPrev) { %> disabled<% } %>">
                            <a class="prev page-link<% if (!hasPrev) { %> disabled<% } %>" href="javascript:void(0);"
                               aria-label="<?php echo t("Previous"); ?>">
                                <span aria-hidden="true">&laquo;</span>

                                <span class="sr-only">
                                    <?php echo t("Previous"); ?>
                                </span>
                            </a>
                        </li>

                        <li class="page-item<% if (!hasNext) { %> disabled<% } %>">
                            <a class="next page-link<% if (!hasNext) { %> disabled<% } %>" href="javascript:void(0);"
                               aria-label="<?php echo t("Next"); ?>">
                                <span aria-hidden="true">&raquo;</span>

                                <span class="sr-only">
                                    <?php echo t("Next"); ?>
                                </span>
                            </a>
                        </li>
                    </ul>
                </nav>
                </div>
                <% } %>

                <a href="javascript:void(0);"
                   class="btn-info btn-block btn btn-large btn-danger ccm-delete-orphaned-blocks">
                    <?php echo t('Remove all orphaned blocks') ?>
                </a>
            </script>

            <script>
                (function ($) {
                    $(function () {
                        var curPage = 0;

                        var loadOrphanedBlockItems = function () {
                            $.fn.dialog.showLoader();

                            var areaHandles = [];

                            $(".ccm-area").each(function() {
                                areaHandles.push($(this).data("areaHandle"));
                            });

                            $.post(CCM_DISPATCHER_FILENAME + '/ccm/system/panels/add/get_orphaned_block_contents', {
                                cID: <?php echo $c->getCollectionID(); ?>,
                                usedAreas: areaHandles,
                                curPage: curPage
                            }, function (json) {
                                var templateHtml = $("#ccm-orphaned-block-template").html();
                                var html = _.template(templateHtml)(json);
                                var $container = $("#ccm-orphaned-block-container");

                                $container.html(html);

                                $container.find("a.prev").on("click", function () {
                                    curPage--;
                                    loadOrphanedBlockItems();
                                });

                                $container.find("a.next").on("click", function () {
                                    curPage++;
                                    loadOrphanedBlockItems();
                                });

                                $container.find('div.ccm-panel-add-orphaned-block-item').each(function () {
                                    new Concrete.OrphanedBlock($(this), window.concreteEditMode, window.concreteEditMode.getNextBlockArea())
                                })

                                $container.find('.ccm-delete-orphaned-block-item').click(function (e) {
                                    e.preventDefault();

                                    var me = $(this),
                                        item = me.closest('.ccm-panel-add-orphaned-block-item');

                                    $.post('<?php echo \Concrete\Core\Support\Facade\Url::to('/ccm/system/panels/add/remove_orphaned_block')->setQuery(["cID" => $c->getCollectionID()]) ?>', {
                                        task: 'delete',
                                        usedAreas: areaHandles,
                                        blockId: item.data('blockId'),
                                        ccm_token: item.data('token')
                                    }, function (r) {
                                        if (typeof r.error === "undefined") {
                                            ConcreteAlert.notify({
                                                message: r.message,
                                                title: r.title
                                            });
                                        } else {
                                            for (var error of r.errors) {
                                                ConcreteAlert.error({'message': error})
                                            }
                                        }

                                        loadOrphanedBlockItems();
                                    }).fail(function (data) {
                                        ConcreteAlert.error("<?php echo t('An error occurred while deleting this item:') ?>\n" + data.responseJSON.errors.join("\n"));
                                    });
                                    return false;
                                });


                                $container.find('a.ccm-delete-orphaned-blocks').unbind().click(function (e) {
                                    e.preventDefault();

                                    $.concreteAjax({
                                        url: '<?php echo \Concrete\Core\Support\Facade\Url::to('/ccm/system/panels/add/remove_orphaned_blocks')->setQuery(["cID" => $c->getCollectionID()]) ?>',
                                        data: {
                                            usedAreas: areaHandles,
                                        },
                                        success: function (r) {
                                            if (typeof r.error === "undefined") {
                                                ConcreteAlert.notify({
                                                    message: r.message,
                                                    title: r.title
                                                });
                                            } else {
                                                for (var error of r.errors) {
                                                    ConcreteAlert.error({'message': error})
                                                }
                                            }

                                            $("#ccm-orphaned-block-container").html("");
                                        }
                                    });

                                    return false;
                                });

                                $.fn.dialog.hideLoader();
                            });
                        };

                        loadOrphanedBlockItems();
                    });
                })(jQuery);
            </script>
        </div>

        <?php
        break;
    case 'blocks':
    /* @var Concrete\Core\Entity\Block\BlockType\BlockType[] $blockTypesForSets */
    ?>
    <div class="ccm-panel-content-inner" id="ccm-panel-add-blocktypes-list">
        <?php
        $i = 0;
        foreach ($blockTypesForSets as $setName => $blockTypes) {
            $i++;
            ?>
            <div class="ccm-panel-add-block-set">
                <header
                        data-toggle="collapse"
                        data-target="#ccm-block-set-<?= $i ?>"
                        aria-expanded="true"
                        aria-controls="ccm-block-set-<?= $i ?>"
                >
                    <?= $setName ?><i class="fa fa-chevron-up float-right"></i>
                </header>
                <div id="ccm-block-set-<?= $i ?>" class="ccm-block-set collapse show">
                    <?php
                    // This class is added to help align the last row when it contains less than 3 elements
                    $justifyLastRowClass = (count($blockTypes) % 3) > 0 ? 'ccm-flex-align-last-row' : '';
                    ?>
                    <ul class="d-flex flex-row flex-wrap justify-content-between <?= $justifyLastRowClass; ?>">
                        <?php
                        foreach ($blockTypes as $bt) {
                            $btIcon = $ci->getBlockTypeIconURL($bt);
                            ?>
                            <li>
                                <a
                                        data-panel-add-block-drag-item="block"
                                        class="ccm-panel-add-block-draggable-block-type"
                                        data-cID="<?= $c->getCollectionID() ?>"
                                        data-block-type-handle="<?= $bt->getBlockTypeHandle() ?>"
                                        data-dialog-title="<?= t('Add %s', t($bt->getBlockTypeName())) ?>"
                                        data-dialog-width="<?= $bt->getBlockTypeInterfaceWidth() ?>"
                                        data-dialog-height="<?= $bt->getBlockTypeInterfaceHeight() ?>"
                                        data-has-add-template="<?= $bt->hasAddTemplate() ?>"
                                        data-supports-inline-add="<?= $bt->supportsInlineAdd() ?>"
                                        data-btID="<?= $bt->getBlockTypeID() ?>"
                                        data-dragging-avatar="<?= h('<div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center"><img src="' . $btIcon . '" /></div><p><span>' . t($bt->getBlockTypeInSetName()) . '</span></p>') ?>"
                                        title="<?= t($bt->getBlockTypeName()) ?>"
                                        href="javascript:void(0)"
                                >
                                    <div class="ccm-block-icon-wrapper d-flex align-items-center justify-content-center">
                                        <img src="<?= $btIcon ?>"/></div>
                                    <p><span><?= t($bt->getBlockTypeInSetName()) ?></span></p>
                                </a>
                            </li>
                            <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
            <?php
        }
        $p = new Permissions();
        if (Config::get('concrete.marketplace.enabled') && $p->canInstallPackages()) {
            ?>
            <div class="ccm-marketplace-btn-wrapper">
                <button
                        type="button"
                        onclick="window.location.href='<?= URL::to('/dashboard/extend/addons') ?>'"
                        class="btn-info btn-block btn btn-large"
                ><?= t('Get More Blocks') ?></button>
            </div>
            <?php
        }
        ?>
    </div>
</section>
<?php
break;
}
?>

<script type="text/javascript">
    $(function () {
        // switching the up/down arrows for collapsing block sets
        $('#ccm-panel-add-block').find('div[id^="ccm-block-set-"]').on('hidden.bs.collapse shown.bs.collapse', function () {
            $(this).prev('header[data-toggle="collapse"]').find('i.fa').toggleClass('fa-chevron-up fa-chevron-down');
        });

        // This makes the elements under the dropdown not react to hover.
        // Originally the block below the dropdown would grab the focus
        // and it would be impossible to click a link in the dropdown
        $('#dropdown-menu').on('hide.bs.dropdown shown.bs.dropdown', function () {
            $('#ccm-panel-add-blocktypes-list').toggleClass('ccm-no-pointer-events');
        });
        // switching between grid and stacked view for blocks
        var gridViewSwitcher = $('.ccm-panel-header-list-grid-view-switcher');

        gridViewSwitcher.on('click', function () {
            $('#ccm-panel-add-blocktypes-list').toggleClass('ccm-stacked-list');
            $(this).find('i.fa').toggleClass('fa-list fa-th');
        });

        // hiding the grid view switcher when not needed.
        // This uses a data attribute to mark panels
        // that don't require the grid switcher
        Concrete.event.bind('PanelLoad', function (evt, data) {
            gridViewSwitcher.removeClass('d-none');
            var element = $(data.element);
            if (element.find('[data-hide-grid-view-switcher]').length) {
                gridViewSwitcher.addClass('d-none');
            }
        });
    });
</script>
