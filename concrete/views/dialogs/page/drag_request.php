<?php

use Concrete\Core\View\View;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Controller\Dialog\Page\DragRequest $controller */
/* @var Concrete\Core\View\DialogView $view */

/* @var bool|null $showProgressBar */
/* @var int $totalItems */
// -- OR --
/* @var string $validationToken */
/* @var Concrete\Core\Page\Sitemap\DragRequestData $dragRequestData */
/* @var string $originalPageIDs */
/* @var string $formID */

if (isset($showProgressBar) && $showProgressBar) {
    View::element('progress_bar', ['totalItems' => $totalItems, 'totalItemsSummary' => t2('%d page', '%d pages', $totalItems)]);

    return;
}
if (!$dragRequestData->canDoAnyOf([$dragRequestData::OPERATION_MOVE, $dragRequestData::OPERATION_ALIAS, $dragRequestData::OPERATION_COPY, $dragRequestData::OPERATION_COPYVERSION])) {
    ?>
    <div class="alert alert-danger">
        <?= t('You are not allowed to perform any operation with the selected pages.') ?>
    </div>
    <?php
    return;
}
$singleOriginalPage = $dragRequestData->getSingleOriginalPage();
$singleOriginalPageName = (is_object($singleOriginalPage)) ? $singleOriginalPage->getCollectionName() : t('Original Page');

?>

<div class="alert alert-info">
    <?php
    if ($singleOriginalPage === null) {
        echo t('What do you wish to do?');
    } else {
        if ($dragRequestData->getDragMode() === 'none') {
            echo t('You selected to move/copy "%s" onto "%s". What do you wish to do?', h($singleOriginalPageName), h($dragRequestData->getDestinationPage()->getCollectionName()));
        } else {
            echo t('You dragged "%s" onto "%s". What do you wish to do?', h($singleOriginalPageName), h($dragRequestData->getDestinationPage()->getCollectionName()));
        }

    }
    ?>
</div>

<form id="<?= $formID ?>">
    <input type="hidden" name="validationToken" value="<?= h($validationToken) ?>" />
    <input type="hidden" name="dragMode" value="<?= h($dragRequestData->getDragMode()) ?>" />
    <input type="hidden" name="destCID" value="<?= $dragRequestData->getDestinationPage()->getCollectionID() ?>" />
    <?php
    if ($dragRequestData->getDestinationSibling() !== null) {
        ?>
        <input type="hidden" name="destSibling" value="<?= $dragRequestData->getDestinationSibling()->getCollectionID() ?>" />
        <?php
    }
    ?>
    <input type="hidden" name="origCID" value="<?= $originalPageIDs ?>" />

    <?php
    if ($dragRequestData->canDo($dragRequestData::OPERATION_MOVE)) {
        ?>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" id="ctask1" name="ctask" class="form-check-input" value="<?= $dragRequestData::OPERATION_MOVE ?>" />
                <label class="form-check-label" for="ctask1">
                    <?php
                    if ($singleOriginalPage !== null) {
                        echo t('<strong>Move</strong> "%1$s" beneath "%2$s"', h($singleOriginalPageName), h($dragRequestData->getDestinationPage()->getCollectionName()));
                     } else {
                         echo t('<strong>Move</strong> pages beneath "%s"', h($dragRequestData->getDestinationPage()->getCollectionName()));
                     }
                     ?>
                </label>
                <?php
                if ($singleOriginalPage && !$singleOriginalPage->isExternalLink()) {
                    ?>
                    <div class="form-check" style="margin: 0 0 0 20px">
                        <input type="checkbox" class="form-check-input" name="saveOldPagePath" id="saveOldPagePath" value="1" />
                        <label class="form-check-label" for="saveOldPagePath">
                            <?= t('Save old page path') ?>
                        </label>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }

    if ($dragRequestData->canDo($dragRequestData::OPERATION_ALIAS)) {
        ?>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" name="ctask" id="ctask2" class="form-check-input" value="<?= $dragRequestData::OPERATION_ALIAS ?>" />
                <label class="form-check-label" for="ctask2">
                    <?php
                    if ($singleOriginalPage !== null) {
                        echo t('<strong>Alias</strong> "%1$s" beneath "%2$s"', h($singleOriginalPageName), h($dragRequestData->getDestinationPage()->getCollectionName()));
                    } else {
                        echo t('<strong>Alias</strong> pages beneath "%s"', h($dragRequestData->getDestinationPage()->getCollectionName()));
                    }
                    ?>
                </label>
                <div class="text-muted" style="margin: 0 0 0 20px">
                    <?= t('Pages appear in both locations; all edits to originals will be reflected in their alias.') ?>
                </div>
            </div>
        </div>
        <?php
    }

    if ($dragRequestData->canDo($dragRequestData::OPERATION_COPY) || $dragRequestData->canDo($dragRequestData::OPERATION_COPYVERSION)) {
        ?>
        <div class="form-group">
            <div class="form-check">
                <input type="radio" name="ctask" id="ctask3" class="form-check-input" value="a-copy-operation" />
                <label class="form-check-label" for="ctask3">
                    <?= sprintf('<strong>%s</strong>', $singleOriginalPage !== null ? t('Copy Page') : t('Copy Pages')) ?>
                </label>
                <div style="margin: 0 0 0 20px">
                    <?php
                    if ($dragRequestData->canDo($dragRequestData::OPERATION_COPY)) {
                        ?>
                        <div class="form-check">
                            <input type="radio" class="form-check-input" id="dtask1" name="dtask" value="<?= $dragRequestData::OPERATION_COPY ?>" />
                            <label class="form-check-label" for="dtask1">
                                <?php
                                if ($singleOriginalPage !== null) {
                                    echo h(t('Copy "%1$s" beneath "%2$s"', $singleOriginalPageName, $dragRequestData->getDestinationPage()->getCollectionName()));
                                } else {
                                    echo h(t('Copy pages beneath "%s"', $dragRequestData->getDestinationPage()->getCollectionName()));
                                }
                                ?>
                            </label>
                        </div>
                        <?php
                    }
                    if ($dragRequestData->canDo($dragRequestData::OPERATION_COPYALL)) {
                        ?>
                        <div class="form-check">

                            <input type="radio" class="form-check-input" id="dtask2"  name="dtask"  value="<?= $dragRequestData::OPERATION_COPYALL ?>" />
                            <label class="form-check-label" for="dtask2">
                                <?= h(t('Copy "%1$s" and all its children beneath "%2$s"', $singleOriginalPageName, $dragRequestData->getDestinationPage()->getCollectionName())) ?>
                            </label>
                        </div>
                        <?php
                    }
                    if ($dragRequestData->canDo($dragRequestData::OPERATION_COPYVERSION)) {
                        ?>
                        <div class="form-check">

                            <input type="radio" class="form-check-input" id="dtask3"  name="dtask"  value="<?= $dragRequestData::OPERATION_COPYVERSION ?>" />
                            <label class="form-check-label" for="dtask3">
                                <?= h(t('Replace "%1$s" with a copy of "%2$s"', $dragRequestData->getDestinationPage()->getCollectionName(), $singleOriginalPageName)) ?>
                            </label>
                        </div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
        <?php
    }
    ?>

    <div class="dialog-buttons">
        <a href="javascript:void(0)" onclick="$.fn.dialog.closeTop()" class="pull-left btn btn-default"><?= t('Cancel') ?></a>
        <a href="javascript:void(0)" data-form-selector="#<?= $formID ?>" onclick="ConcreteSitemap.submitDragRequest($($(this).data('form-selector')))" class="float-end btn btn-primary"><?= t('Go') ?></a>
    </div>
</form>

<script>
$(document).ready(function() {
    var $form = $('#' + <?= json_encode($formID) ?>);
    if (window.localStorage && window.localStorage.getItem && window.localStorage.removeItem) {
        if (window.localStorage.getItem('ccm-sitemap-movePageSaveOldPagePath')) {
            $form.find('input[name="saveOldPagePath"]').prop('checked', true);
        }
        $form.find('input[name="saveOldPagePath"]').on('change', function() {
            if (this.checked) {
                window.localStorage.setItem('ccm-sitemap-movePageSaveOldPagePath', '1');
            } else {
                window.localStorage.removeItem('ccm-sitemap-movePageSaveOldPagePath');
            }
        });
    }
    function muteInput(selector, muted) {
        var $input = $form.find(selector);
        if (muted) {
            $input.attr('disabled', true);
        } else {
            $input.attr('disabled', false);
        }
    }
    function updateState() {
        var ctask = $form.find('input[name="ctask"]:checked').val();
        muteInput('input[name="saveOldPagePath"]', ctask !== <?= json_encode($dragRequestData::OPERATION_MOVE) ?>);
        muteInput('input[name="dtask"]', ctask !== 'a-copy-operation');
    }
    $form.find('input[name="ctask"]').on('change', function() {
    	updateState();
    });
    $form.find('input[name="saveOldPagePath"]').on('change click', function() {
        $form.find('input[name="ctask"][value="' + <?= json_encode($dragRequestData::OPERATION_MOVE) ?> + '"]')
            .prop('checked', true)
            .trigger('change')
        ;
    });
    $form.find('input[name="dtask"]').on('change click', function() {
        $form.find('input[name="ctask"][value="a-copy-operation"]')
            .prop('checked', true)
            .trigger('change')
        ;
    });
    $form.find('input[name="dtask"]:first').prop('checked', 'checked');
    $form.find('input[name="ctask"]:first').prop('checked', 'checked');
    updateState();
});
</script>
