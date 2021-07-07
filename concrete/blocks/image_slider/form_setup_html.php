<?php /** @noinspection PhpDeprecationInspection */

defined('C5_EXECUTE') or die("Access Denied.");

use Concrete\Core\Application\Service\UserInterface;
use Concrete\Core\Block\View\BlockView;
use Concrete\Core\Editor\EditorInterface;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Legacy\FilePermissions;
use Concrete\Core\Legacy\TaskPermission;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Utility\Service\Identifier;
use Concrete\Core\Validation\CSRF\Token;

$fp = FilePermissions::getGlobal();
$tp = new TaskPermission();

/** @var BlockView $view */
/** @var array $rows */
/** @var int $navigationType */
/** @var int $timeout */
/** @var int $speed */
/** @var int $noAnimate */
/** @var int $pause */
/** @var int $maxWidth */

$app = Application::getFacadeApplication();
/** @var UserInterface $userInterface */
$userInterface = $app->make(UserInterface::class);
/** @var Identifier $id */
$idHelper = $app->make(Identifier::class);
/** @var Form $form */
$form = $app->make(Form::class);
/** @var Token $token */
$token = $app->make(Token::class);
/** @var EditorInterface $editor */
$editor = $app->make(EditorInterface::class);

$id = $idHelper->getString(18);

echo $userInterface->tabs([
    ['slides-' . $id, t('Slides'), true],
    ['options-' . $id, t('Options')],
]);

?>

<div class="tab-content">
    <div class="tab-pane show active" id="slides-<?php echo $id; ?>" role="tabpanel">
        <div class="ccm-image-slider-block-container">
            <div class="ccm-image-slider-entries ccm-image-slider-entries-<?php echo $bID; ?>">

            </div>

            <div>
                <button type="button"
                        class="btn btn-success ccm-add-image-slider-entry ccm-add-image-slider-entry-<?php echo $bID; ?>">
                    <?php echo t('Add Slide'); ?>
                </button>
            </div>
        </div>
    </div>

    <div class="tab-pane" id="options-<?php echo $id; ?>" role="tabpanel">
        <div class="form-group">
            <?php echo $form->label("", t("Navigation")); ?>
            <div class="form-check">
                <?php echo $form->radio($view->field('navigationType'), 0, $navigationType, ["id" => "navigationTypeArrows", "name" => $view->field('navigationType')]); ?>
                <?php echo $form->label("navigationTypeArrows", t("Arrows"), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio($view->field('navigationType'), 1, $navigationType, ["id" => "navigationTypeBullets", "name" => $view->field('navigationType')]); ?>
                <?php echo $form->label("navigationTypeBullets", t("Bullets"), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio($view->field('navigationType'), 2, $navigationType, ["id" => "navigationTypeArrowsAndBullets", "name" => $view->field('navigationType')]); ?>
                <?php echo $form->label("navigationTypeArrowsAndBullets", t("Arrows & Bullets"), ["class" => "form-check-label"]); ?>
            </div>

            <div class="form-check">
                <?php echo $form->radio($view->field('navigationType'), 3, $navigationType, ["id" => "navigationTypeNone", "name" => $view->field('navigationType')]); ?>
                <?php echo $form->label("navigationTypeNone", t("None"), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label($view->field('timeout'), t('Slide Duration')); ?>

            <div class="input-group" style="width: 150px">
                <?php echo $form->number($view->field('timeout'), $timeout ? $timeout : 4000, ['min' => '1', 'max' => '99999']); ?>

                <span class="input-group-text">
                    <?php echo t('ms'); ?>
                </span>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label($view->field('speed'), t('Slide Transition Speed')); ?>

            <div class="input-group" style="width: 150px">
                <?php echo $form->number($view->field('speed'), $speed ? $speed : 500, ['min' => '1', 'max' => '99999']); ?>

                <span class="input-group-text">
                    <?php echo t('ms'); ?>
                </span>
            </div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox($view->field('noAnimate'), "1", $noAnimate); ?>
                <?php echo $form->label($view->field('noAnimate'), t("Disable Automatic Slideshow"), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <div class="form-check">
                <?php echo $form->checkbox($view->field('pause'), "1", $pause); ?>
                <?php echo $form->label($view->field('pause'), t("Pause Slideshow on Hover"), ["class" => "form-check-label"]); ?>
            </div>
        </div>

        <div class="form-group">
            <?php echo $form->label($view->field('maxWidth'), t('Maximum Slide Width (0 means no limit)')); ?>

            <div class="input-group" style="width: 150px">
                <?php echo $form->number($view->field('maxWidth'), $maxWidth ? $maxWidth : 0, ['min' => '0', 'max' => '9999']); ?>

                <span class="input-group-text">
                    <?php echo t('px'); ?>
                </span>
            </div>
        </div>
    </div>
</div>

<!--suppress JSDuplicatedDeclaration, JSUnresolvedVariable -->
<script>
    if (typeof CCM_EDITOR_SECURITY_TOKEN === "undefined") {
        let CCM_EDITOR_SECURITY_TOKEN = "<?php echo h($token->generate('editor')); ?>";
    }

    <?php
    $editorJavascript = $editor->outputStandardEditorInitJSFunction();
    ?>

    if (typeof launchEditor === "undefined") {
        var launchEditor = <?php echo $editorJavascript; ?>;
    }

    $(document).ready(function () {
        let sliderEntriesContainer = $('.ccm-image-slider-entries-<?php echo $bID; ?>');
        let _templateSlide = _.template($('#imageTemplate-<?php echo $bID; ?>').html());

        let attachDelete = function ($obj) {
            $obj.click(function () {
                let deleteIt = confirm('<?php echo t('Are you sure?'); ?>');
                if (deleteIt === true) {
                    let slideID = $(this).closest('.ccm-image-slider-entry').find('.editor-content').attr('id');
                    if (typeof CKEDITOR === 'object') {
                        CKEDITOR.instances[slideID].destroy();
                    }

                    $(this).closest('.ccm-image-slider-entry-<?php echo $bID; ?>').remove();
                    doSortCount();
                }
            });
        };

        let attachFileManagerLaunch = function ($obj) {
            $obj.click(function () {
                let oldLauncher = $(this);
                ConcreteFileManager.launchDialog(function (data) {
                    ConcreteFileManager.getFileDetails(data.fID, function (r) {
                        jQuery.fn.dialog.hideLoader();
                        let file = r.files[0];
                        oldLauncher.html(file.resultsThumbnailImg);
                        oldLauncher.next('.image-fID').val(file.fID);
                    });
                });
            });
        };

        let doSortCount = function () {
            $('.ccm-image-slider-entry-<?php echo $bID; ?>').each(function (index) {
                $(this).find('.ccm-image-slider-entry-sort').val(index);
            });
        };

        sliderEntriesContainer.on('change', 'select[data-field=entry-link-select]', function () {
            let container = $(this).closest('.ccm-image-slider-entry-<?php echo $bID; ?>');
            switch (parseInt($(this).val())) {
                case 2:
                    container.find('div[data-field=entry-link-page-selector]').addClass('hide-slide-link').removeClass('show-slide-link');
                    container.find('div[data-field=entry-link-url]').addClass('show-slide-link').removeClass('hide-slide-link');
                    break;
                case 1:
                    container.find('div[data-field=entry-link-url]').addClass('hide-slide-link').removeClass('show-slide-link');
                    container.find('div[data-field=entry-link-page-selector]').addClass('show-slide-link').removeClass('hide-slide-link');
                    break;
                default:
                    container.find('div[data-field=entry-link-page-selector]').addClass('hide-slide-link').removeClass('show-slide-link');
                    container.find('div[data-field=entry-link-url]').addClass('hide-slide-link').removeClass('show-slide-link');
                    break;
            }
        });

        <?php if ($rows) {
        foreach ($rows as $row) {
        $linkType = 0;
        if ($row['linkURL']) {
            $linkType = 2;
        } elseif ($row['internalLinkCID']) {
            $linkType = 1;
        } ?>
        sliderEntriesContainer.append(_templateSlide({
            fID: '<?php echo $row['fID']; ?>',
            <?php if (File::getByID($row['fID'])) {
            ?>
            image_url: '<?php
                /** @var Version $file */
                $file = File::getByID($row['fID']);
                echo h($file->getThumbnailURL('file_manager_listing'));
                ?>',
            <?php
            } else {
            ?>
            image_url: '',
            <?php
            } ?>
            link_url: '<?php echo $row['linkURL']; ?>',
            link_type: '<?php echo $linkType; ?>',
            title: '<?php echo addslashes(h($row['title'])); ?>',
            description: '<?php echo str_replace(["\t", "\r", "\n"], "", addslashes(h($row['description']))); ?>',
            sort_order: '<?php echo $row['sortOrder']; ?>'
        }));
        sliderEntriesContainer.find('.ccm-image-slider-entry-<?php echo $bID; ?>:last-child div[data-field=entry-link-page-selector]').concretePageSelector({
            'inputName': '<?php echo $view->field('internalLinkCID'); ?>[]', 'cID': <?php if (1 == $linkType) {
                ?><?php echo intval($row['internalLinkCID']); ?><?php
            } else {
                ?>false<?php
            } ?>
        });
        <?php
        }
        } ?>

        doSortCount();
        sliderEntriesContainer.find('select[data-field=entry-link-select]').trigger('change');

        $('.ccm-add-image-slider-entry-<?php echo $bID; ?>').click(function () {
            let thisModal = $(this).closest('.ui-dialog-content');
            sliderEntriesContainer.append(_templateSlide({
                fID: '',
                title: '',
                link_url: '',
                cID: '',
                description: '',
                link_type: 0,
                sort_order: '',
                image_url: ''
            }));

            $('.ccm-image-slider-entry-<?php echo $bID; ?>').not('.slide-closed').each(function () {
                $(this).addClass('slide-closed');
                let thisEditButton = $(this).closest('.ccm-image-slider-entry-<?php echo $bID; ?>').find('.btn.ccm-edit-slide');
                thisEditButton.text(thisEditButton.data('slideEditText'));
            });
            let newSlide = $('.ccm-image-slider-entry-<?php echo $bID; ?>').last();
            let closeText = newSlide.find('.btn.ccm-edit-slide').data('slideCloseText');
            newSlide.removeClass('slide-closed').find('.btn.ccm-edit-slide').text(closeText);

            thisModal.scrollTop(newSlide.offset().top);
            launchEditor(newSlide.find('.editor-content'));
            attachDelete(newSlide.find('.ccm-delete-image-slider-entry-<?php echo $bID; ?>'));
            attachFileManagerLaunch(newSlide.find('.ccm-pick-slide-image'));
            newSlide.find('div[data-field=entry-link-page-selector-select]').concretePageSelector({
                'inputName': '<?php echo $view->field('internalLinkCID'); ?>[]'
            });
            doSortCount();
        });

        $('.ccm-image-slider-entries-<?php echo $bID; ?>').on('click', '.ccm-edit-slide', function () {
            $(this).closest('.ccm-image-slider-entry-<?php echo $bID; ?>').toggleClass('slide-closed');
            let thisEditButton = $(this);
            if (thisEditButton.data('slideEditText') === thisEditButton.text()) {
                thisEditButton.text(thisEditButton.data('slideCloseText'));
            } else if (thisEditButton.data('slideCloseText') === thisEditButton.text()) {
                thisEditButton.text(thisEditButton.data('slideEditText'));
            }
        });

        $('.ccm-image-slider-entries-<?php echo $bID; ?>').sortable({
            placeholder: "ui-state-highlight",
            axis: "y",
            handle: "i.fa-arrows",
            cursor: "move",
            update: function () {
                doSortCount();
            }
        });

        attachDelete($('.ccm-delete-image-slider-entry-<?php echo $bID; ?>'));
        attachFileManagerLaunch($('.ccm-pick-slide-image-<?php echo $bID; ?>'));
        $(function () {  // activate editors
            if ($('.editor-content-<?php echo $bID; ?>').length) {
                launchEditor($('.editor-content-<?php echo $bID; ?>'));
            }
        });
    });
</script>

<!--suppress CssUnusedSymbol -->
<style>
    .ccm-image-slider-block-container input[type="text"],
    .ccm-image-slider-block-container textarea {
        display: block;
        width: 100%;
    }

    .ccm-image-slider-block-container .btn-success {
        margin-bottom: 20px;
    }

    .ccm-image-slider-entries {
        padding-bottom: 30px;
        position: relative;
    }

    .ccm-image-slider-block-container .slide-well {
        min-height: 20px;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #f5f5f5;
        border: 1px solid #e3e3e3;
        border-radius: 4px;
        -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.05);
    }

    .ccm-pick-slide-image {
        padding: 5px;
        cursor: pointer;
        background: #dedede;
        border: 1px solid #cdcdcd;
        text-align: center;
        vertical-align: middle;
        width: 72px;
        height: 72px;
        display: table-cell;
    }

    .ccm-pick-slide-image img {
        max-width: 100%;
    }

    .ccm-image-slider-entry {
        position: relative;
    }

    .ccm-image-slider-entry.slide-closed .form-group {
        display: none;
    }

    .ccm-image-slider-entry .form-group {
        margin-left: 0 !important;
        margin-right: 0 !important;
        padding-left: 0 !important;
        padding-right: 0 !important;
        border-bottom: none !important;
    }

    .ccm-image-slider-entry.slide-closed .form-group:first-of-type {
        display: block;
        margin-bottom: 0;
    }

    .ccm-image-slider-entry.slide-closed .form-group:first-of-type label {
        display: none;
    }

    .btn.ccm-edit-slide {
        position: absolute;
        top: 10px;
        right: 127px;
    }

    .btn.ccm-delete-image-slider-entry {
        position: absolute;
        top: 10px;
        right: 41px;
    }

    .ccm-image-slider-block-container i:hover {
        color: #428bca;
    }

    .ccm-image-slider-block-container i.fa-arrows {
        position: absolute;
        top: 6px;
        right: 5px;
        cursor: move;
        font-size: 20px;
        padding: 5px;
    }

    .ccm-image-slider-block-container .ui-state-highlight {
        height: 94px;
        margin-bottom: 15px;
    }

    .ccm-image-slider-entries .ui-sortable-helper {
        -webkit-box-shadow: 0 10px 18px 2px rgba(54, 55, 66, 0.27);
        -moz-box-shadow: 0 10px 18px 2px rgba(54, 55, 66, 0.27);
        box-shadow: 0 10px 18px 2px rgba(54, 55, 66, 0.27);
    }

    .ccm-image-slider-block-container .show-slide-link {
        display: block;
    }

    .ccm-image-slider-block-container .hide-slide-link {
        display: none;
    }
</style>

<script type="text/template" id="imageTemplate-<?php echo $bID; ?>">
    <div class="ccm-image-slider-entry ccm-image-slider-entry-<?php echo $bID; ?> slide-well slide-closed">
        <div class="form-group">
            <label class="control-label form-label"><?php echo t('Image'); ?></label>
            <div class="ccm-pick-slide-image ccm-pick-slide-image-<?php echo $bID; ?>">
                <% if (image_url.length > 0) { %>
                <!--suppress HtmlUnknownTarget, HtmlRequiredAltAttribute -->
                <img src="<%= image_url %>"/>
                <% } else { %>
                <i class="fa fa-picture-o"></i>
                <% } %>
            </div>
            <input type="hidden" name="<?php echo $view->field('fID'); ?>[]" class="image-fID" value="<%=fID%>"/>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?php echo t('Title'); ?></label>
            <!--suppress HtmlFormInputWithoutLabel -->
            <input class="form-control ccm-input-text" type="text" name="<?php echo $view->field('title'); ?>[]"
                   value="<%=title%>"/>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?php echo t('Description'); ?></label>
            <div class="editor-edit-content"></div>
            <!--suppress HtmlFormInputWithoutLabel -->
            <textarea id="ccm-slide-editor-<%= _.uniqueId() %>" style="display: none"
                      class="editor-content editor-content-<?php echo $bID; ?>"
                      name="<?php echo $view->field('description'); ?>[]"><%=description%></textarea>
        </div>
        <div class="form-group">
            <label class="control-label form-label"><?php echo t('Link'); ?></label>
            <!--suppress HtmlFormInputWithoutLabel -->
            <select data-field="entry-link-select" name="<?php echo $view->field('linkType'); ?>[]" class="form-select"
                    style="width: 60%;">
                <option value="0"
                <% if (!link_type) { %>selected<% } %>><?php echo t('None'); ?></option>
                <option value="1"
                <% if (link_type == 1) { %>selected<% } %>><?php echo t('Another Page'); ?></option>
                <option value="2"
                <% if (link_type == 2) { %>selected<% } %>><?php echo t('External URL'); ?></option>
            </select>
        </div>
        <div data-field="entry-link-url" class="form-group hide-slide-link">
            <label class="control-label form-label"><?php echo t('URL:'); ?></label>
            <textarea class="form-control" name="<?php echo $view->field('linkURL'); ?>[]"><%=link_url%></textarea>
        </div>
        <div data-field="entry-link-page-selector" class="form-group hide-slide-link">
            <label class="control-label form-label"><?php echo t('Choose Page:'); ?></label>
            <div data-field="entry-link-page-selector-select"></div>
        </div>
        <button type="button" class="btn btn-sm btn-secondary ccm-edit-slide ccm-edit-slide-<?php echo $bID; ?>"
                data-slide-close-text="<?php echo t('Collapse Slide'); ?>"
                data-slide-edit-text="<?php echo t('Edit Slide'); ?>"><?php echo t('Edit Slide'); ?></button>
        <button type="button"
                class="btn btn-sm btn-danger ccm-delete-image-slider-entry ccm-delete-image-slider-entry-<?php echo $bID; ?>"><?php echo t('Remove'); ?></button>
        <i class="fa fa-arrows"></i>

        <input class="ccm-image-slider-entry-sort" type="hidden" name="<?php echo $view->field('sortOrder'); ?>[]"
               value="<%=sort_order%>"/>
    </div>
</script>
