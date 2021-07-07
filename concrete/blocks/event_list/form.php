<?php /** @noinspection PhpDeprecationInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Calendar\Calendar;
use Concrete\Core\Entity\Attribute\Key\PageKey;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\View\View;

/** @var array $attributeKeys */
/** @var PageKey[] $pageAttributeKeys */
/** @var Calendar[] $calendars */
/** @var object|null $featuredAttribute */
/** @var string $filterByTopic */
/** @var array $caID */

/** @var string $calendarAttributeKeyHandle */
/** @var int $totalToRetrieve */
/** @var int $totalPerPage */
/** @var int $filterByTopicAttributeKeyID */
/** @var int $filterByTopicID */
/** @var string $filterByPageTopicAttributeKeyHandle */
/** @var bool $filterByFeatured */
/** @var string $eventListTitle */
/** @var int $linkToPage */

$app = Application::getFacadeApplication();
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
/** @var Form $form */
$form = $app->make(Form::class);

if (count($pageAttributeKeys)) {
    $pageTopicAttributeKeyHandles = ["" => t('** Select Page Attribute')];

    foreach ($pageAttributeKeys as $attributeKey) {
        $pageTopicAttributeKeyHandles[$attributeKey->getAttributeKeyHandle()] = $attributeKey->getAttributeKeyDisplayName();
    }
}
?>

<fieldset>
    <legend>
        <?php echo t('Data Source') ?>
    </legend>

    <?php /** @noinspection PhpUnhandledExceptionInspection */
    View::element('calendar/block/data_source', [
        'multiple' => true,
        'caID' => $caID,
        'calendarAttributeKeyHandle' => $calendarAttributeKeyHandle
    ]); ?>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Filtering') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('filterByTopic', t('Filter By Topic')); ?>

        <div class="form-check">
            <?php echo $form->radio('filterByTopic', 'none', $filterByTopic, ["id" => 'filterByTopicNone', 'name' => 'filterByTopic']); ?>
            <?php echo $form->label('filterByTopicNone', t('No topic filtering'), ["class" => "form-check-label"]); ?>
        </div>

        <div class="form-check">
            <?php echo $form->radio('filterByTopic', 'specific', $filterByTopic, ["id" => 'filterByTopicSpecific', 'name' => 'filterByTopic']); ?>
            <?php echo $form->label('filterByTopicSpecific', t('Specific Topic'), ["class" => "form-check-label"]); ?>
        </div>

        <?php if (count($pageAttributeKeys)) { ?>
            <div class="form-check">
                <?php echo $form->radio('filterByTopic', 'page_attribute', $filterByTopic, ["id" => 'filterByTopicPageAttribute', 'name' => 'filterByTopic']); ?>
                <?php echo $form->label('filterByTopicPageAttribute', t('Current Page'), ["class" => "form-check-label"]); ?>
            </div>

            <div data-row="page-attribute">
                <div class="form-group">
                    <?php echo $form->text('filterByPageTopicAttributeKeyHandle', $pageTopicAttributeKeyHandles, $filterByPageTopicAttributeKeyHandle); ?>
                </div>
            </div>
        <?php } ?>

        <div data-row="specific-topic">
            <div class="form-group">
                <!--suppress HtmlFormInputWithoutLabel -->
                <select class="form-select" name="filterByTopicAttributeKeyID">
                    <option value="">
                        <?php echo t('** Select Topic Attribute') ?>
                    </option>

                    <?php foreach ($attributeKeys as $ak) { ?>
                        <?php $attributeController = $ak->getController(); ?>

                        <option value="<?php echo h($ak->getAttributeKeyID()) ?>"
                                <?php if ($ak->getAttributeKeyID() == $filterByTopicAttributeKeyID) { ?>selected<?php } ?>
                                data-tree-id="<?php echo h($attributeController->getTopicTreeID()) ?>">
                            <?php echo $ak->getAttributeKeyDisplayName() ?>
                        </option>
                    <?php } ?>
                </select>

                <?php echo $form->hidden("filterByTopicID", $filterByTopicID); ?>

                <div id="ccm-block-event-list-topic-tree-wrapper"></div>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('filterByFeatured', t('Featured Events')); ?>

        <div class="form-check">
            <?php
            $checkboxAttributes = [
                "style" => "vertical-align: middle"
            ];

            if (!is_object($featuredAttribute)) {
                $checkboxAttributes["disabled"] = "disabled";
            }

            echo $form->checkbox('filterByFeatured', $eventListTitle, ($filterByFeatured == 1), $checkboxAttributes);
            echo $form->label('filterByFeatured', t('Display featured events only.'), ["class" => "form-check-label"]);
            ?>
        </div>

        <?php if (!is_object($featuredAttribute)) { ?>
            <div class="alert alert-info">
                <?php echo t('(%s: You must create the "is_featured" event attribute first.)', '<strong>' . t('Note') . '</strong>'); ?>
            </div>
        <?php } ?>
    </div>
</fieldset>

<fieldset>
    <legend>
        <?php echo t('Results') ?>
    </legend>

    <div class="form-group">
        <?php echo $form->label('eventListTitle', t('Title')); ?>
	    <div class="input-group">
        	<?php echo $form->text('eventListTitle', $eventListTitle) ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
		</div>
	</div>

    <div class="form-group">
        <?php echo $form->label('totalToRetrieve', t('Total Number of Events to Retrieve')); ?>
        <?php echo $form->text('totalToRetrieve', $totalToRetrieve); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('totalPerPage', t('Events to Display Per Page')); ?>
        <?php echo $form->text('totalPerPage', $totalPerPage); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('linkToPage', t('Link To More Events Calendar/Page')); ?>
        <?php echo $pageSelector->selectPage('linkToPage', $linkToPage) ?>
    </div>
</fieldset>

<!--suppress EqualityComparisonWithCoercionJS -->
<script type="text/javascript">
    $(function () {
        $('input[name=filterByTopic]').on('change', function () {
            let selected = $('input[name=filterByTopic]:checked').val();
            if (selected == 'page_attribute') {
                $('div[data-row=specific-topic]').hide();
                $('div[data-row=page-attribute]').show();
            } else if (selected == 'specific') {
                $('div[data-row=page-attribute]').hide();
                $('div[data-row=specific-topic]').show();
            } else {
                $('div[data-row=specific-topic]').hide();
                $('div[data-row=page-attribute]').hide();
            }
        }).trigger('change');

        $('select[name=filterByTopicAttributeKeyID]').on('change', function () {
            let $tree = $('#ccm-block-event-list-topic-tree');

            $tree.remove();

            let chosenTree = $(this).find('option:selected').attr('data-tree-id');

            if (!chosenTree) {
                return;
            }

            $('#ccm-block-event-list-topic-tree-wrapper').append($('<div id=ccm-block-event-list-topic-tree>'));

            $tree.concreteTree({
                'treeID': chosenTree,
                'chooseNodeInForm': true,
                <?php if ($filterByTopicID) { ?>
                'selectNodesByKey': [<?php echo intval($filterByTopicID) ?>],
                <?php } ?>
                'onSelect': function (nodes) {
                    if (nodes.length) {
                        $('input[name=filterByTopicID]').val(nodes[0]);
                    } else {
                        $('input[name=filterByTopicID]').val('');
                    }
                }
            });
        }).trigger('change');
    });
</script>
