<?php defined('C5_EXECUTE') or die('Access Denied.');
$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */
?>
<div class="ccm-ui">

<?php if ($total == 0) { ?>

    <?php if (!$view->controller->getRequest()->isPost()) { ?>
        <?php echo t("There are no pages of this type added to your website.")?>
    <?php } ?>
<?php } else { ?>

    <form method="post" id="ccmUpdateFromPageTypeForm" data-dialog-form-processing="progressive" data-dialog-form="update-from-page-type" action="<?php echo $controller->action('submit')?>">

    <p><?php echo t('This will reset all blocks and their positions on child pages to those that are set in the defaults.')?></p>

    <div data-dialog-form-element="progress-bar"></div>

    <div class="dialog-buttons">
        <button class="btn btn-default pull-left" data-dialog-action="cancel"><?php echo t('Cancel')?></button>
        <button class="btn btn-primary pull-right" data-dialog-action="submit"><?php echo t('Proceed')?></button>
    </div>

    </form>

<?php } ?>

</div>
