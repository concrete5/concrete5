<?php
defined('C5_EXECUTE') or die("Access Denied.");
$cmpp = new Permissions($pagetype);
$cp = new Permissions($page);
$v = $page->getVersionObject();
$publishDate = $v->getPublishDate();
?>

<?php if ($cp->canApprovePageVersions()) {

    $composer = Core::make('helper/concrete/composer');
    $publishTitle = $composer->getPublishButtonTitle($page);

    ?>

    <div class="float-end btn-group" data-page-type-composer-form-btns="publish">
        <button type="button" data-page-type-composer-form-btn="publish" class="ps-3 pe-3 btn btn-primary"><?=$publishTitle?></button>
        <button data-page-type-composer-form-btn="schedule" type="button" class="ps-3 pe-3 btn btn-primary <?php if ($publishDate) { ?>active<?php } ?>">
            <i class="far fa-clock"></i>
        </button>
    </div>

    <div style="display: none">
        <div data-dialog="schedule-page">

            <?php $composer->displayPublishScheduleSettings($page); ?>

        </div>
    </div>

    <?php

} ?>

<?php if (!is_object($page) || $page->isPageDraft()) {
    ?>
    <button type="button" data-page-type-composer-form-btn="preview" class="btn btn-success float-end"><?=t('Edit Mode')?></button>
<?php
} else {
    ?>
    <button type="button" data-page-type-composer-form-btn="preview" class="btn btn-success float-end"><?=t('Save')?></button>
<?php
} ?>

<?php if (is_object($page) && $page->isPageDraft()) {
    if ($cp->canDeletePage()) {
        ?>
        <button type="button" data-page-type-composer-form-btn="discard" class="btn btn-danger float-start"><?=t('Discard Draft')?></button>
    <?php
    }
    ?>
    <button type="button" data-page-type-composer-form-btn="exit" class="btn btn-secondary float-start"><?=t('Save and Exit')?></button>
<?php
} ?>


<style type="text/css">
    div.ccm-ui button[data-page-type-composer-form-btn=save] {
        margin-left: 10px;
    }
    div.ccm-ui button[data-page-type-composer-form-btn=permissions] {
        margin-left: 10px;
    }
    div.ccm-ui button[data-page-type-composer-form-btn=exit] {
        margin-left: 10px;
    }
    div.ccm-ui button[data-page-type-composer-form-btn=preview] {
        margin-left: 10px;
    }
    div.ccm-ui div[data-page-type-composer-form-btns=publish] {
        margin-left: 10px;
    }
</style>

<script type="text/javascript">
    $(function() {
        $('button[data-page-type-composer-form-btn=schedule]').on('click', function() {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=schedule-page]',
                modal: true,
                width: 'auto',
                title: '<?=t('Schedule Publishing')?>',
                height: 'auto',
                onOpen: function() {
                    $('.ccm-check-in-schedule').on('click', function() {
                        var data = $('form[data-panel-detail-form=compose]').serializeArray();
                        var data = data.concat($('div[data-dialog=schedule-page] :input').serializeArray());
                        data.push({'name': 'action', 'value': 'schedule'});
                        ConcreteEvent.fire('PanelComposerPublish', {data: data});
                    });
                }
            });
        });
    });
</script>
