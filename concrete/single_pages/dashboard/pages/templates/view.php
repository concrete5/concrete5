<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\Pages\Templates $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 */

if (isset($template) && is_object($template) && ($controller->getAction() == 'edit' || $controller->getAction() == 'update')) {
    ?>
    <form method="post" class="form-horizontal" id="update_page_template" action="<?=$view->action('update'); ?>">
        <?php $token->output('update_page_template'); ?>
        <input type="hidden" name="pTemplateID" value="<?=$template->getPageTemplateID(); ?>" />

        <div class="form-group row">
            <label for="pTemplateName" class="col-2 "><?=t('Name'); ?></label>
            <div class="col-10">
                <?=$form->text('pTemplateName', $template->getPageTemplateName()); ?>
           </div>
        </div>

        <div class="form-group row">
            <label for="pTemplateHandle" class="col-2"><?=t('Handle'); ?></label>
            <div class="col-10">
                <?=$form->text('pTemplateHandle', $template->getPageTemplateHandle()); ?>
            </div>
        </div>

        <div class="form-group row">
            <label for="pTemplateHandle" class="col-2"><?=t('Icon'); ?></label>
            <div class="col-10">
                <div class="row">
                    <?php
                        $templateIcon = $template->getPageTemplateIcon();
                        $i = 0;
                        foreach ($icons as $ic) {
                    ?>
                          <div class="col-2 text-center">
                            <label>
                                 <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS . '/' . $ic; ?>" class="img-fluid" />
                                 <span class="form-check">
                                    <?=$form->radio('pTemplateIcon', $ic, $ic == $templateIcon); ?>
                                 </span>
                                <label class="form-check-label"></label>
                            </label>
                          </div>
                          <?php
                            $i++;
                        }
                        ?>
                </div>
            </div>
        </div>
      
        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <a href="<?=$view->action(''); ?>" class="btn btn-secondary float-start"><?=t('Cancel'); ?></a>
                <div class="btn-toolbar float-end">
                    <button class="btn btn-danger me-1" data-bs-toggle="modal" data-bs-target="#delete-template" type="button"><?=t('Delete Template'); ?></button>
                    <button type="submit" class="btn btn-primary"><?=t('Update'); ?></button>
                </div>
            </div>
        </div>
    </form>
<?php
} else {
        ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?= $view->action('add'); ?>" class="btn btn-primary"><?= t('Add Template'); ?></a>
    </div>

    <?php
    if (count($templates) == 0) {
        ?>
        <br/><strong><?=t('No page types found.'); ?></strong><br/><br>
    <?php
    } else {
        ?>
        <table class="table table-striped">
            <tbody>
                <?php foreach ($templates as $pt) { ?>
                    <tr>
                        <td><a href="<?=$view->action('edit', $pt->getPageTemplateID()); ?>"><?=$pt->getPageTemplateIconImage(); ?></a></td>
                        <td style="width: 100%; vertical-align: middle"><a href="<?=$view->action('edit', $pt->getPageTemplateID()); ?>"><p class="lead" style="margin-bottom: 0;"><?=$pt->getPageTemplateDisplayName(); ?></p></a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php
    }
}

if ($template) {
        ?>
    <div class="modal fade" id="delete-template" tabindex="-1">
        <form method="post" action="<?=$view->action('delete', $template->getPageTemplateID(), $token->generate('delete_page_template')); ?>">
            <?php $token->output('delete_template'); ?>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?=t('Delete Template'); ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                    </div>
                    <div class="modal-body">
                        <?=t('Are you sure?'); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal"><?=t('Cancel'); ?></button>
                        <button type="submit" class="btn btn-danger float-end"><?=t('Delete Template'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php
}
?>
