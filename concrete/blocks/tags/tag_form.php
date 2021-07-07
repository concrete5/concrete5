<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Block\Tags\Controller;
use Concrete\Core\Attribute\Context\DashboardFormContext;
use Concrete\Core\Entity\Attribute\Key\Key;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\PageSelector;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Application;

/** @var Controller $controller */
/** @var string $title */
/** @var int $targetCID */
/** @var string $displayMode */
/** @var int $cloudCount */
/** @var bool $inStackDashboardPage */
/** @var Key|null $ak */

$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
$c = Page::getCurrentPage();
/** @var PageSelector $pageSelector */
$pageSelector = $app->make(PageSelector::class);
?>

<?php if (!$ak instanceof Key) { ?>
    <div class="alert alert-danger">
        <?php echo t('Error: The required page attribute with the handle of: "%s" doesn\'t exist', $controller->attributeHandle) ?>
    </div>
<?php } else { ?>
    <?php echo $form->hidden("attributeHandle", $controller->attributeHandle); ?>

    <div class="form-group">
        <?php echo $form->label("title", t('Title')); ?>
	    <div class="input-group">
		    <?php echo $form->text('title', $title); ?>
			<?php echo $form->select('titleFormat', \Concrete\Core\Block\BlockController::$btTitleFormats, $titleFormat, array('style' => 'width:105px;flex-grow:0;', 'class' => 'form-select')); ?>
		</div>
	</div>

    <div class="form-group">
        <label class="control-label form-label">
            <?php echo t('Display a List of Tags From') ?>
        </label>

        <div class="form-check">
            <label for="displayModePage" class="form-check-label">
                <?php echo $form->radio('displayMode', 'page', $displayMode, ["id" => "displayModePage", "name" => "displayMode"]) ?>

                <?php echo t('The Current Page.') ?>
            </label>
        </div>

        <div class="form-check">
            <label for="displayModeCloud" class="form-check-label">
                <?php echo $form->radio('displayMode', 'cloud', $displayMode, ["id" => "displayModeCloud", "name" => "displayMode"]) ?>

                <?php echo t('The Entire Site.') ?>
            </label>
        </div>
    </div>

    <?php if (!$inStackDashboardPage) { ?>
        <div id="ccm-tags-display-page" class="form-group">
            <label class="control-label form-label">
                <?php echo $ak->getAttributeKeyDisplayName(); ?>
            </label>

            <?php
            $av = $c->getAttributeValueObject($ak);
            /** @noinspection PhpDeprecationInspection */
            $ak->render(new DashboardFormContext(), $av);
            ?>
        </div>
    <?php } ?>

    <div id="ccm-tags-display-cloud" class="form-group">
        <?php echo $form->label('cloudCount', t('Number to Display')) ?>
        <?php echo $form->text('cloudCount', $cloudCount, ['size' => 4]) ?>
    </div>

    <div class="form-group">
        <?php echo $form->label("targetCID", t('Link Tags to Filtered Page List')); ?>
        <?php echo $pageSelector->selectPage('targetCID', $targetCID); ?>
    </div>

    <script>
        $(function () {
            tags.init();
        });
    </script>
<?php } ?>
