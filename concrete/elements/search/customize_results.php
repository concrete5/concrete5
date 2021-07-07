<?php
defined('C5_EXECUTE') or die("Access Denied.");
/**
 * @var $provider \Concrete\Core\User\Search\SearchProvider
 */
$available = $provider->getAvailableColumnSet();
$current = isset($query) ? $query->getColumns() : $provider->getDefaultColumnSet();
$all = $provider->getAllColumnSet();
$list = $provider->getCustomAttributeKeys();
$itemsPerPageOptions = $provider->getItemsPerPageOptions();
$itemsPerPage = isset($query) ? $query->getItemsPerPage() : $provider->getItemsPerPage();
$form = Core::make('helper/form');

if (!isset($type)) {
    $type = null;
}
?>

<section data-section="customize-results">

    <fieldset>
        <legend class="mb-3"><?= t('Choose Columns') ?></legend>

        <?php
        if (count($available->getColumns())) {
            ?>
            <div class="form-group">
                <label class="control-label form-label"><?= t('Standard Properties') ?></label>
                <?php
                $columns = $available->getColumns();
                foreach ($columns as $col) {
                    ?>
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               name="<?= $col->getColumnKey() ?>"
                               id="<?= $col->getColumnKey() ?>"
                               value="1"
                               <?php if ($current->contains($col)) { ?>checked<?php } ?>>
                        <label class="form-check-label"
                               for="<?= $col->getColumnKey() ?>"><?= $col->getColumnName() ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>

        <?php
        if (count($list)) {
            ?>
            <div class="form-group">
                <label class="control-label form-label"><?= t('Custom Attributes') ?></label>
                <?php
                foreach ($list as $ak) {
                    ?>
                    <div class="form-check">
                        <input type="checkbox"
                               class="form-check-input"
                               name="<?= 'ak_' . $ak->getAttributeKeyHandle() ?>"
                               id="<?= 'ak_' . $ak->getAttributeKeyHandle() ?>"
                               value="1"
                               <?php if ($current->contains($ak)) { ?>checked<?php } ?>>
                        <label class="form-check-label"
                               for="<?= 'ak_' . $ak->getAttributeKeyHandle() ?>"><?= $ak->getAttributeKeyDisplayName() ?></label>
                    </div>
                    <?php
                }
                ?>
            </div>
            <?php
        }
        ?>
    </fieldset>
    <hr>
    <fieldset>
        <legend><?= t('Column Order') ?></legend>

        <div class="form-group">
            <p><?= t('Click and drag to change column order.') ?></p>
            <ul class="item-select-list" data-search-column-list="<?= $type ?>">
                <?php
                foreach ($current->getColumns() as $col) {
                    ?>
                    <li style="cursor: move" data-field-order-column="<?= $col->getColumnKey() ?>"><input type="hidden"
                                                                                                          name="column[]"
                                                                                                          value="<?= $col->getColumnKey() ?>"/><?= $col->getColumnName() ?>
                        <i class="ccm-item-select-list-sort ui-sortable-handle"></i>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </fieldset>
    <hr>
    <fieldset>
        <legend><?= t('Sort By') ?></legend>

        <?php $ds = $current->getDefaultSortColumn(); ?>

        <div class="form-group">
            <label class="control-label form-label" for="fSearchDefaultSort"><?= t('Default Column') ?></label>
            <select <?php if (count($all->getSortableColumns()) == 0) { ?> disabled="disabled"<?php } ?>
                    class="form-select" data-search-select-default-column="<?= $type ?>" id="fSearchDefaultSort"
                    name="fSearchDefaultSort">
                <?php
                foreach ($all->getSortableColumns() as $col) {
                    ?>
                    <option id="<?= $col->getColumnKey() ?>"
                            value="<?= $col->getColumnKey() ?>" <?php if ($ds && $col->getColumnKey() == $ds->getColumnKey()) { ?> selected="selected" <?php } ?>><?= $col->getColumnName() ?></option>
                    <?php
                }
                ?>
            </select>
        </div>

        <div class="form-group">
            <label class="control-label form-label" for="fSearchDefaultSortDirection"><?= t('Direction') ?></label>
            <select <?php if (count($all->getSortableColumns()) == 0) { ?> disabled="disabled"<?php } ?>
                    class="form-select" data-search-select-default-column-direction="<?= $type ?>"
                    name="fSearchDefaultSortDirection">
                <option
                        value="asc" <?php if (is_object($ds) && $ds->getColumnDefaultSortDirection() == 'asc') { ?> selected="selected"<?php } ?>><?= t('Ascending') ?></option>
                <option
                        value="desc" <?php if (is_object($ds) && $ds->getColumnDefaultSortDirection() == 'desc') { ?> selected="selected"<?php } ?>><?= t('Descending') ?></option>
            </select>
        </div>

    </fieldset>

    <?php if ($includeNumberOfResults) { ?>
        <hr>

        <fieldset>
            <legend><?= t('Number of Results') ?></legend>

            <div class="form-group">
                <select class="form-select" name="fSearchItemsPerPage">
                    <?php
                    foreach ($itemsPerPageOptions as $option) {
                        ?>
                        <option <?php if ($itemsPerPage == $option) { ?> selected="selected"<?php } ?>
                                value="<?= $option ?>">
                            <?= $option ?>
                        </option>
                        <?php
                    }
                    ?>
                </select>
            </div>
        </fieldset>

    <?php } ?>

</section>

<script type="text/javascript">
    $(function () {
        var $form = $('section[data-section=customize-results]'),
            $columns = $form.find('ul[data-search-column-list]');

        $('ul[data-search-column-list]').sortable({
            cursor: 'move',
            opacity: 0.5
        });
        $form.on('click', 'input[type=checkbox]', function () {
            var label = $(this).parent().find('label').html(),
                id = $(this).attr('id');

            if ($(this).prop('checked')) {
                if ($form.find('li[data-field-order-column=\'' + id + '\']').length == 0) {
                    $columns.append('<li data-field-order-column="' + id + '"><input type="hidden" name="column[]" value="' + id + '" />' + label + '<i class="ccm-item-select-list-sort ui-sortable-handle"></i><\/li>');
                }
            } else {
                $columns.find('li[data-field-order-column=\'' + id + '\']').remove();
            }
        });
    });
</script>
