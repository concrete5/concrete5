<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div data-component="search-field-selector" class="ccm-search-field-selector">

    <div class="form-group">
        <button class="btn btn-primary" type="button" data-button-action="add-field"><?=t('Add Field')?></button>
    </div>
    <!-- <hr/> -->
    <div data-container="search-fields" class="ccm-search-fields-advanced">

    </div>

    <script type="text/template" data-template="search-field-row">
        <div class="ccm-search-field-selector-row">
            <select data-action="<?=$addFieldAction?>" name="field[]" class="ccm-search-field-selector-choose form-control">
                <option value=""><?=t('** Select Field')?></option>
                <?php foreach($manager->getGroups() as $group) { ?>
                    <optgroup label="<?=$group->getName()?>">
                        <?php foreach($group->getFields() as $field) { ?>
                            <option value="<?=$field->getKey()?>" <% if (typeof(field) != 'undefined' && field.key == '<?=$field->getKey()?>') { %> selected <% } %>><?=$field->getDisplayName()?></option>
                        <?php } ?>
                    </optgroup>
                <?php } ?>
            </select>
            <div class="form-group"><% if (typeof(field) != 'undefined') { %><%=field.element%><% } %></div>
            <a data-search-remove="search-field" class="ccm-search-remove-field" href="#"><i class="fa fa-minus-circle"></i></a>
        </div>
    </script>

    <?php if (isset($query)) { ?>
        <script type="text/json" data-template="default-query">
            <?=json_encode($query)?>
        </script>
    <?php } ?>
</div>
