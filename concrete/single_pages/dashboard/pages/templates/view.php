<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<?php if (isset($template) && is_object($template) && ($this->controller->getTask() == 'edit' || $this->controller->getTask() == 'update')) {
    $form = Loader::helper('form');
    ?>
      
    <form method="post" class="form-horizontal" id="update_page_template" action="<?=$view->url('/dashboard/pages/templates', 'update')?>">
    <?=$this->controller->token->output('update_page_template')?>
    <input type="hidden" name="pTemplateID" value="<?=$template->getPageTemplateID()?>" />

        <?php $confirmMsg = t('Are you sure?');
    ?>
        <script type="text/javascript">
        deleteTemplate = function() {
            if(confirm('<?=$confirmMsg?>')){ 
                location.href="<?=$view->url('/dashboard/pages/templates/', 'delete', $template->getPageTemplateID(), $this->controller->token->generate('delete_page_template'))?>";
            }   
        }
        </script>


        
        <div class="form-group">
            <label for="pTemplateName" class="col-md-2 control-label"><?=t('Name')?></label>
            <div class="col-md-10">
                <?=$form->text('pTemplateName', $template->getPageTemplateName())?>
            </div>
        </div>

        <div class="form-group">
            <label for="pTemplateHandle" class="col-md-2 control-label"><?=t('Handle')?></label>
            <div class="col-md-10">
                <?=$form->text('pTemplateHandle', $template->getPageTemplateHandle())?>
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
                <label style="text-align: center">
                     <img src="<?=REL_DIR_FILES_PAGE_TEMPLATE_ICONS.'/'.$ic;
        ?>" class="img-responsive" style="vertical-align: middle" />
                     <?=$form->radio('pTemplateIcon', $ic, $ic == $template->getPageTemplateIcon())?>
                </label>
              </div>
              <?php ++$i;
        ?>
            <?php 
    }
    ?>
            </div>
        </div>
      
    <div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <a href="<?=$view->url('/dashboard/pages/templates')?>" class="btn btn-default pull-left"><?=t("Cancel")?></a>
        <div class="btn-toolbar pull-right">
            <button class="btn btn-danger" onclick="deleteTemplate()" type="button"><?=t('Delete Template')?></button>
            <button type="submit" class="btn btn-primary"><?=t('Update')?></button>
        </div>
    </div>
    </div> 

    </form>
    

<?php 
} else {
    ?>

    <div class="ccm-dashboard-header-buttons">
        <a href="<?php echo View::url('/dashboard/pages/templates/add')?>" class="btn btn-primary"><?php echo t("Add Template")?></a>
    </div>

    <?php if (count($templates) == 0) {
    ?>
        <br/><strong><?=t('No page types found.')?></strong><br/><br>
    <?php 
} else {
    ?>

        <table class="table table-striped">

    <?php foreach ($templates as $pt) {
    ?>
        <tr>
            <td><a href="<?=$view->action('edit', $pt->getPageTemplateID())?>"><?=$pt->getPageTemplateIconImage()?></a></td>
            <td style="width: 100%; vertical-align: middle"><a href="<?=$view->action('edit', $pt->getPageTemplateID())?>"><p class="lead" style="margin-bottom: 0px"><?=$pt->getPageTemplateDisplayName()?></p></a></td>
        </tr>
    <?php 
}
    ?>


        </table>

    <?php 
}
    ?>

<?php 
} ?>