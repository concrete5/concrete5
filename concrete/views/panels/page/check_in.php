<?php defined('C5_EXECUTE') or die("Access Denied.");
$v = $c->getVersionObject();
$require_version_comments = (bool) Config::get('concrete.misc.require_version_comments');
?>

<div class="ccm-panel-content-inner">

<form method="post" id="ccm-check-in" action="<?=$controller->action('submit')?>">

<h5><?=t('Version Comments')?></h5>

<div class="ccm-panel-check-in-comments"><textarea name="comments" class="form-control" id="ccm-check-in-comments"<?php echo $require_version_comments ? ' required="required"' : ''; ?>></textarea></div>

<?php if ($cp->canApprovePageVersions()) {

    $composer = Core::make('helper/concrete/composer');
    $publishTitle = $composer->getPublishButtonTitle($c);

    ?>
<div class="ccm-panel-check-in-publish">
    <?php $publishAction = (is_object($publishErrors) && $publishErrors->has()) ? false : true ?>
    <div class="btn-group d-flex" role="group">
        <button id="ccm-check-in-publish" type="submit" name="action" value="publish"
                class="pe-3 ps-3 btn btn-primary" <?=$publishAction ?: 'disabled' ?>>
            <?=$publishTitle?>
        </button>
        <button id="ccm-check-in-schedule" type="button" class="pe-3 ps-3 btn btn-primary" <?= $publishAction ?: 'disabled' ?>>
            <i class="far fa-clock"></i>
        </button>
    </div>
    <div id="ccm-check-in-schedule-wrapper" style="display:none;">
        <?php $composer->displayPublishScheduleSettings($c); ?>
    </div>
    <br/><br/>

        <?php if (count($publishErrors->getList())) { ?>
            <div class="small">
            <?php foreach ($publishErrors->getList() as $error): ?>
                <div class="text-warning"><strong><i class="fa fa-warning"></i> <?=$error?></strong></div>
                <br/>
            <?php endforeach; ?>
            </div>
        <?php } ?>

        <?php $pagetype = PageType::getByID($c->getPageTypeID()); ?>
        <?php if (count($publishErrors->getList()) && (is_object($pagetype))): ?>
            <div class="small">
                <div class="text-info">
                    <strong>
                        <i class="fa fa-question-circle"></i>
                        <?=t('You can specify page name, page location and attributes from the ' .
                             '<a href="#" data-launch-panel-detail="page-composer" data-panel-detail-url="%s" ' .
                             'data-panel-transition="fade">Page Compose interface</a>.',
                             URL::to('/ccm/system/panels/details/page/composer')); ?>
                    </strong>
                </div>
                <br/>
            </div>
        <?php endif ?>

</div>

<?php
} ?>
    <div class="d-grid">
        <button id="ccm-check-in-preview" type="submit" name="action" value="save" class="btn-success btn"><?=t('Save Changes')?></button>
    </div>

    <?php if ($c->isPageDraft() && $cp->canDeletePage()) {
    ?>
        <div class="d-grid mt-2">
            <button id="ccm-check-in-discard" type="submit" name="action" value="discard" class="btn-danger btn"><?=t('Discard Draft')?></button>
        </div>
	<?php
} elseif ($v->canDiscard()) {
    ?>
        <div class="d-grid mt-2">
            <button id="ccm-check-in-discard" type="submit" name="action" value="discard" class="btn-danger btn"><?=t('Discard Changes')?></button>
        </div>
	<?php
} ?>
	<input type="hidden" name="approve" value="PREVIEW" id="ccm-approve-field" />

</form>

<script type="text/javascript">
$(function() {
    setTimeout("$('#ccm-check-in-comments').focus();",300);
    $('#ccm-check-in').concreteAjaxForm();
    <?php if ($c->isPageDraft() && $cp->canDeletePage()) {
    ?>
    $('button#ccm-check-in-discard').on('click', function () {
        return confirm('<?=t('This will remove this draft and it cannot be undone. Are you sure?')?>');
    });
	<?php
} ?>

    var toggleScheduler = function () {
        $("#ccm-check-in-schedule-wrapper").toggle();
        $("#ccm-check-in-publish").prop('disabled', !$("#ccm-check-in-publish").prop('disabled'));
    },
        isScheduled = <?= json_encode($publishDate) ?>;

    if (isScheduled) {
        toggleScheduler();
    }

    $("#ccm-check-in-schedule, #ccm-check-in-schedule-wrapper .remove").click(function () {
        toggleScheduler();
    });
});
</script>

</div>
