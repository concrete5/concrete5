<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<?php
$color = Core::make('helper/form/color');
echo Core::make('helper/concrete/ui')->tabs(array(
    array('search', t('Source & Search'), true),
    array('filtering', t('Filtering')),
    array('results', t('Results')),
    array('design', t('Design'))
));
?>

<div class="ccm-tab-content" id="ccm-tab-content-search">

    <div class="form-group">
        <?=$form->label('exEntityID', t('Entity'))?>
        <?=$form->select('exEntityID', $entities, $exEntityID, [
            'data-action' => $view->action('load_entity_data')
        ]);?>
    </div>

    <div class="form-group">
        <div class="checkbox"><label>
                <?=$form->checkbox('enableSearch', 1, $enableSearch, array('data-options-toggle' => 'search'))?>
                <?=t('Enable Search')?>
            </label>
        </div>
    </div>

    <fieldset data-options="search">

        <div class="form-group">
            <label class="control-label"><?=t("Keyword Search")?></label>
            <div class="checkbox"><label>
                    <?=$form->checkbox('enableKeywordSearch', 1, $enableKeywordSearch)?>
                    <?=t('Search by Keywords')?>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?=t("Enable Searching by Attributes")?></label>

            <div data-container="advanced-search">

            </div>
        </div>

        <div class="form-group">
            <label class="control-label"><?=t("Enable Searching by Associations")?></label>

            <div data-container="search-associations">

            </div>
        </div>
    </fieldset>

</div>

<div class="ccm-tab-content" id="ccm-tab-content-filtering">

    <div data-container="search-field-selector">
        <?php if ($searchFieldSelectorElement) { ?>
            <?php $searchFieldSelectorElement->render() ?>
        <?php } else {  ?>
            <?=t('You must choose an entity before you can customize its filtering.') ?>
        <?php } ?>
    </div>


</div>

<div class="ccm-tab-content" id="ccm-tab-content-results">

    <div class="form-group">
        <?=$form->label('displayLimit', t('Items Per Page'))?>
        <?=$form->text('displayLimit', $displayLimit)?>
    </div>

    <div class="form-group">
        <label class="control-label" for="detailPage"><?=t("Link to Detail Page")?></label>
        <?=Core::make('helper/form/page_selector')->selectPage('detailPage', $detailPage)?>

        <div data-container="linked-attributes">

        </div>
    </div>


    <div data-container="customize-results">
        <?php if ($customizeElement) { ?>
            <?php $customizeElement->render() ?>
        <?php } else {  ?>
            <?=t('You must choose an entity before you can customize its search results.') ?>
        <?php } ?>
    </div>
</div>

<div class="ccm-tab-content" id="ccm-tab-content-design">

    <fieldset>
        <div class="form-group">
            <?=$form->label('tableName', t('Name'))?>
            <?=$form->text('tableName', $tableName, array('maxlength' => '128'))?>
        </div>
        <div class="form-group">
            <?=$form->label('tableDescription', t('Description'))?>
            <?=$form->text('tableDescription', $tableDescription, array('maxlength' => '128'))?>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Design')?></legend>
        <div class="form-group">
            <?=$form->label('headerBackgroundColor', t('Header Background'))?>
            <div>
                <?=$color->output('headerBackgroundColor', $headerBackgroundColor)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('headerBackgroundColorActiveSort', t('Header Background (Active Sort)'))?>
            <div>
                <?=$color->output('headerBackgroundColorActiveSort', $headerBackgroundColorActiveSort)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('headerTextColor', t('Header Text Color'))?>
            <div>
                <?=$color->output('headerTextColor', $headerTextColor)?>
            </div>
        </div>
        <div class="form-group">
            <?=$form->label('', t('Table Striping'))?>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 0, $tableStriped)?>
                    <?=t('Off (all rows the same color)')?>
                </label>
            </div>
            <div class="radio">
                <label>
                    <?=$form->radio('tableStriped', 1, $tableStriped)?>
                    <?=t('On (change color of alternate rows)')?>
                </label>
            </div>
        </div>

        <div class="form-group" data-options="table-striped" style="margin-bottom: 150px;">
            <?=$form->label('rowBackgroundColorAlternate', t('Alternate Row Background Color'))?>
            <div>
                <?=$color->output('rowBackgroundColorAlternate', $rowBackgroundColorAlternate)?>
            </div>
        </div>
    </fieldset>

</div>

<script type="text/template" data-template="express-attribute-search-list">
    <% _.each(attributes, function(attribute) { %>
    <div class="checkbox"><label>
        <input type="checkbox" name="searchProperties[]" value="<%=attribute.akID%>"
           <% var akID = attribute.akID + ''; %>
            <% if (_.contains(selected, akID)) { %> checked<% } %>>
        <%=attribute.akName%>
        </label>
    </div>
    <% }); %>
</script>

<script type="text/template" data-template="express-association-search-list">
    <% _.each(associations, function(association) { %>
    <div class="checkbox"><label>
            <input type="checkbox" name="searchAssociations[]" value="<%=association.associationID%>"
            <% var associationID = association.associationID + ''; %>
            <% if (_.contains(selected, associationID)) { %> checked<% } %>>
            <%=association.associationName%>
        </label>
    </div>
    <% }); %>
</script>

<script type="text/template" data-template="express-attribute-link-list">
    <% _.each(attributes, function(attribute) { %>
    <div class="checkbox"><label>
            <input type="checkbox" name="linkedProperties[]" value="<%=attribute.akID%>"
            <% var akID = attribute.akID + ''; %>
            <% if (_.contains(selected, akID)) { %> checked<% } %>>
            <%=attribute.akName%>
        </label>
    </div>
    <% }); %>
</script>


<script type="application/javascript">
    Concrete.event.publish('block.express_entry_list.open', {
        'searchProperties': <?=json_encode($searchProperties)?>,
        'searchPropertiesSelected': <?=json_encode($searchPropertiesSelected)?>,
        'searchAssociations': <?=json_encode($searchAssociations)?>,
        'searchAssociationsSelected': <?=json_encode($searchAssociationsSelected)?>,
        'linkedPropertiesSelected': <?=json_encode($linkedPropertiesSelected)?>
    });
</script>
