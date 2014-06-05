<?php
use Concrete\Core\Page\Stack\Pile\PileContent;

defined('C5_EXECUTE') or die("Access Denied.");
?>

<section>

<div data-panel-menu="accordion" class="ccm-panel-header-accordion">
    <nav>
        <span></span>
        <ul class="ccm-panel-header-accordion-dropdown">
            <li><a data-panel-accordion-tab="blocks" <? if (!in_array(
                    $tab,
                    array(
                        'clipboard',
                        'stacks',
                        'tiles'))) {
                ?>data-panel-accordion-tab-selected="true" <? } ?>><?= t('Blocks') ?></a></li>
            <li><a data-panel-accordion-tab="clipboard"
                   <? if ($tab == 'clipboard') { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=
                    t(
                        'Clipboard') ?></a></li>
            <li><a data-panel-accordion-tab="stacks"
                   <? if ($tab == 'stacks') { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=
                    t(
                        'Stacks') ?></a></li>
            <li><a data-panel-accordion-tab="tiles"
                   <? if ($tab == 'tiles') { ?>data-panel-accordion-tab-selected="true" <? } ?>><?=
                    t(
                        'Gathering Tiles') ?></a></li>
        </ul>
    </nav>
</div>

<?php
switch ($tab) {

case 'tiles':
    ?>

    Gathering tiles

    <?php
    break;


case 'stacks':
    ?>
    <div id="ccm-panel-add-block-stack-list">
        <?php
        /** @var Stack[] $stacks */

        foreach ($stacks as $stack) {
            if (!$stack) {
                continue;
            }

            /** @var Block[] $blocks */
            $blocks = $stack->getBlocks();
            $block_count = count($blocks);
            ?>
            <div class="ccm-panel-add-block-stack-item"
                 data-panel-add-block-drag-item="stack-item"
                 data-cID="<?= intval($stack->getCollectionID()) ?>"
                 data-block-type-handle="<?= t('stack') ?>"
                 data-has-add-template="no"
                 data-supports-inline-add="no"
                 data-btID="0"
                 data-dragging-avatar="<?= h('<p><img src="/concrete/images/stack.png" /><span>' . t('Stack') . '</span></p>') ?>"
                 data-block-id="<?= intval($stack->getCollectionID()) ?>">
                <div class="stack-name">
                    <span class="handle"><?= htmlspecialchars($stack->getStackName()) ?></span>
                </div>
                <div class="blocks">
                    <div class="block-count">
                        <?= $block_count ?> <?= t2('Block', 'Blocks', $block_count) ?>
                    </div>
                    <?php

                    foreach ($blocks as $block) {
                        $type = $block->getBlockTypeObject();
                        $icon = $ci->getBlockTypeIconURL($type);
                        ?>
                        <div class="block"
                             data-panel-add-block-drag-item="block"
                             class="ccm-panel-add-block-draggable-block-type"
                             data-cID="<?= $stack->getCollectionID() ?>"
                             data-block-type-handle="<?= $type->getBlockTypeHandle() ?>"
                             data-dialog-title="<?= t('Add %s', t($type->getBlockTypeName())) ?>"
                             data-dialog-width="<?= $type->getBlockTypeInterfaceWidth() ?>"
                             data-dialog-height="<?= $type->getBlockTypeInterfaceHeight() ?>"
                             data-has-add-template="<?= $type->hasAddTemplate() ?>"
                             data-supports-inline-add="<?= $type->supportsInlineAdd() ?>"
                             data-btID="<?= $type->getBlockTypeID() ?>"
                             data-dragging-avatar="<?=
                             h(
                                 '<p><img src="' . $icon . '" /><span>' . t(
                                     $type->getBlockTypeName()) . '</span></p>') ?>"
                             title="<?= t($type->getBlockTypeName()) ?>"
                             href="javascript:void(0)"
                             data-block-id="<?= intval($block->getBlockID()) ?>">

                            <div class="block-name">
                                <span class="handle"><?= h($type->getBlockTypeName()) ?></span>
                            </div>
                            <div class="block-content">
                                <?php
                                $block->display()
                                ?>
                            </div>
                            <div class="block-handle"></div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
    <script>
        $('div.ccm-panel-add-block-stack-item').each(function () {
            var active = false,
                item = $(this),
                count = item.find('div.block-count');

            item.click(function (e) {
                e.preventDefault();
                var method;
                if (active) {
                    method = $.fn.removeClass;
                } else {
                    method = $.fn.addClass;
                }

                active = !active;

                method.call(item, 'active');

                var blocks = item.find('div.blocks');
                if (active) {
                    blocks.height('auto');
                    var height = blocks.height();
                    blocks.height('');
                    blocks.height(height);
                } else {
                    blocks.height('');
                }

                setTimeout(function() {
                    count.hasClass('hidden') ? count.removeClass('hidden') : count.addClass('hidden');
                }, 250);
                return false;
            });
        });
        $('div.ccm-panel-add-block-stack-item').find('a.stack-handle').toggle(function (e) {
            e.preventDefault();
            $(this).closest('div.ccm-panel-add-block-stack-item').addClass('active');
        }, function (e) {
            e.preventDefault();
            $(this).closest('div.ccm-panel-add-block-stack-item').removeClass('active');
        })
    </script>
    <?php
    break;


case 'clipboard':
    ?>

    <div id="ccm-panel-add-clipboard-block-list">
        <?php
        /** @var PileContent[] $contents */
        foreach ($contents as $pile_content) {
            $block = Block::getByID($pile_content->getItemID());

            if (!$block || !is_object($block) || $block->isError()) continue;

            $type = $block->getBlockTypeObject();
            $icon = $ci->getBlockTypeIconURL($type);
            ?>
            <div class="ccm-panel-add-clipboard-block-item"
                 data-event="duplicate"
                 data-panel-add-block-drag-item="clipboard-item"
                 data-name="<?= h($type->getBlockTypeName()) ?>"
                 data-cID="<?= $block->getBlockCollectionID() ?>"
                 data-block-type-handle="<?= $type->getBlockTypeHandle() ?>"
                 data-dialog-title="<?= t('Add %s', t($type->getBlockTypeName())) ?>"
                 data-dialog-width="<?= $type->getBlockTypeInterfaceWidth() ?>"
                 data-dialog-height="<?= $type->getBlockTypeInterfaceHeight() ?>"
                 data-has-add-template="<?= $type->hasAddTemplate() ?>"
                 data-supports-inline-add="<?= $type->supportsInlineAdd() ?>"
                 data-btID="<?= $type->getBlockTypeID() ?>"
                 data-pcID="<?=$pile_content->getPileContentID()?>"
                 data-dragging-avatar="<?=
                 h(
                     '<p><img src="' . $icon . '" /><span>' . t(
                         $type->getBlockTypeName()) . '</span></p>') ?>"
                 data-block-id="<?= intval($block->getBlockID()) ?>">
                <div class="block-name">
                    <span class="handle"><?= h($type->getBlockTypeName()) ?></span>
                </div>
                <div class="blocks">
                    <div class="block"
                         class="ccm-panel-add-block-draggable-block-type"
                         title="<?= t($type->getBlockTypeName()) ?>">

                        <div class="block-content">
                            <?php
                            $block->display()
                            ?>
                        </div>
                        <div class="block-handle"></div>
                    </div>
                </div>
            </div>
        <?php
        }
        ?>
    </div>

    <?php
    break;

default:
?>

<div class="ccm-panel-content-inner">

    <?php
    $sets = BlockTypeSet::getList();
    $types = array();
    foreach ($blockTypes as $bt) {
        if (!$cp->canAddBlockType($bt)) {
            continue;
        }

        $btsets = $bt->getBlockTypeSets();
        foreach ($btsets as $set) {
            $types[$set->getBlockTypeSetName()][] = $bt;
        }
        if (count($btsets) == 0) {
            $types['Other'][] = $bt;
        }
    }

    for ($i = 0; $i < count($sets); $i++) {
        $set = $sets[$i];

        ?>
        <div class="ccm-panel-add-block-set">
            <header><?= $set->getBlockTypeSetDisplayName() ?></header>
            <ul>

                <? $blocktypes = $types[$set->getBlockTypeSetName()];
                foreach ($blocktypes as $bt) {

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
                            data-dragging-avatar="<?=
                            h(
                                '<p><img src="' . $btIcon . '" /><span>' . t(
                                    $bt->getBlockTypeName()) . '</span></p>') ?>"
                            title="<?= t($bt->getBlockTypeName()) ?>"
                            href="javascript:void(0)"
                            >
                            <p><img src="<?= $btIcon ?>"/><span><?= t($bt->getBlockTypeName()) ?></span></p>
                        </a>
                    </li>

                <? } ?>
            </ul>
        </div>

    <? } ?>

    <? if (is_array($types['Other'])) { ?>

        <div class="ccm-panel-add-block-set">
            <header><?= t('Other') ?></header>
            <ul>
                <? $blocktypes = $types['Other'];
                foreach ($blocktypes as $bt) {
                    $btIcon = $ci->getBlockTypeIconURL($bt);
                    ?>

                    <li data-block-type-sets="<?= $sets ?>">
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
                            data-dragging-avatar="<?=
                            h(
                                '<p><img src="' . $btIcon . '" /><span>' . t(
                                    $bt->getBlockTypeName()) . '</span></p>') ?>"
                            title="<?= t($bt->getBlockTypeName()) ?>"
                            href="javascript:void(0)"
                            ><p><img src="<?= $btIcon ?>"/><span><?= t($bt->getBlockTypeName()) ?></span></p></a>
                    </li>

                <? } ?>
            </ul>

        </div>
    <? } ?>

</div>

</section>

    <? } ?>
