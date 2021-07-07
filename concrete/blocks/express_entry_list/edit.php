<?php /** @noinspection PhpDeprecationInspection */
/** @noinspection PhpComposerExtensionStubsInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Controller\Element\Search\CustomizeResults;
use Concrete\Controller\Element\Search\SearchFieldSelector;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Entity\Express\Entity;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;

/** @var CustomizeResults $customizeElement */
/** @var SearchFieldSelector $searchFieldSelectorElement */
/** @var array $linkedPropertiesSelected */
/** @var array $searchPropertiesSelected */
/** @var array $searchProperties */
/** @var array $searchAssociationsSelected */
/** @var array $searchAssociations */

/** @var Entity[] $entities */
/** @var string $exEntityID */
/** @var int $detailPage */
/** @var string $linkedProperties */
/** @var string $searchProperties */
/** @var string $searchAssociations */
/** @var string $columns */
/** @var string $filterFields */
/** @var int $displayLimit */
/** @var int $enableSearch */
/** @var int $enableKeywordSearch */
/** @var string $headerBackgroundColor */
/** @var string $headerBackgroundColorActiveSort */
/** @var string $headerTextColor */
/** @var string $tableName */
/** @var string $tableDescription */
/** @var bool $tableStriped */
/** @var string $rowBackgroundColorAlternate */

$app = Application::getFacadeApplication();
/** @var Color $color */
$color = $app->make(Color::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var UserInterface $userInterface */
$userInterface = $app->make(UserInterface::class);
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);

echo $userInterface->tabs([
    ['search', t('Source & Search'), true],
    ['filtering', t('Filtering')],
    ['results', t('Results')],
    ['design', t('Design')]
]);

?>

<div class="tab-content">
    <div class="tab-pane show active" id="search" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label('exEntityID', t('Entity')) ?>
            <?php echo $form->select('exEntityID', $entities, $exEntityID, [
                'data-action' => $view->action('load_entity_data')
            ]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->checkbox('enableSearch', 1, $enableSearch, ['data-options-toggle' => 'search']) ?>
            <?php echo $form->label('enableSearch', t('Enable Search'), ["class" => "form-check-label"]) ?>
        </div>

        <div data-options="search">
            <hr>
            <div class="form-group">
                <div class="form-check">
                    <?php echo $form->checkbox('enableKeywordSearch', 1, $enableKeywordSearch) ?>
                    <?php echo $form->label('enableKeywordSearch', t('Search by Keywords'), ["class" => "form-check-label"]) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("", t("Enable Searching by Attributes")); ?>

                <div data-container="advanced-search"></div>
            </div>

            <div class="form-group">
                <?php echo $form->label("", t("Enable Searching by Associations")); ?>

                <div data-container="search-associations"></div>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="filtering" role="tabpanel">
        <div data-container="search-field-selector">
            <?php if ($searchFieldSelectorElement) { ?>
                <?php $searchFieldSelectorElement->render() ?>
            <?php } else { ?>
                <?php echo t('You must choose an entity before you can customize its filtering.') ?>
            <?php } ?>
        </div>
    </div>

    <div class="tab-pane" id="results" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label('displayLimit', t('Items Per Page')) ?>
            <?php echo $form->text('displayLimit', $displayLimit) ?>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox('enablePagination', 1, $enablePagination) ?>
                <?php echo $form->label('enablePagination', t('Display pagination interface in results.'), ["class" => "form-check-label"]) ?>
            </div>
            <div class="form-check">
                <?php echo $form->checkbox('enableItemsPerPageSelection', 1, $enableItemsPerPageSelection) ?>
                <?php echo $form->label('enableItemsPerPageSelection', t('Allow users to select items per page.'), ["class" => "form-check-label"]) ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label('detailPage', t('Link to Detail Page')) ?>
            <?php echo $pageSelector->selectPage('detailPage', $detailPage) ?>

            <div data-container="linked-attributes"></div>
        </div>

        <div data-container="customize-results">
            <?php if ($customizeElement) { ?>
                <?php $customizeElement->render() ?>
            <?php } else { ?>
                <?php echo t('You must choose an entity before you can customize its search results.') ?>
            <?php } ?>
        </div>
    </div>

    <div class="tab-pane" id="design" role="tabpanel">
        <div>
            <div class="form-group">
                <?php echo $form->label('tableName', t('Name')) ?>
			    <div class="input-group">
                	<?php echo $form->text('tableName', $tableName, array('maxlength' => '128')) ?>
					<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
				</div>
			</div>

            <div class="form-group">
                <?php echo $form->label('tableDescription', t('Description')) ?>
                <?php echo $form->text('tableDescription', $tableDescription, array('maxlength' => '128')) ?>
            </div>
        </div>

        <div>
            <legend>
                <?php echo t('Design') ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label('headerBackgroundColor', t('Header Background')) ?>

                <div>
                    <?php $color->output('headerBackgroundColor', $headerBackgroundColor) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('headerBackgroundColorActiveSort', t('Header Background (Active Sort)')) ?>

                <div>
                    <?php $color->output('headerBackgroundColorActiveSort', $headerBackgroundColorActiveSort) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('headerTextColor', t('Header Text Color')) ?>

                <div>
                    <?php $color->output('headerTextColor', $headerTextColor) ?>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label('', t('Table Striping')) ?>

                <div class="form-check">
                    <?php echo $form->radio('tableStriped', 0, $tableStriped, ["name" => "tableStriped", "id" => "tableStripedOff"]) ?>
                    <?php echo $form->label('tableStripedOff', t('Off (all rows the same color)'), ["class" => "form-check-label"]) ?>
                </div>

                <div class="form-check">
                    <?php echo $form->radio('tableStriped', 1, $tableStriped, ["name" => "tableStriped", "id" => "tableStripedOn"]) ?>
                    <?php echo $form->label('tableStripedOn', t('On (change color of alternate rows)'), ["class" => "form-check-label"]) ?>
                </div>
            </div>

            <div class="form-group" data-options="table-striped" style="margin-bottom: 150px;">
                <?php echo $form->label('rowBackgroundColorAlternate', t('Alternate Row Background Color')) ?>

                <div>
                    <?php $color->output('rowBackgroundColorAlternate', $rowBackgroundColorAlternate) ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/template" data-template="express-attribute-search-list">
    <% _.each(attributes, function(attribute) { %>
    <div class="form-check">
        <input id="searchProperties_<%=attribute.akID%>" type="checkbox" name="searchProperties[]"
               class="form-check-input" value="<%=attribute.akID%>"
        <% var akID = attribute.akID + ''; %>
        <% if (_.contains(selected, akID)) { %> checked<% } %>>
        <label for="searchProperties_<%=attribute.akID%>" class="form-check-label">
            <%=attribute.akName%>
        </label>
    </div>
    <% }); %>
</script>

<script type="text/template" data-template="express-association-search-list">
    <% _.each(associations, function(association) { %>
    <div class="form-check">
        <input id="searchAssociations_<%=association.associationID%>" type="checkbox" name="searchAssociations[]"
               class="form-check-input" value="<%=association.associationID%>"
        <% var associationID = association.associationID + ''; %>
        <% if (_.contains(selected, associationID)) { %> checked<% } %>>
        <label for="searchAssociations_<%=association.associationID%>" class="form-check-label">
            <%=association.associationName%>
        </label>
    </div>
    <% }); %>
</script>

<script type="text/template" data-template="express-attribute-link-list">
    <% _.each(attributes, function(attribute) { %>
    <div class="form-check">
        <input id="linkedProperties_<%=attribute.akID%>" type="checkbox" name="linkedProperties[]"
               class="form-check-input" value="<%=attribute.akID%>"
        <% var akID = attribute.akID + ''; %>
        <% if (_.contains(selected, akID)) { %> checked<% } %>>
        <label for="linkedProperties_<%=attribute.akID%>" class="form-check-label">
            <%=attribute.akName%>
        </label>
    </div>
    <% }); %>
</script>

<script>
    $(function(){
        Concrete.event.publish('block.express_entry_list.open', {
            'searchProperties': <?php echo json_encode($searchProperties)?>,
            'searchPropertiesSelected': <?php echo json_encode($searchPropertiesSelected)?>,
            'searchAssociations': <?php echo json_encode($searchAssociations)?>,
            'searchAssociationsSelected': <?php echo json_encode($searchAssociationsSelected)?>,
            'linkedPropertiesSelected': <?php echo json_encode($linkedPropertiesSelected)?>
        });
    });
</script>
