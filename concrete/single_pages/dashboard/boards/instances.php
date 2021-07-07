<?php

defined('C5_EXECUTE') or die("Access Denied.");

?>

<div class="row">
    <div class="col-4">
        <?php
        $element = Element::get('dashboard/boards/menu', ['board' => $board, 'action' => 'instances']);
        $element->render();
        ?>
    </div>
    <div class="col-8">

        <h3><?=t('Instances')?></h3>

        <?php if ($instances) { ?>

            <table class="table table-striped">
            <thead>
                <tr>
                    <th class="w-50"><?=t('Name')?></th>
                    <th class="w-50"><?=t('Date Created')?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($instances as $instance) {
                    $instanceName = $instance->getBoardInstanceName();
                    if (!$instanceName) {
                        $instanceName = t('(Untitled)');
                    }
                    ?>
                    <tr>
                        <td><a href="<?=URL::to('/dashboard/boards/instances/details',
                            $instance->getBoardInstanceID())?>"><?=$instanceName?></a>
                        </td>
                        <td><?=$instance->getDateCreatedObject()->format('Y-m-d H:i:s')?></td>

                    </tr>
                <?php } ?>
            </tbody>

            </table>
        <?php } else { ?>

            <p><?=t('No board instances found.')?></p>

        <?php } ?>

        <hr>

        <h3><?=t("Generate New Instance")?></h3>

        <p class="help-block"><?=t("Create a completely new instance of this board, based on the current data sources, templates and rules. ")?></p>

        <button type="button" data-bs-toggle="modal" data-bs-target="#generate-instance" class="btn btn-lg btn-primary"><?=t("Generate")?></button>

        <div class="modal fade" id="generate-instance" tabindex="-1">
            <form method="post" action="<?=$view->action('generate_instance', $board->getBoardID())?>">
                <?=$token->output('generate_instance')?>
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"><?=t('Generate Instance')?></h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="<?= t('Close') ?>"></button>
                        </div>
                        <div class="modal-body">
                            <div class="form-group">
                                <?=$form->label('boardInstanceName', t('Name'))?>
                                <?=$form->text('boardInstanceName')?>
                            </div>
                            <?php
                            if (!$board->getSite()) {
                                // That means this is a shared board. So we give the admin the option of which site
                                // they want the instance to go in. Instances HAVE to live in a site.
                            ?>
                            <div class="form-group">
                                <?=$form->label('siteID', t('Name'))?>
                                <?php
                                $siteService = app()->make('site');
                                $sites = array('' => t('** Select Site'));
                                $activeSite = $siteService->getActiveSiteForEditing();
                                foreach($siteService->getList() as $site) {
                                    $sites[$site->getSiteID()] = $site->getSiteName();
                                }
                                print $form->select('siteID', $sites, $activeSite->getSiteID());
                                ?>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=t('Cancel')?></button>
                            <button type="submit" class="btn btn-primary float-end"><?=t('Create Instance')?></button>
                        </div>
                    </div>
                </div>
            </form>
        </div>



    </div>
</div>
