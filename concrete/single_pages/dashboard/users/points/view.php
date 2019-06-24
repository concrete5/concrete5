<?php defined('C5_EXECUTE') or die('Access Denied.');

$dh = Core::make('helper/date'); /* @var $dh \Concrete\Core\Localization\Service\Date */

?>
<form class="form-inline" action="<?php echo $view->action('view')?>" method="get">
    <div class="ccm-dashboard-header-buttons">
	    <a href="<?=View::url('/dashboard/users/points/assign')?>" class="btn btn-primary"><?=t('Add Points')?></a>
	</div>

    <div class="ccm-pane-options">
        <div class="ccm-pane-options-permanent-search">
            <?=$form->label('uID', t('User'))?>
            <?php echo $form_user_selector->quickSelect('uID', $_GET['uID'], array('form-control'));?>
        </div>
    </div>

    <div class="clearfix" style="margin-top: 30px;">
        <input type="submit" value="<?=t('Search')?>" class="btn btn-primary pull-right" />
    </div>
</form>
<?php
if (count($entries) > 0) {
    ?>
<br>
<div class="table-responsive">
	<table id="ccm-product-list" class="ccm-search-results-table compact-results">
    	<thead>
    		<th class="<?=$upEntryList->getSearchResultsClass('uName')?>"><a href="<?=$upEntryList->getSortByURL('uName', 'asc')?>"><?=t('User')?></a></th>
    		<th class="<?=$upEntryList->getSearchResultsClass('upaName')?>"><a href="<?=$upEntryList->getSortByURL('upaName', 'asc')?>"><?=t('Action')?></a></th>
    		<th class="<?=$upEntryList->getSearchResultsClass('upPoints')?>"><a href="<?=$upEntryList->getSortByURL('upPoints', 'asc')?>"><?=t('Points')?></a></th>
    		<th class="<?=$upEntryList->getSearchResultsClass('timestamp')?>"><a href="<?=$upEntryList->getSortByURL('timestamp', 'asc')?>"><?=t('Date Assigned')?></a></th>
    		<th><span><?=t("Details")?></span></th>
    		<th></th>
    	</thead>

        <tbody>
        <?php
        foreach ($entries as $up) {
            ?>
        	<tr>
        		<?php
                    $ui = $up->getUserPointEntryUserObject();
            $action = $up->getUserPointEntryActionObject();
            ?>
        		<td><?php if (is_object($ui)) {
        ?><?php echo h($ui->getUserName())?><?php
    }
            ?></td>
        		<td><?php if (is_object($action)) {
        ?><?=h($action->getUserPointActionName())?><?php
    }
            ?></td>
        		<td><?php echo number_format($up->getUserPointEntryValue())?></td>
        		<td><?php echo $dh->formatDateTime($up->getUserPointEntryTimestamp());
            ?></td>
        		<td><?=h($up->getUserPointEntryDescription())?></td>
        		<td style="Text-align: right">
                    <?php
                    $delete = \Concrete\Core\Url\Url::createFromUrl($view->action('deleteEntry', $up->getUserPointEntryID()));

            $delete->setQuery(array(
                        'ccm_token' => \Core::make('helper/validation/token')->generate('delete_community_points'),
                    ));
            ?>
        		    <a href="<?=$delete?>" class="btn btn-sm btn-danger"><?=t('Delete')?></a>
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
	<div id="ccm-list-none"><?=t('No entries found.')?></div>
<?php
}
$upEntryList->displayPaging(); ?>
