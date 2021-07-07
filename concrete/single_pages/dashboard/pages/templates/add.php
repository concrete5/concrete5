<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\Pages\Templates\Add $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 */

?>
<form method="post" id="add_page_template" action="<?= $view->action('add_page_template'); ?>">
    <?php $token->output('add_page_template'); ?>
    <?=$form->hidden('task', 'add'); ?>

    <div class="form-group row">
        <label for="pTemplateName" class="col-2"><?=t('Name'); ?></label>
        <div class="col-10">
            <?=$form->text('pTemplateName'); ?>
        </div>
    </div>

    <div class="form-group row">
        <label for="pTemplateHandle" class="col-2"><?=t('Handle'); ?></label>
        <div class="col-10">
          <?=$form->text('pTemplateHandle'); ?>
        </div>
    </div>

    <div class="form-group row">
        <label for="pTemplateHandle" class="col-2"><?=t('Icon'); ?></label>
        <div class="col-10">
            <div class="row">
                <?php
                $i = 0;
                foreach ($icons as $ic) {
                    ?>
                    <div class="col-2 text-center">
                        <label>
                            <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS . '/' . $ic; ?>" class="img-fluid" style="" />
                            <span class="form-check">
                                <?=$form->radio('pTemplateIcon', $ic, $i == 0); ?>
                            </span>
                            <label class="form-check-label"></label>
                        </label>
                    </div>
                <?php $i++;
                }
                ?>
            </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/pages/templates'); ?>" class="btn btn-secondary float-start"><?=t('Cancel'); ?></a>
            <button type="submit" class="btn float-end btn-primary"><?=t('Add'); ?></button>
        </div>
    </div>
</form>
