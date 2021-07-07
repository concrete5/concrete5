<?php

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Controller\SinglePage\Dashboard\System\Files\ExternalFileProvider;
use Concrete\Core\Entity\File\ExternalFileProvider\ExternalFileProvider as ExternalFileProviderEntity;
use Concrete\Core\Entity\File\ExternalFileProvider\Type\Type;
use Concrete\Core\Form\Service\Form;
use Concrete\Core\Page\Page;
use Concrete\Core\Page\View\PageView;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\Validation\CSRF\Token;

/* @var ExternalFileProvider $controller */
/* @var Form $form */
/* @var Page $c */
/* @var PageView $view */
/* @var Token $token */
/* @var ExternalFileProviderEntity[] $externalFileProviders */
/* @var Type[] $types */

switch ($controller->getAction()) {
    case 'select_type':
    case 'add':
    case 'edit':
    case 'update':
    case 'delete':
        /* @var Type $type */
        if (!isset($externalFileProvider) || !is_object($externalFileProvider)) {
            $externalFileProvider = null;
        }

        if ($externalFileProvider !== null) {
            /* @var ExternalFileProviderEntity $externalFileProvider */
            $efpName = $externalFileProvider->getName();
            $method = 'update';
            ?>
            <div class="ccm-dashboard-header-buttons">
                <form method="post" action="<?php echo $view->action('delete'); ?>">

                    <?php echo $form->hidden('efpID', $externalFileProvider->getID()); ?>

                    <?php echo $token->output('delete'); ?>

                    <button type="submit" class="btn btn-danger">
                        <?php echo t('Delete External File Provider'); ?>
                    </button>
                </form>
            </div>
        <?php } else {
            $efpName = '';
            $method = 'add';
        }
        ?>

        <form method="post" action="<?php echo $view->action($method); ?>" id="ccm-attribute-key-form">
            <?php echo $token->output($method); ?>

            <?php echo $form->hidden('efpTypeID', $type->getID()); ?>

            <?php if ($externalFileProvider !== null) { ?>
                <?php echo $form->hidden('efpID', $externalFileProvider->getID()); ?>
            <?php } ?>

            <fieldset>
                <legend>
                    <?php echo t('Basics'); ?>
                </legend>

                <div class="form-group">
                    <?php echo $form->label('efpName', t('Name')); ?>

                    <div class="input-group">
                        <?php echo $form->text('efpName', $efpName); ?>

                        <span class="input-group-text">
                            <i class="fa fa-asterisk"></i>
                        </span>
                    </div>
                </div>
            </fieldset>

            <?php if ($type->hasOptionsForm()) { ?>
                <fieldset>
                    <legend>
                        <?php echo t('Options %s External File Provider Type', $type->getName()); ?>
                    </legend>

                    <?php /** @noinspection PhpUnhandledExceptionInspection */
                    $type->includeOptionsForm($externalFileProvider); ?>
                </fieldset>
            <?php } ?>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?php echo Url::to($c); ?>" class="btn float-start btn-secondary">
                        <?php echo t('Back'); ?>
                    </a>

                    <?php if ($externalFileProvider !== null) { ?>
                        <button type="submit" class="btn btn-primary float-end">
                            <?php echo t('Save'); ?>
                        </button>
                    <?php } else { ?>
                        <button type="submit" class="btn btn-primary float-end">
                            <?php echo t('Add'); ?>
                        </button>
                    <?php } ?>
                </div>
            </div>
        </form>
        <?php
        break;

    default: ?>
        <h3>
            <?php echo t('External File Providers'); ?>
        </h3>

        <?php if (count($externalFileProviders) === 0) { ?>
            <p>
                <?php echo t("You don't have created any external file providers yet."); ?>
            </p>
        <?php } else { ?>
            <ul class="item-select-list">
                <?php foreach ($externalFileProviders as $externalFileProvider) { ?>
                    <li>
                        <a href="<?php echo $view->action('edit', $externalFileProvider->getID()); ?>">
                            <i class="fas fa-file-export"></i> <?php echo $externalFileProvider->getDisplayName(); ?>
                        </a>
                    </li>
                <?php } ?>
            </ul>
        <?php } ?>

        <form method="get" action="<?php echo $view->action('select_type'); ?>"
              id="ccm-external-file-provider-type-form">
            <fieldset>
                <legend>
                    <?php echo t('Add External File Provider'); ?>
                </legend>

                <?php if (count($types) === 0) { ?>
                    <p>
                        <?php echo t("Currently are no external file provider types available."); ?>
                    </p>
                <?php } else { ?>
                <div class="form-group">
                    <label for="atID">
                        <?php echo t('Choose Type'); ?>
                    </label>

                    <div class="row row-cols-auto g-0 align-items-center">
                        <div class="col-auto me-2">
                            <?php echo $form->select('efpTypeID', $types); ?>
                        </div>
                        <div class="col-auto">
                            <button type="submit" class="btn btn-secondary">
                                <?php echo t('Go'); ?>
                            </button>
                        </div>
                    </div>
                </div>
        <?php } ?>
            </fieldset>
        </form>
        <?php
        break;
}
