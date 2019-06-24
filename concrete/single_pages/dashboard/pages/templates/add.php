<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
?>


    <form method="post" class="form-horizontal" id="add_page_template" action="<?=$view->url('/dashboard/pages/templates/add', 'add_page_template')?>">
    <?=$this->controller->token->output('add_page_template')?>
    <?=$form->hidden('task', 'add'); ?>


    <div class="form-group">
        <label for="pTemplateName" class="col-md-2 control-label"><?=t('Name')?></label>
        <div class="col-md-10">
            <?=$form->text('pTemplateName')?>
        </div>
    </div>

    <div class="form-group">
        <label for="pTemplateHandle" class="col-md-2 control-label"><?=t('Handle')?></label>
        <div class="col-md-10">
            <?=$form->text('pTemplateHandle')?>
        </div>
    </div>

    <div class="form-group">
        <label for="pTemplateHandle" class="col-md-2 control-label"><?=t('Icon')?></label>
        <div class="col-md-10">

        <?php
        $i = 0;
        foreach ($icons as $ic) {
            ?>
          <div class="col-sm-2">
            <label style="display:block;text-align: center">
                 <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS.'/'.$ic;
            ?>" class="img-responsive" style="vertical-align: middle" />
                 <?=$form->radio('pTemplateIcon', $ic, $i == 0)?>
            </label>
          </div>
          <?php ++$i;
            ?>
        <?php 
        } ?>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?=$view->url('/dashboard/pages/templates')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
        <button type="submit" class="btn pull-right btn-primary"><?=t('Add')?></button>
    </div>
    </div>
    </form>
