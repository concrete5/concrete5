<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Permission\Checker $cp
 * @var Concrete\Core\Page\Page $c
 * @var Concrete\Core\Application\Service\Urls $ci
 * @var Concrete\Core\Entity\Block\BlockType\BlockType[] $blockTypesForSets
 */

$id = str_replace('.', '_', uniqid('ccm-add-block-lists-', true));
?>
<div id="<?= $id ?>">
    <div class="input-group">
        <span class="input-group-text"><i class="fas fa-search"></i></span>
        <input type="search" class="form-control" autofocus="autofocus"/>
    </div>
    <br/>
    <?php
    foreach ($blockTypesForSets as $setName => $blockTypes) {
        ?>
        <section>
            <h3 class="fw-normal"><?= $setName ?></h3>
            <ul class="item-select-list">
                <?php
                foreach ($blockTypes as $bt) {
                    $btIcon = $ci->getBlockTypeIconURL($bt);
                    ?>
                    <li>
                        <a
                                data-cID="<?= $c->getCollectionID() ?>"
                                data-block-type-handle="<?= $bt->getBlockTypeHandle() ?>"
                                data-block-type-name="<?= h(t($bt->getBlockTypeName())) ?>"
                                data-block-type-description="<?= h(t($bt->getBlockTypeDescription())) ?>"
                                data-dialog-title="<?= t('Add %s', t($bt->getBlockTypeName())) ?>"
                                data-dialog-width="<?= $bt->getBlockTypeInterfaceWidth() ?>"
                                data-dialog-height="<?= $bt->getBlockTypeInterfaceHeight() ?>"
                                data-has-add-template="<?= $bt->hasAddTemplate() ?>"
                                data-supports-inline-add="<?= $bt->supportsInlineAdd() ?>"
                                data-btID="<?= $bt->getBlockTypeID() ?>"
                                title="<?= h(t($bt->getBlockTypeDescription())) ?>"
                                href="javascript:void(0)"
                        ><img src="<?= $btIcon ?>"/> <?= t($bt->getBlockTypeName()) ?></a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </section>
        <?php
    }
    ?>
</div>

<script>
    $(function () {
        var $list = $('#<?= $id ?>'),
            $search = $list.find('input[type="search"]');

        $search.on('keydown keypress keyup change blur', function () {
            var search = $.trim($search.val()).toLowerCase();
            $list.find('section').each(function () {
                var $section = $(this),
                    someDisplayed = false;
                $section.find('li a').each(function () {
                    var $a = $(this),
                        $li = $a.closest('li');
                    if (search === '' || ($a.data('block-type-name') || '').toLowerCase().indexOf(search) >= 0 || ($a.data('block-type-description') || '').toLowerCase().indexOf(search) >= 0) {
                        $li.show();
                        someDisplayed = true;
                    } else {
                        $li.hide();
                    }
                });
                $section.toggle(someDisplayed);
            });
        });

        $list.find('a').on('click', function () {
            ConcreteEvent.publish('AddBlockListAddBlock', {
                $launcher: $(this)
            });
            return false;
        });

        setTimeout(function () {
            $search.focus();
        }, 250);
    });
</script>
