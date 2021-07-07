<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Controller\SinglePage\Dashboard\System\Update\Update $controller
 * @var Concrete\Core\Localization\Service\Date $dh
 * @var string $currentVersion
 * @var Concrete\Core\Updater\ApplicationUpdate[] $updates
 * @var Concrete\Core\Updater\RemoteApplicationUpdate|null $remoteUpdate
 */
?>

<fieldset>
    <legend><?= t('Current Version') ?></legend>
    <?= t('You are currently running concrete5 version %s', '<strong>' . h($currentVersion) . '</strong>') ?>
</fieldset>

<?php
if ($remoteUpdate !== null) {
    ?>
    <form method="POST" action="<?= $controller->action('download_update') ?>">
        <?php $token->output('download_update') ?>
        <input type="submit" id="ccm-update-download-submit" class="d-none" />
        <fieldset>
            <legend><?= t('Available Update for Download') ?></legend>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <dl>
                            <dt><?= t('Version') ?></dt>
                            <dd><?= h($remoteUpdate->getVersion()) ?>
                            <dt><?= t('Release Date') ?></dt>
                            <dd><?= h($dh->formatDate($remoteUpdate->getDate(), 'long')) ?></dd>
                        </dl>
                    </div>
                    <div class="col">
                        <dl>
                            <dt><?= t('Release Notes') ?></dt>
                            <dd>
                                <?php
                                if ((string) $remoteUpdate->getNotes() === '') {
                                    ?>
                                    <i><?= t('Release Notes not available.') ?></i>
                                    <?php
                                } else {
                                    ?>
                                    <div class="ccm-dashboard-update-detail-release-notes">
                                        <?= $remoteUpdate->getNotes() ?>
                                    </div>
                                    <?php
                                }
                                ?>
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>
        </fieldset>
    </form>
    <script>
        $('#ccm-update-download-submit').closest('form').on('submit', function(e) {
            var $form = $(this);
            ConcreteAlert.confirm(<?= json_encode(t('Note: Downloading an update will NOT automatically install it.')) ?>, function() {
                $form.off('submit').submit().on('submit', function(e) { e.preventDefault(); return false; });
            });
            e.preventDefault();
            return false;
        });
    </script>
    <?php
}
?>

<fieldset>
    <legend><?= t('Apply Downloaded Update') ?></legend>
    <?php
    if ($updates !== []) {
        ?>
        <p><?= t('Several updates are available. Please choose the desired update from the list below.') ?></p>
        <form method="POST" class="form" action="<?= $controller->action('start') ?>" id="ccm-update-form">
            <input type="submit" id="ccm-update-start-submit" class="d-none" />
            <?php
            foreach ($updates as $updateIndex => $update) {
                ?>
                <div class="form-check">
                    <?= $form->radio('updateVersion', $update->getUpdateVersion(), $updateIndex ? '' : $update->getUpdateVersion(), ['id' => "updateVersion{$updateIndex}", 'data-version' => h($update->getUpdateVersion())]) ?>
                    <label class="form-check-label" for="updateVersion<?= $updateIndex ?>"><?= h(t('Version %s', $update->getUpdateVersion())) ?></label>
                </div>
                <?php
            }
            ?>
        </form>
        <script>
        $(document).ready(function() {
            var $radios = $('input[type="radio"][name="updateVersion"]');
            $radios
                .on('change', function() {
                    $('label[for="ccm-update-start-submit"]').text(<?= json_encode(t('Install v%s', '[[VERSION]]')) ?>.replace(/\[\[VERSION\]\]/g, $radios.filter(':checked').data('version')));
                })
                .trigger('change')
            ;
        });
        </script>
        <?php
    } else {
        ?>
        <?= t('No updates are ready to be installed.') ?>
        <?php
    }
    ?>
</fieldset>

<div class="ccm-dashboard-form-actions-wrapper">
    <div class="ccm-dashboard-form-actions">
        <div class="float-end">
            <a href="<?= $controller->action('check_for_updates') ?>" class="btn btn-primary"><?= t('Check For Updates') ?></a>
            <?php
            if ($remoteUpdate !== null) {
                ?>
                <label for="ccm-update-download-submit" class="btn btn-success mb-0"><?= t('Download v.%s', h($remoteUpdate->getVersion())) ?></label>
                <?php
            }
            if ($updates !== []) {
                ?>
                <label for="ccm-update-start-submit" class="btn btn-success mb-0"></label>
                <?php
            }
            ?>
        </div>
    </div>
</div>
