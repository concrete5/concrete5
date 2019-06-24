<?php defined('C5_EXECUTE') or die('Access Denied.');

    //$interface = Loader::helper('interface');
?>

<?php if ($showForm) {
    ?>
<form method="post" action="<?=$view->action('save')?>" id="ccm-community-points-action">
    <?= \Core::make('helper/validation/token')->output('add_action');
    ?>
    <div class="row">
        <div class="col-md-12">

            <?php
                echo $form->hidden('upaID', $upaID);
    ?>

        	<div class="checkbox">
                <label>
                    <?=$form->checkbox('upaIsActive', 1, ($upaIsActive == 1 || (!$upaID)))?> <?=t('Enabled')?>
                </label>
            </div>

        	<div class="form-group">
        	    <?=$form->label('upaHandle', t('Action Handle'));
    ?>
        		<div class="input">
            		<?php
                        $args = array();
    if ($upaHasCustomClass) {
        $args['disabled'] = 'disabled';
    }
    ?>
                    <?php echo $form->text('upaHandle', $upaHandle, $args);
    ?>
        		</div>
        	</div>

        	<div class="form-group">
        	    <?=$form->label('upaName', t('Action Name'));
    ?>
        		<div class="input">
        		    <?php echo $form->text('upaName', $upaName);
    ?>
        		</div>
        	</div>

        	<div class="form-group">
                <?=$form->label('upaDefaultPoints', t('Default Points'));
    ?>
        		<div class="input">
        		    <?php echo $form->number('upaDefaultPoints', $upaDefaultPoints);
    ?>
        		</div>
        	</div>

        	<div class="form-group">
        	    <?=$form->label('gBadgeID', t('Badge Associated'));
    ?>
        		<div class="input">
        			<?=$form->select('gBadgeID', $badges, $gBadgeID)?>
        			<i class="icon-question-sign launch-tooltip" title="<?=t('If a badge is assigned to this action, the first time this user performs this action they will be granted the badge.')?>"></i>
        		</div>
        	</div>

            <?php
            $label = t('Add Action');
    if ($upaID > 0) {
        $label = t('Update Action');
    }
    ?>

            <div class="ccm-dashboard-form-actions-wrapper">
                <div class="ccm-dashboard-form-actions">
                    <a href="<?=$view->url('/dashboard/users/points/actions')?>" class="btn btn-default pull-left"><?=t('Back to List')?></a>
                    <button class="btn btn-primary pull-right" type="submit"><?=$label?></button>
                </div>
            </div>
        </div>
    </div>
</form>
<?php
} else {
    ?>
	<div class="ccm-dashboard-header-buttons">
	    <a href="<?=$view->action('add')?>" class="btn btn-primary"><?=t('Add Action')?></a>
	</div>

	<?php
        if (!$mode) {
            $mode = $_REQUEST['mode'];
        }
    $txt = Loader::helper('text');
    $keywords = $_REQUEST['keywords'];

    if (count($actions) > 0) {
        ?>
        <div class="table-responsive">
			<table class="ccm-search-results-table compact-results">
    			<thead>
    				<th><span><?=t("Active")?></span></th>
    				<th class="<?=$actionList->getSearchResultsClass('upaName')?>"><a href="<?=$actionList->getSortByURL('upaName', 'asc')?>"><?=t('Action Name')?></a></th>
    				<th class="<?=$actionList->getSearchResultsClass('upaHandle')?>"><a href="<?=$actionList->getSortByURL('upaHandle', 'asc')?>"><?=t('Action Handle')?></a></th>
    				<th class="<?=$actionList->getSearchResultsClass('upaDefaultPoints')?>"><a href="<?=$actionList->getSortByURL('upaDefaultPoints', 'asc')?>"><?=t('Default Points')?></a></th>
    				<th class="<?=$actionList->getSearchResultsClass('upaBadgeGroupID')?>"><a href="<?=$actionList->getSortByURL('upaBadgeGroupID', 'asc')?>"><?=t('Group')?></a></th>
    				<th></th>
    			</thead>

                <tbody>
            		<?php
                    foreach ($actions as $upa) {
                        ?>
            		<tr class="">
            			<td style="text-align: center"><?php if ($upa['upaIsActive']) {
        ?><i class="fa fa-check"></i><?php
    }
                        ?></td>
            			<td><?=h($upa['upaName'])?></td>
            			<td><?=h($upa['upaHandle'])?></td>
            			<td><?=number_format($upa['upaDefaultPoints'])?></td>
            			<td><?php echo h($upa['gName']);
                        ?></td>
            			<td style="text-align: right">
                            <?php
                            $delete_url = \League\Url\Url::createFromUrl($view->action('delete', $upa['upaID']));
                        $delete_url = $delete_url->setQuery(array(
                                'ccm_token' => \Core::make('helper/validation/token')->generate('delete_action'),
                            ));
                        ?>
            			    <a href="<?=$view->action($upa['upaID'])?>" class="btn btn-sm btn-default"><?=t('Edit')?></a>
            			    <a href="<?=$delete_url?>" class="btn btn-sm btn-danger"><?=t('Delete')?></a>
            			</td>
            		</tr>
            		<?php
                    }
            ?>
                </tbody>
    		</table>
        </div>
		<?php
    } else {
        ?>
			<p><?=t('No Actions found.')?></p>
		<?php
    }
    ?>
	
<?=$actionList->displayPagingV2();?>

<?php
} ?>

<?=Loader::helper('concrete/dashboard')->getDashboardPaneFooterWrapper(false)?>
