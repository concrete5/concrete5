<?php

defined('C5_EXECUTE') or die('Access Denied.');

/**
 * @var Concrete\Core\Application\Service\Dashboard $dashboard
 * @var Concrete\Core\Form\Service\Form $form
 * @var Concrete\Core\Html\Service\Html $html
 * @var Concrete\Core\Application\Service\UserInterface $interface
 * @var Concrete\Core\Validation\CSRF\Token $token
 * @var Concrete\Core\Page\View\PageView $view
 * @var Concrete\Controller\SinglePage\Dashboard\System\Optimization\Jobs $controller
 * @var Concrete\Core\Localization\Service\Date $dh
 * @var Concrete\Core\Url\Resolver\Manager\ResolverManagerInterface $urlResolver
 * @var string $auth
 * @var Concrete\Core\Job\Job[] $installedJobs
 * @var Concrete\Core\Job\Job[] $availableJobs
 * @var Concrete\Core\Job\Set|null $defaultJobSet
 * @var Concrete\Core\Job\Set|true|null $editingJobSet
 * @var Concrete\Core\Job\Set[] $jobSets
 * @var string $activeTab
 */

if ($editingJobSet !== null) {
    if ($editingJobSet !== true && $editingJobSet->canDelete()) {
        ?>
        <div class="d-none">
            <div data-dialog-wrapper="delete-job-set">
                <form method="post" action="<?= $controller->action('delete_set', $editingJobSet->getJobSetID()) ?>">
                    <?php $token->output("delete_set{$editingJobSet->getJobSetID()}") ?>
                    <p><?= t('Warning, this cannot be undone. No jobs will be deleted but they will no longer be grouped together.') ?></p>
                    <div class="dialog-buttons">
                        <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                        <button class="btn btn-danger float-end" onclick="$('div[data-dialog-wrapper=delete-job-set] form').submit()"><?=t('Delete Job Set')?></button>
                    </div>
                </form>
            </div>
        </div>
        <?php
    }
    ?>
    <form class="form-vertical" method="post" action="<?= $controller->action('update_set', $editingJobSet === true ? 'new' : $editingJobSet->getJobSetID()) ?>">
        <?php $token->output('update_set' . ($editingJobSet === true ? 'new' : $editingJobSet->getJobSetID())) ?>
        <div class="row">
            <div class="col-md">
                <fieldset>
                    <legend><?= t('Details') ?></legend>
                    <div class="form-group">
                        <?= $form->label('jsName', t('Name')) ?>
                        <?= $form->text('jsName', $editingJobSet === true ? '' : $editingJobSet->getJobSetName(), ['required' => 'required', 'maxlength' => '128']) ?>
                    </div>
                </fieldset>
            </div>
            <div class="col-md">
                <fieldset>
                    <legend><?= t('Jobs') ?></legend>
                    <?php
                    if ($installedJobs !== []) {
                        ?>
                        <div class="form-group">
                            <?php
                            foreach ($installedJobs as $g) {
                                ?>
                                <div class="form-check">
                                    <?= $form->checkbox('jID[]', $g->getJobID(), $editingJobSet === true ? false : $editingJobSet->contains($g), ['id' => "job-{$g->getJobID()}-for-set"]) ?>
                                    <label class="form-check-label" for="job-<?= $g->getJobID() ?>-for-set"><?= h($g->getJobName()) ?></label>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                        <?php
                    } else {
                        ?>
                        <p><?= t('No Jobs found.') ?></p>
                        <?php
                    }
                    ?>
                </fieldset>
            </div>
        </div>
        <?php
        $isScheduled = $editingJobSet === true ? false : (bool) $editingJobSet->isScheduled;
        ?>
        <div class="card card-body bg-light">
            <h4><?= t('Automation Instructions') ?></h4>
            <?= $form->select(
            'isScheduled',
            [
                '1' => t('Run job set when people browse a page'),
                '0' => t('Run job set through cron'),
            ],
            $isScheduled ? '1' : '0',
            [
                'class' => 'ccm-jobs-automation-schedule-type',
            ]
        ) ?>
            <fieldset class="mt-3 ccm-jobs-automation-schedule-auto<?= $isScheduled ? '' : ' d-none' ?>">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= $form->label('value', t('Run this Job Set Every')) ?>
                            <div class="input-group">
                                <?= $form->number('value', $editingJobSet === true ? 0 : $editingJobSet->scheduledValue, ['min' => '0']) ?>
                                <?= $form->select(
                'unit',
                [
                    'hours' => t('Hours'),
                    'days' => t('Days'),
                    'weeks' => t('Weeks'),
                    'months' => t('Months'),
                ],
                $editingJobSet === true ? 'days' : $editingJobSet->scheduledInterval
            ) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <fieldset class="ccm-jobs-automation-schedule-cron<?= $isScheduled ? ' d-none' : '' ?>">
                <?php
                if ($editingJobSet === true) {
                    ?>
                    <p><?= t("To run all the jobs in this Job Set, once created you'll be provided an URL to be scheduled using cron or a similar system.") ?></p>
                    <?php
                } else {
                    ?>
                    <p><?= t('To run all the jobs in this Job Set, schedule this URL using cron or a similar system:') ?></p>
                    <?= $form->textarea(
                        '',
                        (string) $urlResolver->resolve(["/ccm/system/jobs?auth={$auth}&jsID={$editingJobSet->getJobSetID()}"]),
                        [
                            'class' => 'ccm-default-jobs-url',
                            'rows' => '2',
                            'readonly' => 'readonly',
                        ]
                    ) ?>
                    <?php
                }
                ?>
            </fieldset>
        </div>

        <div class="ccm-dashboard-form-actions-wrapper">
            <div class="ccm-dashboard-form-actions">
                <div class="float-start">
                    <a class="btn btn-secondary" href="<?= $controller->action('view_sets') ?>" ><?= t('Cancel') ?></a>
                </div>
                <div class="float-end">
                    <?php
                    if ($editingJobSet !== true && $editingJobSet->canDelete()) {
                        ?>
                        <a href="javascript:void(0)" class="btn btn-danger" data-dialog="delete-job-set"><?= t('Delete Job Set') ?></a>
                        <?php
                    }
                    ?>
                    <button class="btn btn-primary" type="submit" ><?= $editingJobSet === true ? t('Create Job Set') : t('Update Job Set') ?></button>
                </div>
            </div>
        </div>

    </form>
    <?php
} else {
    echo $interface->tabs([
        ['jobs', t('Jobs'), $activeTab === 'jobs'],
        ['jobSets', t('Job Sets'), $activeTab === 'jobSets'],
    ]);

    ?>
    <div class="tab-content">

        <div class="tab-pane<?= $activeTab === 'jobs' ? ' show active' : '' ?>" id="jobs" role="tabpanel">
            <?php
            if ($installedJobs !== []) {
                ?>
                <table class="table table-striped" id="ccm-jobs-list">
                    <thead>
                        <tr>
                            <th><?= t('ID') ?></th>
                            <th><?= t('Name') ?></th>
                            <th><?= t('Last Run') ?></th>
                            <th><?= t('Results of Last Run') ?></th>
                            <th colspan="3"><a href="<?= $controller->action('reset', $token->generate('reset_jobs')) ?>" class="btn btn-secondary float-end btn-sm"><?= t('Reset All Jobs') ?></a></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $uninstallToken = $token->generate('uninstall_job');
                        foreach ($installedJobs as $j) {
                            ?>
                            <tr class="<?= $j->didFail() ? ' table-danger' : '' ?><?= $j->getJobStatus() === 'RUNNING' ? ' running' : '' ?>">
                                <td><?= $j->getJobID() ?></td>
                                <td><i class="fas fa-question-circle launch-tooltip" title="<?= h($j->getJobDescription()) ?>"></i> <?= h($j->getJobName()) ?></td>
                                <td class="jDateLastRun">
                                    <?php
                                    if ($j->getJobStatus() === 'RUNNING') {
                                        echo '<strong>', t('Running since %s', $dh->formatDateTime($j->getJobDateLastRun(), true, true)), '</strong>';
                                    } elseif (empty($j->getJobDateLastRun()) || substr((string) $j->getJobDateLastRun(), 0, 4) == '0000') {
                                        echo t('Never');
                                    } else {
                                        echo $dh->formatDateTime($j->getJobDateLastRun(), true, true);
                                    }
                                    ?>
                                </td>
                                <td class="jLastStatusText"><?= $j->getJobLastStatusText() ?></td>
                                <td class="ccm-jobs-button">
                                    <button data-jID="<?= $j->getJobID() ?>" data-jSupportsQueue="<?= $j->supportsQueue() ?>" data-jName="<?= h($j->getJobName()) ?>" class="btn-run-job btn btn-secondary btn-sm float-end"><i class="fas fa-play"></i> <?= t('Run') ?></button>
                                </td>
                                <td style="width: 25px">
                                    <a href="javascript:void(0)" class="ccm-automate-job-instructions btn btn-secondary btn-sm icon-link launch-tooltip" data-jSupportsQueue="<?= $j->supportsQueue() ?>" data-jID="<?= $j->getJobID() ?>" title="<?= t('Automate this Job') ?>"><i class="far fa-clock"></i></a>
                                </td>
                                <td style="width: 25px">
                                    <?php
                                    if ($j->canUninstall()) {
                                        ?>
                                        <a href="<?= $controller->action('uninstall', $j->getJobID(), $uninstallToken) ?>" class="icon-link launch-tooltip btn btn-danger btn-sm btn-uninstall-job" title="<?= t('Remove this Job') ?>"><i class="far fa-trash-alt"></i></a>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <div class="d-none">
                    <?php
                    foreach ($installedJobs as $j) {
                        ?>
                        <div id="jd<?= $j->getJobID() ?>" class="ccm-ui" title="<?= t('Automation Instructions') ?>">
                            <form action="<?= $controller->action('update_job_schedule', $j->getJobID()) ?>" method="post" data-schedule-form="<?= $j->getJobID() ?>">
                                <?php $token->output("update_job_schedule{$j->getJobID()}") ?>
                                <?= $form->select(
                            "isScheduled{$j->getJobID()}",
                            [
                                '1' => t('Run job when people browse a page'),
                                '0' => t('Run job through cron'),
                            ],
                            $j->isScheduled ? '1' : '0',
                            [
                                'class' => 'ccm-jobs-automation-schedule-type',
                            ]
                        ) ?>
                                <div class="mt-3 ccm-jobs-automation-schedule-auto<?= $j->isScheduled ? '' : ' d-none' ?>">
                                    <div class="form-group">
                                        <?= $form->label("value{$j->getJobID()}", t('Run this Job Every')) ?>
                                        <div class="input-group">
                                            <?= $form->number("value{$j->getJobID()}", $j->scheduledValue, ['min' => '0']) ?>
                                            <?= $form->select(
                                    "unit{$j->getJobID()}",
                                    [
                                        'minutes' => t('Minutes'),
                                        'hours' => t('Hours'),
                                        'days' => t('Days'),
                                        'weeks' => t('Weeks'),
                                        'months' => t('Months'),
                                    ],
                                    $j->scheduledInterval
                                ) ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 ccm-jobs-automation-schedule-cron<?= $j->isScheduled ? ' d-none' : '' ?>">
                                    <?php
                                    if ($j->supportsQueue()) {
                                        ?>
                                        <p><?= t('The "%s" job supports queueing, meaning it can be run in a couple different ways:', h($j->getJobName())) ?></p>

                                        <h4><?= t('No Queueing') ?></h4>
                                        <p><?= t('This will treat the job as though it were like any other concrete5 job. The entire job will be run at once.') ?></p>
                                        <?= $form->textarea(
                                            '',
                                            (string) $urlResolver->resolve(["/ccm/system/jobs?auth={$auth}&jID={$j->getJobID()}"]),
                                            [
                                                'class' => 'ccm-default-jobs-url',
                                                'rows' => '2',
                                                'readonly' => 'readonly',
                                            ]
                                        ) ?>

                                        <h4><?= t('Queueing') ?></h4>
                                        <p><?= t("First, schedule this URL for when you'd like this job to run:") ?></p>
                                        <?= $form->textarea(
                                            '',
                                            (string) $urlResolver->resolve(["/ccm/system/jobs/run_single?auth={$auth}&jID={$j->getJobID()}"]),
                                            [
                                                'class' => 'ccm-default-jobs-url',
                                                'rows' => '2',
                                                'readonly' => 'readonly',
                                            ]
                                        ) ?>
                                        <p><?= t('Then, make sure this URL is scheduled to run frequently, like every 3-5 minutes:') ?></p>
                                        <?= $form->textarea(
                                            '',
                                            (string) $urlResolver->resolve(["/ccm/system/jobs/check_queue?auth={$auth}"]),
                                            [
                                                'class' => 'ccm-default-jobs-url',
                                                'rows' => '2',
                                                'readonly' => 'readonly',
                                            ]
                                        ) ?>
                                        <p><?= t('The first URL starts the process - the second ensures that it completes in batches.') ?></p>
                                        <?php
                                    } else {
                                        ?>
                                        <p><?= t('To run the "%s" job, automate the following URL using cron or a similar system:', h($j->getJobName())) ?></p>
                                        <?= $form->textarea(
                                            '',
                                            (string) $urlResolver->resolve(["/ccm/system/jobs/run_single?auth={$auth}&jID={$j->getJobID()}"]),
                                            [
                                                'class' => 'ccm-default-jobs-url',
                                                'rows' => '2',
                                                'readonly' => 'readonly',
                                            ]
                                        ) ?>
                                        <?php
                                    }
                                    ?>
                                </div>
                                <div class="dialog-buttons">
                                    <button class="btn btn-secondary float-start" onclick="jQuery.fn.dialog.closeTop()"><?= t('Cancel') ?></button>
                                    <button onclick="$('form[data-schedule-form=<?= $j->getJobID() ?>]').submit()" class="btn btn-primary float-end"><?= t('Save') ?></button>
                                </div>
                            </form>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <?php
            } else {
                ?>
                <p><?= t('You have no jobs installed.') ?></p>
                <?php
            }

            if ($availableJobs !== []) {
                ?>
                <h4><?= t('Awaiting Installation') ?></h4>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th><?= t('Name') ?></th>
                            <th><?= t('Description') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $installToken = $token->generate('install_job');
                        foreach ($availableJobs as $job) {
                            ?>
                            <tr>
                                <td><?= h($job->getJobName()) ?></td>
                                <td><?= h($job->getJobDescription()) ?></td>
                                <td>
                                    <?php
                                    if (!$job->invalid) {
                                        ?>
                                        <a href="<?= $controller->action('install', $job->jHandle, $installToken) ?>" class="btn btn-sm btn-secondary float-end"><?= t('Install') ?></a>
                                        <?php
                                    }
                                    ?>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            }

            if ($defaultJobSet !== null) {
                ?>
                <div class="card card-body bg-light">
                    <h5><?= t('Automation Instructions') ?></h5>
                    <p><?= t(
                    'To run all the jobs in the <a href="%s">%s</a> Job Set, schedule this URL using cron or a similar system:',
                    h((string) $controller->action('edit_set', $defaultJobSet->getJobSetID())),
                    h($defaultJobSet->getJobSetDisplayName())
                ) ?></p>
                    <?= $form->text(
                        '',
                        (string) $urlResolver->resolve(["/ccm/system/jobs?auth={$auth}"]),
                        [
                            'class' => 'ccm-default-jobs-url',
                            'readonly' => 'readonly',
                        ]
                    ) ?>
                </div>
                <?php
            }
            ?>
        </div>

        <div class="tab-pane<?= $activeTab === 'jobSets' ? ' show active' : '' ?>" id="jobSets" role="tabpanel">
            <?php
            if ($jobSets !== []) {
                ?>
                <ul class="item-select-list" id="ccm-job-set-list">
                    <?php
                    foreach ($jobSets as $j) {
                        ?>
                        <li>
                            <a href="<?= $controller->action('edit_set', $j->getJobSetID()) ?>">
                                <i class="fas fa-bars"></i> <?= $j->getJobSetDisplayName() ?>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
            } else {
                ?>
                <p><?= t('You have not added any Job sets.') ?></p>
                <?php
            }
            ?>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <div class="float-end">
                        <a class="btn btn-primary" href="<?= $controller->action('edit_set', 'new') ?>" ><?= t('Add Job Set') ?></a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php
}
?>
<script>
$(document).ready(function() {
    'use strict';

    var pulseRowInterval = null;

    $.fn.showLoading = function() {
        var $row = $(this);
        if ($row.find('button').attr('data-jSupportsQueue')) {
            $row.find('button').html('<i class="fas fa-sync fa-spin"></i> <?= t('View') ?>');
        } else {
            $row.find('button').html('<i class="fas fa-sync fa-spin"></i> <?= t('Run') ?>').prop('disabled', true);
        }
        $row.removeClass('table-danger table-success');

        if (!$row.attr('data-color')) {
            $row.find('td').css('background-color', '#ccc');
        }
        pulseRowInterval = setInterval(function() {
            if ($row.attr('data-color') == '#ccc') {
                $row.find('td').css('background-color', '#fff');
                $row.attr('data-color', '#fff');
            } else {
                $row.find('td').css('background-color', '#ccc');
                $row.attr('data-color', '#ccc');
            }
        }, 500);
    }

    $.fn.hideLoading = function() {
        $(this).find('button').html('<i class="fa fa-play"></i> <?= t('Run') ?>').prop('disabled', false);
        var $row = $(this);
        $row.removeClass();
        $row.find('td').css('background-color', '');
        $row.attr('data-color', '');
        if (pulseRowInterval !== null) {
            clearInterval(pulseRowInterval);
            pulseRowInterval = null;
        }
    }

    jQuery.fn.processResponse = function(r) {
        var $this = $(this);
        $this.hideLoading();
        if (r.error) {
            $this.addClass('table-danger');
        } else {
            $this.addClass('table-success');
        }
        $this.find('.jDateLastRun').html(r.jDateLastRun);
        $this.find('.jLastStatusText').html(r.result);
    }

    $('tr.running').showLoading();

    $('.btn-run-job').on('click', $('#ccm-jobs-list'), function() {
        var $this = $(this),
            $row = $this.closest('tr'),
            jSupportsQueue = $this.attr('data-jSupportsQueue'),
            jID = $this.attr('data-jID'),
            jName = $this.attr('data-jName'),
            params = [
                {name: 'auth', value: <?= json_encode($auth) ?>},
                {name: 'jID', value: jID}
            ]
        ;
        $row.showLoading();
        if (jSupportsQueue) {
            new ConcreteProgressiveOperation({
                url: <?= json_encode((string) $urlResolver->resolve(['/ccm/system/jobs/run_single'])) ?>,
                data: params,
                title: jName,
                onComplete: function(r) {
                    $('.ui-dialog-content').dialog('close');
                    $row.processResponse(r);
                }
            });
        } else {
            $.ajax({
                url: <?= json_encode((string) $urlResolver->resolve(['/ccm/system/jobs/run_single'])) ?>,
                data: params,
                dataType: 'json',
                cache: false,
                success: function(json) {
                    $row.processResponse(json);
                }
            });
        }
    });

    $('.ccm-default-jobs-url').on('click', function() {
        $(this).select();
    });

    $('a.ccm-automate-job-instructions').on('click', $("#ccm-jobs-list"), function() {
        $(this).blur();
        jQuery.fn.dialog.open({
            element: '#jd' + $(this).attr("data-jID"),
            height: 550,
            width: 650,
            modal: true,
        });
    });

    $('.btn-uninstall-job').on('click', function (e) {
        if (!confirm(<?= json_encode(t('Are you sure you want to uninstall this job?')) ?>)) {
            e.preventDefault();
            return false;
        }
    });

    $('.ccm-jobs-automation-schedule-type')
        .on('change', function() {
            var $this = $(this),
                $form = $this.closest('form');
            $form.find('.ccm-jobs-automation-schedule-cron').toggleClass('d-none', $this.val() == 1);
            $form.find('.ccm-jobs-automation-schedule-auto').toggleClass('d-none', $this.val() != 1);
        })
        .trigger('change')
    ;

});
</script>
