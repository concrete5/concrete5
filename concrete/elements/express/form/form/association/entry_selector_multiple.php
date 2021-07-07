<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>
<?php
$options = [];
$selectedIDs = [];
if (isset($selectedEntities)) {
    foreach ($selectedEntities as $selectedEntity) {
        $options[] = [
            'exEntryID' => $selectedEntity->getID(),
            'label' => $selectedEntity->getLabel(),
        ];
        $selectedIDs[] = $selectedEntity->getID();
    }
}
?>
<div class="form-group">
    <?php if ($view->supportsLabel()) {
    ?>
        <label class="control-label form-label" for="<?=$view->getControlID(); ?>"><?=$label; ?></label>
    <?php
} ?>
    <?php if ($view->isRequired()) { ?>
        <span class="text-muted small"><?=t('Required')?></span>
    <?php } ?>

    <input data-select-and-add="<?= $control->getId(); ?>" style="width: 100%;display: none" name="express_association_<?= $control->getId(); ?>" value="" />
</div>

<script type="text/javascript">
    $(function() {
        $('input[data-select-and-add=<?= $control->getId(); ?>]').selectize({
            plugins: ['remove_button'],
            valueField: 'exEntryID',
            labelField: 'label',
            searchField: 'label',
            create: false,
            delimiter: ',',
            maxItems: 500,
            options: <?= json_encode($options); ?>,
            items: <?= json_encode($selectedIDs); ?>,
            load: function(query, callback) {
                if (!query.length) return callback();
                $.ajax({
                    url: "<?= \URL::to('/ccm/system/express/entry/get_json'); ?>",
                    data: {
                        'exEntityID': '<?= $control->getAssociation()->getTargetEntity()->getID(); ?>',
                        'keyword': query
                    },
                    dataType: 'json',
                    error: function() {
                        callback();
                    },
                    success: function(res) {
                        callback(res.entries.slice(0, 10));
                    }
                });
            }
        });
    });
</script>
