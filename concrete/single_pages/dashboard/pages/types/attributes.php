<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<p class="lead"><?php echo $pagetype->getPageTypeDisplayName(); ?></p>

<div class="alert alert-info"><?=t('Attributes set here will automatically be applied to new pages of that type.')?></div>

<div data-container="editable-fields">

<?php Loader::element('attribute/editable_set_list', array(
    'category' => $category,
    'object' => $defaultPage,
    'saveAction' => $view->action('update_attribute', $pagetype->getPageTypeID()),
    'clearAction' => $view->action('clear_attribute', $pagetype->getPageTypeID()),
    'permissionsCallback' => function ($ak) {
        return true;
    },
));?>

</div>


<script type="text/javascript">
    $(function() {
        $('div[data-container=editable-fields]').concreteEditableFieldContainer({
            url: '<?=$view->action('save', $pagetype->getPageTypeID())?>',
            data: {
                ccm_token: '<?=Loader::helper('validation/token')->generate()?>'
            }
        });
    });
</script>

<div class="ccm-dashboard-header-buttons">
    <a href="<?=URL::to('/dashboard/pages/types')?>" class="btn btn-default"><?=t('Back to List')?></a>
</div>
