<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Controller\SinglePage\Dashboard\Pages\Containers $controller
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Entity\Page\Container[] $containers
 * @var Concrete\Core\Entity\Page\Container $container
 * @var string $tokenMessage
 */

$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
$icons = $app->make(\Concrete\Core\Page\Container\IconRepository::class)->getIcons();

if (isset($container) && $container) {
    $action = URL::to('/dashboard/pages/containers/update_container', $container->getContainerID());
    $buttonText = t('Save');
    $containerName = $container->getContainerName();
    $containerHandle = $container->getContainerHandle();
    $containerIcon = $container->getContainerIcon();
} else {
    $action = URL::to('/dashboard/pages/containers/add', 'add_container');
    $buttonText = t('Add');
    $containerName = '';
    $containerHandle = '';
    $containerIcon = false;
}

?>


<form method="post" action="<?=$action; ?>">
    <?php $token->output($tokenMessage); ?>
    
    <div class="form-group row">
        <label for="containerName" class="col-2"><?=t('Name'); ?></label>
        <div class="col-10">
           <?=$form->text('containerName', $containerName); ?>
        </div>
    </div>

    <div class="form-group row">
        <label for="containerHandle" class="col-2"><?=t('Handle'); ?></label>
            <div class="col-10">
            <?=$form->text('containerHandle', $containerHandle); ?>
            <div class="form-text text-muted"><?=t('This must be alpha-numeric. Underscores are allowed. It should match a template file that ends in .php'); ?></div>
       </div>
    </div>
    
    <div class="form-group row">
        <label for="containerIcon" class="col-2"><?=t('Icon'); ?></label>
        <div class="col-10">
            <div class="row">
                <?php
                $i = 0;
                foreach ($icons as $icon) {
                    $isChecked = false;
                    if (!$containerIcon) {
                        $isChecked = ($i == 0);
                    } else {
                        $isChecked = $containerIcon == $icon->getFilename();
                    } ?>
                    <div class="col-2 text-center">
                        <label>
                            <img src="<?=$icon->getUrl(); ?>" class="img-fluid"  />
                            <span class="form-check">
                                <?=$form->radio('containerIcon', $icon->getFilename(), $isChecked); ?>
                            </span>
                            <label class="form-check-label" > </label>
                        </label>
                    </div>
                    <?php $i++; ?>
                    <?php
                } ?>
             </div>
        </div>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a href="<?=URL::to('/dashboard/pages/containers'); ?>" class="btn btn-secondary float-start"><?=t('Cancel'); ?></a>
            <button type="submit" class="btn float-end btn-primary"><?=$buttonText; ?></button>
            <?php if ($container) {
                ?>
                <button type="button" class="btn float-end btn-danger me-1" data-bs-toggle="modal" data-bs-target="#delete-container"><?=t('Delete Container'); ?></button>
            <?php
            } ?>
        </div>
    </div>
</form>

<?php if ($container) { ?>
    <div class="modal fade" id="delete-container" tabindex="-1">
        <form method="post" action="<?=$view->action('delete_container', $container->getContainerID()); ?>">
            <?php $token->output('delete_container'); ?>
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><?=t('Delete Container'); ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                    </div>
                    <div class="modal-body">
                        <?=t('Are you sure you want to remove this container? Content will be lost anywhere it is used on your site. This cannot be undone.'); ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary me-auto" data-bs-dismiss="modal"><?=t('Cancel'); ?></button>
                        <button type="submit" class="btn btn-danger float-end"><?=t('Delete Container'); ?></button>
                    </div>
                </div>
            </div>
        </form>
    </div>
<?php
}
?>
