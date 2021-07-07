<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Block\PageAttributeDisplay\Controller;
use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Support\Facade\Application;

/** @var Controller $controller */
/** @var string $displayTag */
/** @var string $dateFormat */
/** @var string $delimiter */
/** @var int $thumbnailWidth */
/** @var int $thumbnailHeight */
/** @var string $attributeTitleText */
/** @var string $attributeHandle */

$app = Application::getFacadeApplication();

/** @var Form $form */
$form = $app->make(Form::class);
/** @var UserInterface $ui */
$ui = $app->make(UserInterface::class);

$pageAttributes = [];

foreach ($controller->getAvailableAttributes() as $ak) {
    $pageAttributes[$ak->getAttributeKeyHandle()] = $ak->getAttributeKeyDisplayName();
}

echo $ui->tabs([
    ['add', t('Add'), true],
    ['options', t('Options')]
]);
?>

<div class="tab-content">
    <div class="tab-pane show active" id="add" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label("attributeHandle", t('Property to Display')); ?>
            <?php echo $form->select("attributeHandle", [
                t('Page Values') => $controller->getAvailablePageValues(),
                t('Page Attributes') => $pageAttributes
            ], $attributeHandle); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("attributeTitleText", t('Title Text')); ?>
            <?php echo $form->text("attributeTitleText", $attributeTitleText); ?>
        </div>
    </div>

    <div class="tab-pane" id="options" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label("displayTag", t('Display Property with Formatting')); ?>
            <?php echo $form->select("displayTag", [
                "h1" => t("H1 (Heading 1)"),
                "h2" => t("H2 (Heading 2)"),
                "h3" => t("H3 (Heading 3)"),
                "p" => t("p (paragraph)"),
                "b" => t("b (bold)"),
                "address" => t("address"),
                "pre" => t("pre (preformatted)"),
                "blockquote" => t("blockquote"),
                "div" => t("div")
            ], $displayTag); ?>
        </div>

        <div class="form-group">
            <?php echo $form->label("dateFormat", t('Format of Date Properties')); ?>
            <?php echo $form->text("dateFormat", $dateFormat); ?>

            <div class="help-block">
                <?php echo sprintf(t('See the formatting options for date at %s.'), '<a href="http://www.php.net/date" target="_blank">php.net/date</a>'); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label("delimiter", t('Delimiter for Multiple Items')); ?>
            <?php echo $form->select("delimiter", [
                "comma" => t('Comma (",")'),
                "commaSpace" => t('Comma With Space After (", ")'),
                "pipe" => t('Pipe ("|")'),
                "dash" => t('Dash ("-")'),
                "semicolon" => t('Semicolon (";")'),
                "semicolonSpace" => t('Semicolon With Space After ("; ")'),
                "break" => t("Newline")
            ], $delimiter); ?>
        </div>

        <fieldset>
            <legend>
                <?php echo t('Thumbnail') ?>
            </legend>

            <div class="form-group">
                <?php echo $form->label("thumbnailWidth", t('Max Width')); ?>

                <div class="input-group">
                    <?php echo $form->number('thumbnailWidth', $thumbnailWidth, ["min" => 0, "step" => 1]); ?>

                    <span class="input-group-text">
                        <?php echo t('px'); ?>
                    </span>
                </div>
            </div>

            <div class="form-group">
                <?php echo $form->label("thumbnailHeight", t('Max Height')); ?>

                <div class="input-group">
                    <?php echo $form->number('thumbnailHeight', $thumbnailHeight, ["min" => 0, "step" => 1]); ?>

                    <span class="input-group-text">
                        <?php echo t('px'); ?>
                    </span>
                </div>
            </div>
        </fieldset>
    </div>
</div>
