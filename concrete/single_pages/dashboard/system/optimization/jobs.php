<?php defined('C5_EXECUTE') or die('Access Denied.');

/* @var $jh JsonHelper */
$json = Core::make('helper/json');
/* @var $dh \Concrete\Core\Localization\Service\Date */
$dh = Core::make('helper/date');

$token = Core::make('token');
$uninstallToken = $token->generate('uninstall_job');

?>
<style type="text/css">
#ccm-jobs-list td {
	vertical-align: middle;
	-webkit-transition-property: color, background-color;
	-webkit-transition-duration: .9s, .9s;
	-moz-transition-property: color, background-color;
	-moz-transition-duration: .9s, .9s;
	-o-transition-property: color, background-color;
	-o-transition-duration: .9s, .9s;
	-ms-transition-property: color, background-color;
	-ms-transition-duration: .9s, .9s;
	transition-property: color, background-color;
	transition-duration: .9s, .9s;
 }

#ccm-jobs-list td button {
 	float: right;
 }

#ccm-jobs-list tr.error td {
	color: #f00;
}

#ccm-jobs-list tr.success td {
	color: #090;
}

</style>

<?=Loader::helper('concrete/ui')->tabs(array(
    array($view->action('view'), t('Jobs'), $jobListSelected),
    array($view->action('view_sets'), t('Job Sets'), isset($jobSetsSelected) ? $jobSetsSelected : null),
), false);?>

<?php if (in_array($this->controller->getTask(), array('view', 'install', 'uninstall', 'job_installed', 'job_uninstalled', 'reset', 'reset_complete', 'job_scheduled'))) {
    ?>

<div id="ccm-tab-content-list">

<?php if (count($installedJobs) > 0) {
    ?>

<table class="table table-stripped" id="ccm-jobs-list">
	<thead>
    	<tr>
    		<th><?=t('ID')?></th>
    		<th style="width: 250px"><?=t('Name')?></th>
    		<th><?=t('Last Run')?></th>
    		<th style="width: 200px"><?=t('Results of Last Run')?></th>
    		<th colspan="3"><a href="<?=$view->action('reset', $token->generate('reset_jobs'))?>" class="btn btn-default pull-right btn-xs"><?=t('Reset All Jobs')?></a></th>
    	</tr>
	</thead>

	<tbody>
    	<?php foreach ($installedJobs as $j) {
    ?>
    		<tr class="<?php if ($j->didFail()) {
    ?>error<?php
}
    ?> <?php if ($j->getJobStatus() == 'RUNNING') {
    ?>running<?php
}
    ?>">
    			<td>
    			    <?=$j->getJobID()?>
                </td>

    			<td>
    			    <i class="fa fa-question-circle launch-tooltip" title="<?=$j->getJobDescription()?>"></i> <?=$j->getJobName()?>
                </td>

    			<td class="jDateLastRun"><?php
                    if ($j->getJobStatus() == 'RUNNING') {
                        $runtime = $dh->formatDateTime($j->getJobDateLastRun(), true, true);
                        echo "<strong>";
                        echo t("Running since %s", $runtime);
                        echo "</strong>";
                    } elseif ($j->getJobDateLastRun() == '' || substr($j->getJobDateLastRun(), 0, 4) == '0000') {
                        echo t('Never');
                    } else {
                        $runtime = $dh->formatDateTime($j->getJobDateLastRun(), true, true);
                        echo $runtime;
                    }
    ?></td>

    			<td class="jLastStatusText">
    			    <?=$j->getJobLastStatusText()?>
                </td>

    			<td class="ccm-jobs-button">
    				<button data-jID="<?=$j->getJobID()?>" data-jSupportsQueue="<?=$j->supportsQueue()?>" data-jName="<?=$j->getJobName()?>" class="btn-run-job btn btn-default btn-xs"><i class="fa fa-play"></i> <?=t('Run')?></button>
    			</td>

    			<td style="width: 25px">
    				<a href="javascript:void(0)" class="ccm-automate-job-instructions icon-link launch-tooltip" data-jSupportsQueue="<?=$j->supportsQueue()?>" data-jID="<?=$j->getJobID()?>" title="<?=t('Automate this Job')?>"><i class="fa fa-clock-o"></i></a>
    			</td>
                <td style="width: 25px">
                    <?php if ($j->canUninstall()) {
                        ?>
                        <a href="<?=$view->action('uninstall', $j->getJobID(), $uninstallToken)?>" class="icon-link launch-tooltip btn-uninstall-job" title="<?=t('Remove this Job')?>"><i class="fa fa-trash-o"></i></a>
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

<div style="display: none" id="ccm-jobs-automation-dialogs">
    <?php foreach ($installedJobs as $j) {
    ?>
    	<div id="jd<?=$j->getJobID()?>" class="ccm-ui">
    		<form action="<?=$view->action('update_job_schedule')?>" method="post" data-schedule-form="<?=$j->getJobID()?>">
    			<?=$form->hidden('jID', $j->getJobID()); ?>
				<?php
				$token->output('update_job_schedule');
				?>

    			<h4><?=t('Run Job')?></h4>

    			<div class="radio">
    			    <label>
        			    <input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="1" <?=($j->isScheduled ? 'checked="checked"' : '')?> />
        				<?=t('When people browse to the page.  (which runs after the main rendering request of the page.)')?>
    			    </label>
    			</div>

    			<fieldset class="ccm-jobs-automation-schedule-auto" <?=($j->isScheduled ? '' : 'style="display: none;"')?>>
    				<div class="well clearfix">
    				    <div class="form-group">
    					    <label><?php  echo t('Run this Job Every')?></label>

    						<div class="input">
    						    <div class="col-md-6">
    							    <?php echo $form->text('value', h($j->scheduledValue), array('class' => ''))?>
    						    </div>

    						    <div class="col-md-6">
    						        <?php echo $form->select('unit', array('minutes' => t('Minutes'), 'hours' => t('Hours'), 'days' => t('Days'), 'weeks' => t('Weeks'), 'months' => t('Months')), h($j->scheduledInterval), array('class' => ''))?>
    						    </div>
    						</div>
    				    </div>
    				</div>
    			</fieldset>

    			<div class="radio">
    			    <label>
        			    <input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="0" <?=($j->isScheduled ? '' : 'checked="checked"')?> />
                        <?=t('Through Cron')?>
    			    </label>
    			</div>

    			<fieldset class="ccm-jobs-automation-schedule-cron" <?=($j->isScheduled ? 'style="display: none;"' : '')?>>
    				<div class="well">
    					<?php if ($j->supportsQueue()) {
    ?>
    						<p><?=t('The "%s" job supports queueing, meaning it can be run in a couple different ways:', $j->getJobName())?></p>

    						<h4><?=t('No Queueing')?></h4>

    						<div class="form-group">
    						    <textarea rows="2" class="ccm-default-jobs-url form-control"><?=URL::to('/ccm/system/jobs?auth=' . $auth . '&jID=' . $j->getJobID())?></textarea>
                            </div>

    						<div class="alert alert-info">
    						    <?=t('This will treat the job as though it were like any other concrete5 job. The entire job will be run at once.')?>
                            </div>

    						<h4><?=t('Queueing')?></h4>

    						<p><?=t("First, schedule this URL for when you'd like this job to run:")?></p>

    						<div class="form-group">
    						    <textarea rows="2" class="ccm-default-jobs-url form-control"><?php echo URL::to('/ccm/system/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID())?></textarea>
                            </div>

    						<p><?=t('Then, make sure this URL is scheduled to run frequently, like every 3-5 minutes:')?></p>

    						<div class="form-group">
    						    <textarea rows="2" class="ccm-default-jobs-url form-control"><?=URL::to('/ccm/system/jobs/check_queue?auth=' . $auth)?></textarea>
    						</div>

    						<div class="alert alert-info">
    						    <?=t('The first URL starts the process - the second ensures that it completes in batches.')?>
                            </div>

    					<?php
} else {
    ?>
    						<p><?=t('To run the "%s" job, automate the following URL using cron or a similar system:', $j->getJobName())?></p><br/>
    						<div>
    						    <textarea rows="2" class="ccm-default-jobs-url form-control"><?=URL::to('/ccm/system/jobs/run_single?auth=' . $auth . '&jID=' . $j->getJobID())?></textarea>
                            </div>
    					<?php
}
    ?>
    				</div>
    			</fieldset>

    			<div class="dialog-buttons">
                    <button href="javascript:void(0)" class="btn btn-default pull-left" onclick="jQuery.fn.dialog.closeTop()"><?=t('Cancel')?></button>
                    <button type="button" onclick="$('form[data-schedule-form=<?=$j->getJobID()?>]').submit()" class="btn btn-primary pull-right"><?=t('Save')?></button>
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
	<p><?=t('You have no jobs installed.')?></p>
<?php
}
    ?>

<?php if (count($availableJobs) > 0) {
    ?>
	<h4><?=t('Awaiting Installation')?></h4>
	<table class="table table-striped">
	<thead>
		<tr>
			<th><?=t('Name')?></th>
			<th><?=t('Description')?></th>
			<th></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach ($availableJobs as $availableJobName => $job):?>
    		<tr>
    			<td><?=$job->getJobName() ?></td>
    			<td><?=$job->getJobDescription() ?></td>
    			<td><?php if (!$job->invalid):?>
    				<a href="<?=$view->action('install', $job->jHandle)?>" class="btn btn-sm btn-default pull-right"><?=t('Install')?></a>
    			<?php endif ?></td>
    		</tr>
		<?php endforeach ?>
	</tbody>
	</table>
<?php
}
    ?>
<?php
$djs = JobSet::getDefault();
    if (is_object($djs)) {
        ?>
<div class="well">
    <h4><?=t('Automation Instructions')?></h4>
    <p><?=t('To run all the jobs in the <a href="%s">%s</a> Job Set, schedule this URL using cron or a similar system:', $view->url('/dashboard/system/optimization/jobs', 'edit_set', $djs->getJobSetID()), $djs->getJobSetDisplayName())?></p>
    <div>
        <input type="text" class="ccm-default-jobs-url form-control ccm-input-text" value="<?=$view->url('/ccm/system/jobs?auth=' . $auth)?>" />
    </div>
</div>
<?php
    }
    ?>

</div>

<?php
} else {
    ?>

<div id="ccm-tab-content-sets">

<?php if (in_array($this->controller->getTask(), array('update_set', 'update_set_jobs', 'edit_set', 'delete_set'))) {
    ?>
	<h2><?=$set->getJobSetName()?></h2>

	<div class="row">
	    <div class="col-md-6">
            <form class="form-vertical" method="post" action="<?php echo $view->action('update_set')?>">

                <input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />

                <?php echo Loader::helper('validation/token')->output('update_set')?>

        		<fieldset>
        			<legend><?=t('Details')?></legend>

        			<div class="form-group">
        				<?php echo $form->label('jsName', t('Name'))?>
        				<div class="input">
        					<?php echo $form->text('jsName', $set->getJobSetName())?>
        				</div>
        			</div>

        			<div class="form-group">
        				<div class="input">
        				    <button type="submit" class="btn btn-primary"><?=t('Update Job Set')?></button>
        				</div>
        			</div>
        		</fieldset>
            </form>

    		<?php if ($set->canDelete()) {
    ?>

        		<form method="post" action="<?php echo $view->action('delete_set')?>" class="form-vertical">
            		<fieldset>
            			<legend><?=t('Delete Set')?></legend>

            			<?php echo Loader::helper('validation/token')->output('delete_set')?>
            			<input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />

            			<p><?php echo t('Warning, this cannot be undone. No jobs will be deleted but they will no longer be grouped together.')?></p>

                		<div class="form-group">
            				<div class="input">
            				    <button type="submit" class="btn btn-primary"><?=t('Delete Job Set')?></button>
            				</div>
            			</div>
            		</fieldset>
        		</form>
    		<?php
}
    ?>
        </div>

        <div class="col-md-6">
            <form class="form-vertical" method="post" action="<?php echo $view->action('update_set_jobs')?>">
                <input type="hidden" name="jsID" value="<?php echo $set->getJobSetID()?>" />
                <?php echo Loader::helper('validation/token')->output('update_set_jobs')?>

                <fieldset>
                    <legend><?=t('Jobs')?></legend>

        			<?php
                    $list = $set->getJobs();
    if (count($installedJobs) > 0) {
        ?>
                        <?php foreach ($installedJobs as $g) {
    ?>
                            <div class="checkbox">
    							<label>
    							    <?php echo $form->checkbox('jID[]', $g->getJobID(), $set->contains($g)) ?>
                                    <span><?php echo $g->getJobName()?></span>

    							</label>
                            </div>
                        <?php
}
        ?>

                        <div class="form-group">
            				<div class="input">
            				    <button type="submit" class="btn btn-primary"><?=t('Update Jobs')?></button>
            				</div>
            			</div>
        			<?php
    } else {
        ?>
        				<p><?php echo t('No Jobs found.')?></p>
        			<?php
    }
    ?>
                </fieldset>
            </form>
        </div>
	</div>

	<br />

    <div class="row">
        <div class="col-md-12">
            <div class="well">
		        <h4><?=t('Automation Instructions')?></h4>

                <form action="<?=$view->action('update_set_schedule');
    ?>" method="post">
			        <?=$form->hidden('jsID', $set->getJobSetID()); ?>
					<?php
					$token->output('update_set_schedule');
					?>
                    <div class="radio">
                        <label>
                            <input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="1" <?=($set->isScheduled ? 'checked="checked"' : '')?> />
                            <?=t('When people browse to the page.  (which runs after the main rendering request of the page.)')?>
                        </label>
                    </div>

                    <fieldset class="ccm-jobs-automation-schedule-auto" <?=($set->isScheduled ? '' : 'style="display: none;"')?>>
    				    <div class="col-md-3">
        				    <div class="form-group">
        				        <label><?php  echo t('Run this Job Every')?></label>
            					<div class="input">
            						<?php echo $form->text('value', h($set->scheduledValue), array('class' => 'col-md-6'))?>
            					</div>
        				    </div>
    				    </div>

    				    <div class="col-md-3">
    				        <div class="form-group">
    				            <label></label>
    				            <div class="input">
            						<?php echo $form->select('unit', array('hours' => t('Hours'), 'days' => t('Days'), 'weeks' => t('Weeks'), 'months' => t('Months')), h($set->scheduledInterval), array('class' => ''))?>
            					</div>
    				        </div>
    				    </div>
                    </fieldset>

                    <div class="radio">
                        <label>
                            <input type="radio" name="isScheduled" class="ccm-jobs-automation-schedule-type" value="0" <?=($set->isScheduled ? '' : 'checked="checked"')?> />
                            <?=t('Through Cron')?>
                        </label>
                    </div>

                    <fieldset class="ccm-jobs-automation-schedule-cron" <?=($set->isScheduled ? 'style="display: none;"' : '')?>>
    				    <p><?=t('To run all the jobs in this Job Set, schedule this URL using cron or a similar system:', $set->getJobSetID())?></p>

                        <div class="form-group">
                            <textarea rows="2" class="ccm-default-jobs-url form-control"><?=$view->url('/tools/required/jobs?auth=' . $auth . '&jsID=' . $set->getJobSetID())?></textarea>
                        </div>
                    </fieldset>

    			<div class="form-group">
    				<?php echo $form->submit('submit', t('Update Schedule'), array('class' => 'btn-primary'))?>
    			</div>
            </form>
            </div>
        </div>
	</div>

<?php
} else {
    ?>

<div class="row">
    <div class="col-md-12">
    	<form method="post" action="<?php echo $view->action('add_set')?>">
            <?php if (count($jobSets) > 0) {
    ?>
        	    <ul class="item-select-list" id="ccm-job-set-list">
        	        <?php foreach ($jobSets as $j) {
    ?>
        	            <li id="jsID_<?=$j->getJobSetID()?>">
                            <a href="<?php echo $view->url('/dashboard/system/optimization/jobs', 'edit_set', $j->getJobSetID())?>">
                                <i class="fa fa-bars"></i> <?php echo $j->getJobSetDisplayName()?>
                            </a>
                        </li>
        	        <?php
}
    ?>
        	    </ul>
        	<?php
} else {
    ?>
        		<p><?php echo t('You have not added any Job sets.')?></p>
        	<?php
}
    ?>

            <br/>

            <h3><?=t('Add Set')?></h3>

        	<?php echo Loader::helper('validation/token')->output('add_set')?>
        	<div class="form-group">
        		<?php echo $form->label('jsName', t('Name'))?>
        		<div class="input">
        			<?php echo $form->text('jsName')?>
        		</div>
        	</div>

        	<div class="form-group">
        		<label><?=t('Jobs')?></label>
        		<?php foreach ($installedJobs as $g) {
    ?>
                    <div class="checkbox">
    					<label>
    					    <?php echo $form->checkbox('jID[]', $g->getJobID()) ?>
                            <span><?php echo $g->getJobName()?></span>
    					</label>
                    </div>
                <?php
}
    ?>
        	</div>

        	<div class="well clearfix">
        	    <button type="submit" class="btn btn-primary pull-right"><?=t('Add Job Set')?></button>
        	</div>

        </form>
    </div>
</div>

	<?php
}
    ?>
</div>
<?php
} ?>


<script type="text/javascript">

var pulseRowInterval = false;

jQuery.fn.showLoading = function() {
	if ($(this).find('button').attr('data-jSupportsQueue')) {
		$(this).find('button').html('<i class="fa fa-refresh fa-spin"></i> <?=t('View')?>');
	} else {
		$(this).find('button').html('<i class="fa fa-refersh fa-spin"></i> <?=t('Run')?>').prop('disabled', true);
	}
	var row = $(this);
	row.removeClass('error success');

	if (!row.attr('data-color')) {
		row.find('td').css('background-color', '#ccc');
	}
	pulseRowInterval = setInterval(function() {
		if (row.attr('data-color') == '#ccc') {
			row.find('td').css('background-color', '#fff');
			row.attr('data-color', '#fff');
		} else {
			row.find('td').css('background-color', '#ccc');
			row.attr('data-color', '#ccc');
		}
	}, 500);
}

jQuery.fn.hideLoading = function() {
	$(this).find('button').html('<i class="fa fa-play"></i> <?=t('Run')?>').prop('disabled', false);
	var row = $(this);
	row.removeClass();
	row.find('td').css('background-color', '');
	row.attr('data-color', '');
	clearInterval(pulseRowInterval);
}

jQuery.fn.processResponse = function(r) {
	$(this).hideLoading();
	if (r.error) {
		$(this).addClass('error');
	} else {
		$(this).addClass('success');
	}
	$(this).find('.jDateLastRun').html(r.jDateLastRun);
	$(this).find('.jLastStatusText').html(r.result);
}

$(function() {
	$('tr.running').showLoading();
	$('.ccm-default-jobs-url').on('click', function() {
		$(this).get(0).select();
	});
	$('a.ccm-automate-job-instructions').on('click', $("#ccm-jobs-list"), function() {
		//if ($(this).attr('data-jSupportsQueue')) { }
        jQuery.fn.dialog.open({
            element: '#jd' + $(this).attr("data-jID"),
			height: 550,
			width: 650,
			modal: true,
			title: <?=$json->encode(t('Automation Instructions'))?>
		});
	});
	$('.btn-run-job').on('click', $('#ccm-jobs-list'), function() {
		var row = $(this).parent().parent();
		row.showLoading();
		var jSupportsQueue = $(this).attr('data-jSupportsQueue');
		var jID = $(this).attr('data-jID');
		var jName = $(this).attr('data-jName');
		var params = [
			{'name': 'auth', 'value': '<?=$auth?>'},
			{'name': 'jID', 'value': jID}
		];
		if (jSupportsQueue) {
			ccm_triggerProgressiveOperation(
				'<?php echo addslashes(URL::to('/ccm/system/jobs/run_single'))?>',
				params,
				jName, function(r) {
					$('.ui-dialog-content').dialog('close');
					row.processResponse(r);
				}, function(r) {
					row.processResponse(r);
				}
			);
		} else {
			$.ajax({
				url: '<?php echo addslashes(URL::to('/ccm/system/jobs/run_single'))?>',
				data: params,
				dataType: 'json',
				cache: false,
				success: function(json) {
					row.processResponse(json);
				}
			});
		}
	});

	$('.ccm-jobs-automation-schedule-type').click(function() {
		if($(this).val() == 1) {
			$(this).parent().parent().siblings('.ccm-jobs-automation-schedule-cron').hide();
			$(this).parent().parent().siblings('.ccm-jobs-automation-schedule-auto').show();
		} else {
			$(this).parent().parent().siblings('.ccm-jobs-automation-schedule-auto').hide();
			$(this).parent().parent().siblings('.ccm-jobs-automation-schedule-cron').show();
		}
	});

    $('.btn-uninstall-job').on('click', function (e) {
        return confirm('<?=t("Are you sure you want to uninstall this job?")?>');
    });
});
</script>
