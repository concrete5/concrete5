<?php /** @noinspection DuplicatedCode */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\FileManager;
use Concrete\Core\Block\CustomStyle;
use Concrete\Core\Entity\StyleCustomizer\Inline\StyleSet;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Form\Service\Widget\Color;
use Concrete\Core\Page\Theme\GridFramework\GridFramework;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\View\View;

if (isset($displayBlockContainerSettings)) {
    $displayBlockContainerSettings = (bool)$displayBlockContainerSettings;
}

$backgroundColor = '';
$image = false;
$baseFontSize = '';
$backgroundRepeat = 'no-repeat';
$backgroundSize = 'auto';
$backgroundPosition = 'left top';
$textColor = '';
$linkColor = '';
$marginTop = '';
$marginLeft = '';
$marginRight = '';
$marginBottom = '';
$paddingTop = '';
$paddingLeft = '';
$paddingRight = '';
$paddingBottom = '';
$borderStyle = '';
$borderWidth = '';
$borderColor = '';
$borderRadius = '';
$alignment = '';
$rotate = '';
$boxShadowHorizontal = '';
$boxShadowVertical = '';
$boxShadowBlur = '';
$boxShadowSpread = '';
$boxShadowColor = '';
$customClass = '';
$customID = '';
$customElementAttribute = '';
/** @var string $resetAction */
/** @var string $bFilename */
/** @var string $canEditCustomTemplate */
/** @var string $saveAction */
/** @var CustomStyle $style */
/** @var StyleSet $set */
$set = $style->getStyleSet();

if (is_object($set)) {
    $backgroundColor = $set->getBackgroundColor();
    $textColor = $set->getTextColor();
    $linkColor = $set->getLinkColor();
    $image = $set->getBackgroundImageFileObject();
    $backgroundRepeat = $set->getBackgroundRepeat();
    $backgroundSize = $set->getBackgroundSize();
    $backgroundPosition = $set->getBackgroundPosition();
    $baseFontSize = $set->getBaseFontSize();
    $marginTop = $set->getMarginTop();
    $marginLeft = $set->getMarginLeft();
    $marginRight = $set->getMarginRight();
    $marginBottom = $set->getMarginBottom();
    $paddingTop = $set->getPaddingTop();
    $paddingLeft = $set->getPaddingLeft();
    $paddingRight = $set->getPaddingRight();
    $paddingBottom = $set->getPaddingBottom();
    $borderStyle = $set->getBorderStyle();
    $borderWidth = $set->getBorderWidth();
    $borderColor = $set->getBorderColor();
    $borderRadius = $set->getBorderRadius();
    $alignment = $set->getAlignment();
    $rotate = $set->getRotate();
    $boxShadowHorizontal = $set->getBoxShadowHorizontal();
    $boxShadowVertical = $set->getBoxShadowVertical();
    $boxShadowBlur = $set->getBoxShadowBlur();
    $boxShadowSpread = $set->getBoxShadowSpread();
    $boxShadowColor = $set->getBoxShadowColor();
    $customClass = $set->getCustomClass();
    $customID = $set->getCustomID();
    $customElementAttribute = $set->getCustomElementAttribute();
}

$enableBlockContainerOptions = [
    -1 => t('Default Setting'),
    0 => t('Disable Grid Container'),
    1 => t('Enable Grid Container')
];

if ($style instanceof CustomStyle && $canEditCustomTemplate) {
    $customTemplateOptions = [
        "" => t('Default')
    ];

    foreach ($templates as $tpl) {
        $customTemplateOptions[$tpl->getTemplateFileFilename()] = $tpl->getTemplateFileDisplayName();
    }
}

$deviceHideClasses = [];
/* @var $gf GridFramework */
if (is_object($gf)) {
    $deviceHideClasses = $gf->getPageThemeGridFrameworkDeviceHideClasses();
}

$al = new FileManager();
$app = Application::getFacadeApplication();
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Color $color */
$color = $app->make(Color::class);
/** @var Identifier $identifier */
$identifier = $app->make(Identifier::class);

?>

<form method="post" action="<?php echo h($saveAction) ?>" id="ccm-inline-design-form"
      data-target-element="<?php echo ($style instanceof CustomStyle) ? "block" : "area"; ?>">

    <ul class="ccm-style-customizer-toolbar ccm-ui">
        <?php if ($style instanceof CustomStyle && $canEditCustomTemplate) { ?>
            <li class="ccm-inline-toolbar-select">
                <div class="form-group">
                    <?php echo $form->label("bFilename", t('Block Template')); ?>
                    <?php echo $form->select("bFilename", $customTemplateOptions, $bFilename, ["class" => "selectpicker form-control"]); ?>
                </div>
            </li>
        <?php } ?>

        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               title="<?php echo h(t('Text Size and Color')); ?>">
                <i class="fa fa-font"></i>
            </a>

            <div class="ccm-dropdown-menu">
                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <div class="form-group">
                                <?php echo $form->label("textColor", t('Text Color')) ?>
                                <?php $color->output('textColor', $textColor); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("linkColor", t('Link Color')) ?>
                                <?php $color->output('linkColor', $linkColor); ?>
                            </div>

                            <hr/>

                            <?php
                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "baseFontSize",
                                "label" => t("Base Font Size"),
                                "value" => $baseFontSize,
                                "valueFormat" => "px"
                            ]);
                            ?>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("alignment", t('Alignment')) ?>
                                <?php echo $form->select('alignment', [
                                    '' => t('None'),
                                    'left' => t('Left'),
                                    'center' => t('Center'),
                                    'right' => t('Right'),
                                ], $alignment, ["class" => "selectpicker form-control"]); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </li>

        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               title="<?php echo h(t('Background Color and Image')); ?>">

                <i class="fa fa-image"></i>
            </a>

            <div class="ccm-dropdown-menu">
                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Background') ?>
                            </legend>

                            <div class="form-group">
                                <?php echo $form->label("backgroundColor", t('Color')) ?>
                                <?php $color->output('backgroundColor', $backgroundColor); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundImageFileID", t('Image')) ?>
                                <?php echo $al->image('backgroundImageFileID', 'backgroundImageFileID', t('Choose Image'), $image); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundRepeat", t('Repeats')) ?>
                                <?php echo $form->select('backgroundRepeat', [
                                    'no-repeat' => t('No Repeat'),
                                    'repeat-x' => t('Horizontally'),
                                    'repeat-y' => t('Vertically'),
                                    'repeat' => t('Horizontally & Vertically'),
                                ], $backgroundRepeat, ["class" => "selectpicker form-control"]); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundSize", t('Size')) ?>
                                <?php echo $form->select('backgroundSize', [
                                    'auto' => t('Auto'),
                                    'contain' => t('Contain'),
                                    'cover' => t('Cover'),
                                    '10%' => t('10%'),
                                    '25%' => t('25%'),
                                    '50%' => t('50%'),
                                    '75%' => t('75%'),
                                    '100%' => t('100%'),
                                ], $backgroundSize, ["class" => "selectpicker form-control"]); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("backgroundPosition", t('Position')) ?>
                                <?php echo $form->select('backgroundPosition', [
                                    'left top' => t('Left Top'),
                                    'left center' => t('Left Center'),
                                    'left bottom' => t('Left Bottom'),
                                    'center top' => t('Center Top'),
                                    'center center' => t('Center Center'),
                                    'center bottom' => t('Center Bottom'),
                                    'right top' => t('Right Top'),
                                    'right center' => t('Right Center'),
                                    'right bottom' => t('Right Bottom'),
                                ], $backgroundPosition, ["class" => "selectpicker form-control"]); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </li>

        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               title="<?php echo h(t('Borders')); ?>">
                <i class="fas fa-border-style"></i>
            </a>

            <div class="ccm-dropdown-menu">
                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Border') ?>
                            </legend>

                            <div class="form-group">
                                <?php echo $form->label("borderColor", t('Color')) ?>
                                <?php $color->output('borderColor', $borderColor); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("borderStyle", t('Style')) ?>
                                <?php echo $form->select('borderStyle', [
                                    '' => t('None'),
                                    'solid' => t('Solid'),
                                    'dotted' => t('Dotted'),
                                    'dashed' => t('Dashed'),
                                    'double' => t('Double'),
                                    'groove' => t('Groove'),
                                    'ridge' => t('Ridge'),
                                    'inset' => t('Inset'),
                                    'outset' => t('Outset'),
                                ], $borderStyle, ["class" => "selectpicker form-control"]); ?>
                            </div>

                            <hr/>

                            <?php
                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "borderWidth",
                                "label" => t("Width"),
                                "value" => $borderWidth,
                                "valueFormat" => "px"
                            ]);
                            ?>

                            <?php
                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "borderWidth",
                                "label" => t("Radius"),
                                "value" => $borderRadius,
                                "valueFormat" => "px"
                            ]);
                            ?>
                        </fieldset>
                    </div>
                </div>
            </div>
        </li>

        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               title="<?php echo h(t('Margin and Padding')); ?>">
                <i class="fa fa-arrows-alt-h"></i>
            </a>

            <div class="ccm-dropdown-menu <?php echo $style instanceof CustomStyle ? "ccm-inline-design-dropdown-menu-doubled" : ""; ?>">
                <div class="row">
                    <div class="<?php echo $style instanceof CustomStyle ? "col-sm-6" : "col-sm-12"; ?>">
                        <fieldset>
                            <legend>
                                <?php echo t('Padding') ?>
                            </legend>

                            <?php
                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "paddingTop",
                                "label" => t("Top"),
                                "value" => $paddingTop,
                                "valueFormat" => "px"
                            ]);

                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "paddingRight",
                                "label" => t("Right"),
                                "value" => $paddingRight,
                                "valueFormat" => "px"
                            ]);

                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "paddingBottom",
                                "label" => t("Bottom"),
                                "value" => $paddingBottom,
                                "valueFormat" => "px"
                            ]);

                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "paddingLeft",
                                "label" => t("Left"),
                                "value" => $paddingLeft,
                                "valueFormat" => "px"
                            ]);
                            ?>
                        </fieldset>
                    </div>

                    <?php if ($style instanceof CustomStyle) { ?>
                    <div class="col-sm-6">
                        <fieldset>
                            <legend>
                                <?php echo t('Margin') ?>
                            </legend>

                            <?php
                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "marginTop",
                                "label" => t("Top"),
                                "value" => $marginTop,
                                "valueFormat" => "px"
                            ]);

                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "marginRight",
                                "label" => t("Right"),
                                "value" => $marginRight,
                                "valueFormat" => "px"
                            ]);

                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "marginBottom",
                                "label" => t("Bottom"),
                                "value" => $marginBottom,
                                "valueFormat" => "px"
                            ]);

                            /** @noinspection PhpUnhandledExceptionInspection */
                            echo View::element("slider", [
                                "name" => "marginLeft",
                                "label" => t("Left"),
                                "value" => $marginLeft,
                                "valueFormat" => "px"
                            ]);
                            ?>
                        </fieldset>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </li>

        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               title="<?php echo h(t('Shadow and Rotation (CSS3)')); ?>">
                <i class="fa fa-magic"></i>
            </a>

            <div class="ccm-dropdown-menu ccm-inline-design-dropdown-menu-doubled">
                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Shadow') ?>
                            </legend>

                            <div class="row">
                                <div class="col-sm-6">
                                    <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo View::element("slider", [
                                        "name" => "boxShadowHorizontal",
                                        "label" => t("Horizontal Position"),
                                        "value" => $boxShadowHorizontal,
                                        "valueFormat" => "px"
                                    ]);
                                    ?>
                                </div>

                                <div class="col-sm-6">
                                    <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo View::element("slider", [
                                        "name" => "boxShadowVertical",
                                        "label" => t("Vertical Position"),
                                        "value" => $boxShadowVertical,
                                        "valueFormat" => "px"
                                    ]);
                                    ?>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-6">
                                    <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo View::element("slider", [
                                        "name" => "boxShadowBlur",
                                        "label" => t("Blur"),
                                        "value" => $boxShadowBlur,
                                        "valueFormat" => "px"
                                    ]);
                                    ?>
                                </div>

                                <div class="col-sm-6">
                                    <?php
                                    /** @noinspection PhpUnhandledExceptionInspection */
                                    echo View::element("slider", [
                                        "name" => "boxShadowSpread",
                                        "label" => t("Spread"),
                                        "value" => $boxShadowSpread,
                                        "valueFormat" => "px"
                                    ]);
                                    ?>
                                </div>
                            </div>

                            <hr/>

                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="form-group">
                                        <?php echo $form->label("boxShadowColor", t('Color')) ?>
                                        <?php $color->output('boxShadowColor', $boxShadowColor, ['showAlpha' => true]); ?>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <hr/>

                <div class="row">
                    <div class="col-sm-6">
                        <fieldset>
                            <legend>
                                <?php echo t('Rotate') ?>
                            </legend>

                            <div>
                                <?php
                                /** @noinspection PhpUnhandledExceptionInspection */
                                echo View::element("slider", [
                                    "name" => "rotate",
                                    "label" => t("Rotation (in degrees)"),
                                    "value" => $rotate,
                                    "valueFormat" => ""
                                ]);
                                ?>
                            </div>
                        </fieldset>
                    </div>

                    <?php if (count($deviceHideClasses)) { ?>
                        <div class="col-sm-6">
                            <fieldset>
                                <legend>
                                    <?php echo t('Device Visibility') ?>

                                    <i class="fa fa-question-circle launch-tooltip"
                                       title="<?php echo h(t('Hide the current content on a particular type of device. Un-check a device below to hide the content.')) ?>"></i>
                                </legend>

                                <div class="btn-group">
                                    <?php foreach ($deviceHideClasses as $class) {
                                        $hidden = false;

                                        if (is_object($set)) {
                                            $hidden = $set->isHiddenOnDevice($class);
                                        }

                                        echo $form->hidden("hideOnDevice" . $identifier->getString(), (int)$hidden, ["name" => "hideOnDevice[" . h($class) . "]", "data-hide-on-device-input" => $class]);

                                        ?>

                                        <button type="button"
                                                data-hide-on-device="<?php echo h($class) ?>"
                                                class="btn btn-secondary <?php echo (!$hidden) ? "active" : ""; ?>">
                                            <i class="<?php echo $gf->getDeviceHideClassIconClass($class) ?>"></i>
                                        </button>
                                    <?php } ?>
                                </div>
                            </fieldset>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </li>

        <li class="ccm-inline-toolbar-icon-cell">
            <a href="javascript:void(0);"
               title="<?php echo t('Custom CSS Classes, Block Name, Block Templates and Reset Styles') ?>">
                <i class="fa fa-cog"></i>
            </a>

            <div class="ccm-dropdown-menu">
                <div class="row">
                    <div class="col-sm-12">
                        <fieldset>
                            <legend>
                                <?php echo t('Advanced') ?>
                            </legend>

                            <div class="form-group">
                                <?php echo $form->label("customClass", t('Custom Class')) ?>
                                <?php
                                $customClassesSelect = [];
                                $customClassesSelected = [];

                                if (is_string($customClass) && $customClass != '') {
                                    $customClassesSelected = explode(' ', $customClass);
                                }

                                if (is_array($customClasses)) {
                                    foreach ($customClasses as $class) {
                                        $customClassesSelect[$class] = $class;
                                    }

                                    if ($customClassesSelected) {
                                        foreach ($customClassesSelected as $class) {
                                            $customClassesSelect[$class] = $class;
                                        }
                                    }
                                }

                                echo $form->selectMultiple('customClass', $customClassesSelect, $customClassesSelected);
                                ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("customID", t('Custom ID')); ?>
                                <?php echo $form->text('customID', $customID); ?>
                            </div>

                            <hr/>

                            <div class="form-group">
                                <?php echo $form->label("customElementAttribute", t('Custom Element Attribute')); ?>
                                <?php echo $form->textarea('customElementAttribute', $customElementAttribute); ?>
                            </div>

                            <hr>

                            <?php if ($displayBlockContainerSettings) { ?>
                                <div class="form-group">
                                    <?php echo $form->label("enableBlockContainer", t('Block Container Class')) ?>
                                    <?php echo $form->select("enableBlockContainer", $enableBlockContainerOptions, $enableBlockContainer, ["class" => "selectpicker form-control"]); ?>
                                </div>

                                <hr/>
                            <?php } ?>

                            <div class="d-grid">
                                <?php echo $form->button("clearStyles", t('Clear Styles'), ["data-reset-action" => h($resetAction), "data-action" => "reset-design"], "btn-danger"); ?>
                            </div>
                        </fieldset>
                    </div>
                </div>
            </div>
        </li>

        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-cancel">
            <?php echo $form->button("cancelDesign", t('Cancel'), ["data-action" => "cancel-design"], "btn-secondary"); ?>
        </li>

        <li class="ccm-inline-toolbar-button ccm-inline-toolbar-button-save">
            <?php echo $form->button("cancelDesign", t('Save'), ["data-action" => "save-design"], "btn-primary"); ?>
        </li>
    </ul>
</form>

<script>
    (function ($) {
        $('#ccm-inline-design-form').concreteInlineStyleCustomizer();
    })(jQuery);
</script>
