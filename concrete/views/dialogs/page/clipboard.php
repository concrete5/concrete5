<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>
<div id="ccm-panel-add-clipboard-block-list">
    <ul class="item-select-list">
    <?php
    /** @var PileContent[] $contents */
    foreach ($contents as $pile_content) {
        $block = Block::getByID($pile_content->getItemID());

        if (!$block || !is_object($block) || $block->isError()) {
            continue;
        }

        $type = $block->getBlockTypeObject();
        $icon = $ci->getBlockTypeIconURL($type);
        ?>

        <li>
            <a
                data-name="<?= h($type->getBlockTypeName()) ?>"
                data-cID="<?= $c->getCollectionID() ?>"
                data-block-type-handle="<?= $type->getBlockTypeHandle() ?>"
                data-dialog-title="<?= t('Add %s', t($type->getBlockTypeName())) ?>"
                data-dialog-width="<?= $type->getBlockTypeInterfaceWidth() ?>"
                data-dialog-height="<?= $type->getBlockTypeInterfaceHeight() ?>"
                data-has-add-template="<?= $type->hasAddTemplate() ?>"
                data-supports-inline-add="<?= $type->supportsInlineAdd() ?>"
                data-btID="<?= $type->getBlockTypeID() ?>"
                data-pcID="<?=$pile_content->getPileContentID()?>"
                href="javascript:void(0)"><img src="<?=$icon?>" /> <?=t($type->getBlockTypeName())?></a>

                <div class="item-select-list-content">
                    <?php
                    $bv = new \Concrete\Core\Block\View\BlockView($block);
        $bv->render('scrapbook')
                    ?>
                </div>
        </li>

        <?php 
    } ?>
    </ul>
</div>

<script type="text/javascript">
    $(function() {
        $('#ccm-panel-add-clipboard-block-list').on('click', 'a', function() {
            ConcreteEvent.publish('ClipboardAddBlock', {
                $launcher: $(this)
            });
            return false;
        });
    });
</script>