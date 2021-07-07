<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?=$view->action('add')?>" class="btn btn-secondary"><?=t('Create Custom Board Element')?></a>
    </div>

<h2 class="mb-3"><?=t('Library')?></h2>

<h3><?=t('Ready to Publish')?></h3>

<?php if ($elements && count($elements)) { ?>

    <ul class="item-select-list">
        <?php foreach($elements as $element) {
            $name = t('(No Name)');
            if ($element->getElementName()) {
                $name = $element->getElementName();
            }
            $created = $element->getDateCreatedDateTime();

            ?>
            <li><a href="<?=URL::to('/dashboard/boards/designer/', 'view_element', $element->getID())?>">
                    <i class="fa fa-th"></i>
                    <?=$name?>
                    <span class="text-muted float-end"><?=$created->format('F d, Y g:i a')?></span>
                </a>
            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <?=t('There are no custom slots in your library.')?>
<?php } ?>

<br/><br/>

<h3><?=t('Drafts')?></h3>

<?php if ($drafts && count($drafts)) { ?>

    <ul class="item-select-list">
        <?php foreach($drafts as $element) {
            $name = t('(No Name)');
            if ($element->getElementName()) {
                $name = $element->getElementName();
            }
            $created = $element->getDateCreatedDateTime();

            ?>
            <li><a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#element-draft-<?=$element->getId()?>">
                    <i class="fa fa-th"></i>
                    <?=$name?>
                    <span class="text-muted float-end"><?=$created->format('F d, Y g:i a')?></span>
                </a>


                <div class="modal fade" id="element-draft-<?=$element->getId()?>" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <form method="post" action="<?=$view->action('delete_element', $element->getId())?>">
                            <?=$token->output('delete_element')?>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"><?=t('Continue')?></h5>
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                                </div>
                                <div class="modal-body">
                                    <?=t('Continue with this custom element or click below to remove.')?>
                                </div>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-danger float-start"><?=t('Delete Draft')?></button>
                                    <a href="<?=$view->controller->getContinueURL($element)?>" class="btn btn-secondary ms-auto float-end"><?=t('Continue')?></a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </li>
        <?php } ?>
    </ul>
<?php } else { ?>
    <?=t('You are not currently working on any custom slots.')?>
<?php } ?>

