<?php
defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Doctrine\ORM\Mapping\Driver\AnnotationDriver[] $drivers
 * @var string $doctrine_dev_mode
 */

?>

<form method="post" id="entities-settings-form" action="<?= $view->action('update_entity_settings'); ?>" style="position: relative">
    <?php $token->output('update_entity_settings'); ?>

    <fieldset>
        <legend><?= t('Settings'); ?></legend>
        <div class="form-group">
            <label class="launch-tooltip" data-bs-placement="right" title="<?= t('Defines whether the Doctrine proxy classes are created on the fly. On the fly generation is active when development mode is enabled.'); ?>">
                <?= t('Doctrine Development Mode'); ?>
            </label>

            <div class="form-check">
                <?= $form->radio('DOCTRINE_DEV_MODE', 'yes', $doctrine_dev_mode); ?>
                <?= $form->label('DOCTRINE_DEV_MODE1', t('On - Proxy classes will be generated on the fly. Good for development.'), ['class' => 'form-check-label']); ?>
            </div>

            <div class="form-check">
                <?= $form->radio('DOCTRINE_DEV_MODE', 'no', $doctrine_dev_mode); ?>
                <?= $form->label('DOCTRINE_DEV_MODE2', t('Off - Proxy classes need to be manually generated. Helps speed up a live site.'), ['class' => 'form-check-label']); ?>
            </div>
        </div>
    </fieldset>

    <fieldset>
        <legend><?=t('Entities'); ?></legend>

        <div class="form-group">
            <?php
            foreach ($drivers as $namespace => $driver) {
                ?>
                <h4><?= $namespace; ?></h4>
                <div class="row">
                    <div class="col-md-1"><span class="text-muted"><?= t('Paths'); ?></span></div>
                    <div class="col-md-11">
                        <?php
                            if ($driver instanceof Doctrine\Common\Persistence\Mapping\Driver\AnnotationDriver) {
                                $paths = $driver->getPaths();
                            } elseif ($driver instanceof Doctrine\Common\Persistence\Mapping\Driver\FileDriver) {
                                $paths = $driver->getLocator()->getPaths();
                            } else {
                                $paths = [];
                            }

                            if (!empty($paths)) {
                                foreach ($paths as $path) {
                                    $shownPath = str_replace('/', DIRECTORY_SEPARATOR, strpos($path, DIR_BASE) === 0 ? substr($path, strlen(DIR_BASE) + 1) : $path); ?><small><?= $shownPath; ?></small><br><?php
                                }
                            }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-1"><span class="text-muted"><?= t('Driver'); ?></span></div>
                    <div class="col-md-11">
                        <?= get_class($driver); ?>
                    </div>
                </div>
                <hr/>
                <?php
            }
            ?>
        </div>
    </fieldset>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="float-start btn btn-danger" name="refresh" value="1" type="submit"><?= t('Refresh Entities'); ?></button>
            <button class="float-end btn btn-primary" type="submit"><?= t('Save'); ?></button>
        </div>
    </div>

</form>
