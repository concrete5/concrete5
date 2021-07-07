<?php defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Controller\SinglePage\Dashboard\Blocks\Types $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Application\Service\Urls $ci
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 * @var int $num
 * @var int $numActive
 */

if ($controller->getAction() == 'inspect') {
    // @var Concrete\Core\Entity\Block\BlockType\BlockType $bt
    // @var int $num
    // @var int $numActive
    ?>
    <h3><img src="<?= $ci->getBlockTypeIconURL($bt) ?>"/> <?= t($bt->getBlockTypeName()) ?></h3>
    <dl>
        <dt><?= t('Description') ?></dt>
        <dd><?= t($bt->getBlockTypeDescription()) ?></dd>
        <dt><?= t('Usage Count') ?></dt>
        <dd><?= $num ?></dd>
        <dt><?= t('Usage Count on Active Pages') ?></dt>
        <dd>
            <?php
            if ($numActive > 0) {
                ?><a href="<?= $view->action('search', $bt->getBlockTypeID()) ?>"><?= $numActive ?></a><?php
            } else {
                echo 0;
            }
            ?>
        </dd>
        <?php
        if ($bt->isBlockTypeInternal()) {
            ?>
            <dt><?= t('Internal') ?></dt>
            <dd><?= t('This is an internal block type.') ?></dd>
            <?php
        }
        ?>
    </dl>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?= $urlResolver->resolve(['/dashboard/blocks/types']) ?>"
               class="btn btn-secondary float-start"><?= t('Back to Block Types') ?></a>
            <div class="float-end">
                <a href="<?= $urlResolver->resolve(['/dashboard/blocks/types', 'refresh', $bt->getBlockTypeID(), $token->generate('ccm-refresh-blocktype')]) ?>"
                   class="btn btn-secondary"><?= t('Refresh') ?></a>
                <?php
                $u = Core::make(Concrete\Core\User\User::class);
                if ($u->isSuperUser()) {
                    ?>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="removeBlockType()"><?= t('Remove') ?></a>
                    <script>
                        function removeBlockType() {
                            <?php
                            if ($bt->canUnInstall()) {
                            ?>
                            if (confirm(<?= json_encode(t('This will remove all instances of the %s block type. This cannot be undone. Are you sure?', $bt->getBlockTypeHandle())) ?>)) {
                                location.href = <?= json_encode((string) $urlResolver->resolve(['/dashboard/blocks/types', 'uninstall', $bt->getBlockTypeID(), $token->generate('ccm-uninstall-blocktype')])) ?>;
                            }
                            <?php
                            } else {
                            ?>
                            alert(<?= json_encode(t('This block type is internal. It cannot be uninstalled.')) ?>);
                            <?php
                            }
                            ?>
                        }
                    </script>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
    <?php
} else {
    /**
     * @var Concrete\Core\Entity\Block\BlockType\BlockType[] $availableBlockTypes
     * @var Concrete\Core\Entity\Block\BlockType\BlockType[] $internalBlockTypes
     * @var array $normalBlockTypesAndSets
     * @var bool $marketplaceEnabled
     * @var bool $enableMoveBlocktypesAcrossSets
     */
    ?>
    <h3><?= t('Awaiting Installation') ?></h3>
    <?php
    if (!empty($availableBlockTypes)) {
        ?>
        <ul class="item-select-list">
            <?php
            foreach ($availableBlockTypes as $bt) {
                $btIcon = $ci->getBlockTypeIconURL($bt);
                ?>
                <li><span class="clearfix"><img src="<?= $btIcon ?>"/> <?= t($bt->getBlockTypeName()) ?>
                    <a href="<?= $urlResolver->resolve(['/dashboard/blocks/types', 'install', $bt->getBlockTypeHandle()]) ?>"
                       class="btn float-end btn-sm btn-secondary"><?= t('Install') ?></a>
                </span></li>
                <?php
            }
            ?>
        </ul>
        <?php
    } else {
        ?>
        <p><?= t('No custom block types are awaiting installation.') ?></p>
        <?php
    }
    if ($marketplaceEnabled) {
        ?>
        <div class="alert alert-info">
            <a class="btn btn-success btn-sm float-end"
               href="<?= $urlResolver->resolve(['/dashboard/extend/addons']) ?>"><?= t('More Add-ons') ?></a>
            <p class="mb-2"><?= t('Browse our marketplace of add-ons to extend your site!') ?></p>
        </div>
        <?php
    }
    ?>
    <hr/>
    <h3><?= t('Installed Block Types') ?></h3>
    <ul class="item-select-list" id="ccm-btlist-btsets">
        <?php
        foreach ($normalBlockTypesAndSets as $normalBlockTypesAndSet) {
            $blockTypeSet = $normalBlockTypesAndSet['blockTypeSet'];
            // @var Concrete\Core\Block\BlockType\Set|null $blockTypeSet
            $normalBlockTypes = $normalBlockTypesAndSet['blockTypes'];
            // @var Concrete\Core\Entity\Block\BlockType\BlockType[] $normalBlockTypes

            ?>
            <li class="ccm-btlist-btset" data-btset-id="<?= $blockTypeSet === null ? '0' : $blockTypeSet->getBlockTypeSetID() ?>">
                <h4 class="ccm-btlist-btset-name">
                    <?php
                    if ($blockTypeSet !== null) {
                        ?><i class="fas fa-bars" aria-hidden="true"></i><?php
                    }
                    ?>
                    <?= $blockTypeSet === null ? t('Other') : $blockTypeSet->getBlockTypeSetDisplayName() ?>
                </h4>
                <ul class="item-select-list ccm-btlist-bts">
                    <?php
                    foreach ($normalBlockTypes as $bt) {
                        ?>
                        <li class="ccm-btlist-bt" data-bt-id="<?= $bt->getBlockTypeID() ?>"
                            title="<?= h($bt->getBlockTypeDescription()) ?>">
                            <a href="<?= $view->action('inspect', $bt->getBlockTypeID()) ?>">
                                <i class="fas fa-bars" aria-hidden="true"></i>
                                <img src="<?= $ci->getBlockTypeIconURL($bt) ?>"/>
                                <?= t($bt->getBlockTypeName()) ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </li>
            <?php
        }
        ?>
    </ul>
    <h3><?= t('Internal Block Types') ?></h3>
    <ul class="item-select-list">
        <?php
        foreach ($internalBlockTypes as $bt) {
            $btIcon = $ci->getBlockTypeIconURL($bt);
            ?>
            <li>
                <a href="<?= $view->action('inspect', $bt->getBlockTypeID()) ?>">
                    <img src="<?= $btIcon ?>"/> <?= t($bt->getBlockTypeName()) ?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
    <script>
        $(document).ready(function () {
            var $btSetList = $('#ccm-btlist-btsets'),
                $btLists = $('#ccm-btlist-btsets .ccm-btlist-bts'),
                $allSortables = $().add($btSetList).add($btLists),
                tokenName = <?= json_encode($token::DEFAULT_TOKEN_NAME) ?>,
                actions = <?= json_encode([
                    'sortSets' => [
                        'url' => (string) $view->action('sort_blocktypesets'),
                        'token' => $token->generate('ccm-sort_blocktypesets'),
                    ],
                    'sortBlockTypes' => [
                        'url' => (string) $view->action('sort_blocktypes'),
                        'token' => $token->generate('ccm-sort_blocktypes'),
                    ],
                ]) ?>;

            function ajax(which, data, onSuccess, onError) {
                data[tokenName] = actions[which].token;
                $.ajax({
                    data: data,
                    dataType: 'json',
                    method: 'POST',
                    url: actions[which].url
                }).done(function () {
                    onSuccess();
                }).fail(function (xhr, status, error) {
                    var msg = error;
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error.message || xhr.responseJSON.error;
                    }
                    window.alert(msg);
                    onError();
                });
            }

            $btSetList.sortable({
                items: '.ccm-btlist-btset',
                handle: '.ccm-btlist-btset-name .fa-bars',
                cursor: 'move',
                axis: 'y',
                containment: $btSetList,
                stop: function (event, ui) {
                    $allSortables.sortable('disable');
                    $(ui.item).css({left: '', top: '', position: ''});
                    var btSetIDs = [];
                    $btSetList.find('.ccm-btlist-btset').each(function () {
                        btSetIDs.push($(this).data('btset-id'));
                    });
                    ajax(
                        'sortSets',
                        {btSetIDs: btSetIDs},
                        function () {
                            $allSortables.sortable('enable');
                        },
                        function () {
                            $allSortables.sortable('cancel');
                            $allSortables.sortable('enable');
                        }
                    );
                }
            });
            $btLists.sortable({
                items: '.ccm-btlist-bt',
                handle: 'a .fa-bars',
                cursor: 'move',
                axis: 'y',
                <?php
                if ($enableMoveBlocktypesAcrossSets) {
                ?>
                containment: $btSetList,
                connectWith: $btLists,
                <?php
                } else {
                ?>
                containment: 'parent',
                <?php
                }
                ?>
                start: function () {
                    var $me = $(this),
                        $btSet = $me.closest('.ccm-btlist-btset'),
                        btSetID = $btSet.data('btset-id');
                    $me.data('original-btset-id', btSetID);
                },
                stop: function (event, ui) {
                    $allSortables.sortable('disable');
                    $(ui.item).css({left: '', top: '', position: ''});
                    var oldBtSetID = $(this).data('original-btset-id'),
                        movingID = $(ui.item).data('bt-id'),
                        $btSet = $(ui.item).closest('.ccm-btlist-btset'),
                        newBtSetID = $btSet.data('btset-id'),
                        btIDs = [];
                    $btSet.find('.ccm-btlist-bt').each(function () {
                        btIDs.push($(this).data('bt-id'));
                    });
                    ajax(
                        'sortBlockTypes',
                        {movingID: movingID, oldBtSetID: oldBtSetID, newBtSetID: newBtSetID, btIDs: btIDs},
                        function () {
                            $allSortables.sortable('enable');
                        },
                        function () {
                            $allSortables.sortable('cancel');
                            $allSortables.sortable('enable');
                        }
                    );
                }
            });
        });
    </script>
    <?php
}
