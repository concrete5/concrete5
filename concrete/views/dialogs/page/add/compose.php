<?php
defined('C5_EXECUTE') or die("Access Denied.");
$composer = Core::make("helper/concrete/composer");
?>

<div class="ccm-ui">
    <form data-dialog-form="add-page-compose" action="<?=$controller->action('submit')?>">
        <?php $pagetype->renderComposerOutputForm(null, $parent); ?>
        <input type="hidden" name="addPageComposeAction" value="preview" />
        <div class="dialog-buttons">
            <button type="button" data-dialog-action="cancel" class="btn btn-secondary float-start"><?=t('Cancel')?></button>
            <div class="btn-group float-end">
                <button type="button" data-dialog-action="submit" value="preview" data-page-type-composer-form-btn="preview" class="btn btn-success"><?=t('Edit Mode')?></button>
                <button type="button" data-composer-dialog-action="publish" value="publish" class="pe-3 ps-3 btn btn-primary"><?=t('Publish Page')?></button>
                <button data-page-type-composer-form-btn="schedule" type="button" class="pe-3 ps-3 btn btn-primary">
                    <i class="far fa-clock"></i>
                </button>
            </div>
        </div>
    </form>
</div>

<div style="display: none">
    <div data-dialog="schedule-page">

        <?php $composer->displayPublishScheduleSettings(); ?>

    </div>
</div>

<script type="text/javascript">
    $(function() {
        $('form[data-dialog-form=add-page-compose]').concreteAjaxForm();
        $('button[data-composer-dialog-action=publish]').on('click', function() {
            $('form[data-dialog-form=add-page-compose] input[name=addPageComposeAction]').val('publish');
            $('form[data-dialog-form=add-page-compose]').submit();
        });
        ConcreteEvent.unsubscribe('AjaxFormSubmitSuccess.addPageCompose');
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess.addPageCompose', function(e, data) {
            if (data.response.cParentID) {
                ConcreteEvent.publish('SitemapAddPageRequestComplete', {'cParentID': data.response.cParentID});
            }
        });

        $('[data-dialog-form=add-page-compose]').parents('.ui-dialog').find('.ui-dialog-titlebar-close').on('click', function() {
            $('div[data-dialog=schedule-page]').parent().remove();
        });

        $('.dialog-buttons button[data-dialog-action=cancel]').on('click', function() {
            $('div[data-dialog=schedule-page]').parent().remove();
        });

        $('button[data-page-type-composer-form-btn=schedule]').on('click', function() {
            jQuery.fn.dialog.open({
                element: 'div[data-dialog=schedule-page]',
                modal: true,
                width: 'auto',
                title: '<?=t('Schedule Publishing')?>',
                height: 'auto',
                onOpen: function() {
                    $('.ccm-check-in-schedule').on('click', function() {
                        var data = $('form[data-dialog-form=add-page-compose]').serializeArray();

                        var data = data.concat($('div[data-dialog=schedule-page] :input').serializeArray());
                        data.push({'name': 'addPageComposeAction', 'value': 'schedule'});
                        $.concreteAjax({
                            data: data,
                            url: '<?=$controller->action('submit')?>',
                            success: function(r) {
                                $('div[data-dialog=schedule-page]').parent().remove();
                                ConcreteEvent.fire('AjaxFormSubmitSuccess', {
                                    response: r
                                });
                            }
                        });
                    });
                }
            });
        });

    });
</script>
