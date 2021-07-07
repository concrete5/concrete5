<?php

defined('C5_EXECUTE') or die("Access Denied.");
$buttonText = t("Add");
/**
 * @var $templates \Concrete\Core\Entity\Board\Template[]
 */
?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'edit']);
        $element->render();
        ?>
    </div>
    <div class="col-8">
        <form method="post" action="<?=$view->action('submit', $board->getBoardID())?>">
            <?=$token->output('submit')?>
            <fieldset>
                <div class="form-group">
                    <?=$form->label('boardName', t('Board Name'))?>
                    <?=$form->text('boardName', $boardName)?>
                </div>

                <div class="form-group">
                    <?=$form->label('templateID', t('Template'))?>
                    <?=$form->select('templateID', $templates, $templateID)?>
                </div>

                <div class="form-group">
                    <label class="control-label form-label"><?=t('Sort By')?></label>

                    <div class="form-check">
                        <?=$form->radio('sortBy', 'relevant_date_asc', $sortBy)?>
                        <label class="form-check-label" for="sortBy1">
                            <?=t('Ascending Date.')?>
                        </label>
                    </div>

                    <div class="form-check">
                        <?=$form->radio('sortBy', 'relevant_date_desc', $sortBy)?>
                        <label class="form-check-label" for="sortBy2">
                            <?=t('Descending Date.')?>
                        </label>
                    </div>
                </div>


                <?php if ($multisite) { ?>
                    <div class="form-group">
                        <label class="control-label form-label"><?=t('Site')?></label>

                        <div class="form-check">
                            <?=$form->radio('sharedBoard', 0, $isSharedBoard)?>
                            <label class="form-check-label" for="sharedBoard1">
                                <?=t('Add to current site.')?>
                            </label>
                        </div>

                        <div class="form-check">
                            <?=$form->radio('sharedBoard', 1, $isSharedBoard)?>
                            <label class="form-check-label" for="sharedBoard2">
                                <?=t('Share board with all sites.')?>
                            </label>
                        </div>
                    </div>
                <?php } ?>

                <div class="ccm-dashboard-form-actions-wrapper">
                    <div class="ccm-dashboard-form-actions ">
                        <button type="button" class="btn btn-danger float-start" data-bs-toggle="modal" data-bs-target="#delete-board"><?=t("Delete")?></button>
                        <button type="submit" class="btn btn-primary float-end"><?=t('Save')?></button>
                    </div>
                </div>
            </fieldset>
        </form>

    </div>
</div>

<div class="modal fade" id="delete-board" tabindex="-1">
    <form method="post" action="<?=$view->action('delete_board', $board->getBoardID())?>">
        <?=$token->output('delete_board')?>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?=t('Delete Board')?></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                </div>
                <div class="modal-body">
                    <?=t('Are you sure you want to remove this board?')?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                    <button type="submit" class="btn btn-danger float-end"><?=t('Delete Board')?></button>
                </div>
            </div>
        </div>
    </form>
</div>



