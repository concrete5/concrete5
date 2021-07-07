<?php
defined('C5_EXECUTE') or die("Access Denied.");

foreach ($sortable_sets as $set) {
    ?>
    <h4><?=$set->getAttributeSetName()?></h4>
    <ul class="item-select-list ccm-attribute-list-wrapper" data-sortable-attribute-set="<?=$set->getAttributeSetID()?>">
        <?php
        foreach ($set->getAttributeKeys() as $key) {
            $controller = $key->getController();
            $formatter = $controller->getIconFormatter();
            ?>
            <li class="ccm-attribute" id="akID_<?=$key->getAttributeKeyID()?>">
                <a href="<?=$view->controller->getEditAttributeKeyURL($key)?>" title="<?php echo t('Handle')?>: <?php echo $key->getAttributeKeyHandle(); ?>">
                    <?=$formatter->getListIconElement()?>
                    <?=$key->getAttributeKeyDisplayName()?>
                </a>
                <?php if ($enableSorting) { ?>
                    <i class="ccm-item-select-list-sort"></i>
                <?php } ?>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}

if (count($unassigned)) {
    if (count($sortable_sets)) {
        ?><h4><?=t('Other')?></h4><?php
    }
    ?>
    <ul class="item-select-list ccm-attribute-list-wrapper">
        <?php
        foreach ($unassigned as $key) {
            $controller = $key->getController();
            $formatter = $controller->getIconFormatter();
            ?>
            <li class="ccm-attribute" id="akID_<?=$key->getAttributeKeyID()?>">
                <a href="<?=$view->controller->getEditAttributeKeyURL($key)?>" title="<?php echo t('Handle')?>: <?php echo $key->getAttributeKeyHandle(); ?>">
                    <?=$formatter->getListIconElement()?>
                    <?=$key->getAttributeKeyDisplayName()?>
                </a>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
}

if ($enableSorting) {
    ?>
    <script type="text/javascript">
        $(function() {
            $("ul[data-sortable-attribute-set]").sortable({
                handle: 'i.ccm-item-select-list-sort',
                cursor: 'move',
                opacity: 0.5,
                stop: function() {
                    var req = $(this).sortable('serialize');
                    req += '&asID=' + $(this).attr('data-sortable-attribute-set');
                    req += '&ccm_token=' + '<?=Core::make("token")->generate('attribute_sort')?>';
                    $.post('<?=$view->controller->getSortAttributeCategoryURL()?>', req, function(r) {});
                }
            });
        });
    </script>
    <?php
}

if (isset($types) && is_array($types) && count($types) > 0) {
    ?>
    <h3><?=t('Add Attribute')?></h3>
    <div class="btn-group">
        <button type="button" class="btn btn-secondary dropdown-toggle" data-button="attribute-type" data-bs-toggle="dropdown">
            <?=t('Choose Type')?> <span class="caret"></span>
        </button>
        <div class="dropdown-menu">
            <?php
            foreach ($types as $type) {
                $controller = $type->getController();
                $formatter = $controller->getIconFormatter();
                /* @var \Concrete\Core\Attribute\IconFormatterInterface $formatter */
                ?>
                <a class="dropdown-item" href="<?=$view->controller->getAddAttributeTypeURL($type)?>"><?=$formatter->getListIconElement()?> <?=$type->getAttributeTypeDisplayName()?></a>
                <?php
            }
            ?>
        </div>
    </div>

    <script type="text/javascript">
        $(function() {
            var documentHeight = $(document).height(),
                position = $('button[data-button=attribute-type]').offset().top;

            if ((documentHeight > 200) && ((documentHeight - position) < 200)) {
                $('button[data-button=attribute-type]').parent().addClass('dropup');
            }

        });
    </script>
    <?php
}
