<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
$cp = new Permissions($calendar);
if (!isset($occurrence) || !$occurrence) {
    $occurrence = null;
}
?>
<div class="ccm-ui">
    <form method="post" class="ccm-event-add form-stacked">

        <?php
        if ($occurrence !== null) {
            ?>
            <input type="hidden" name="versionOccurrenceID" value="<?= $occurrence->getID() ?>"/>
            <?php

        } else {
            ?>

            <input type="hidden" name="caID" value="<?= $calendar->getID() ?>">

            <?php
        }
        Element::get('event/form', array('calendar' => $calendar, 'occurrence' => $occurrence))->render();
        ?>
    </form>
</div>
<div class="dialog-buttons">
    <button class="btn btn-secondary float-end" data-dialog-action="cancel"><?= t('Cancel') ?></button>

    <?php if ($occurrence !== null || $cp->canApproveCalendarEvent()) {
        $showButtonGroup = true;
    } else {
        $showButtonGroup = false;
    } ?>

            <?php if ($cp->canApproveCalendarEvent()) { ?>
                <button type="submit" data-event-dialog-action="save"
                        class="btn btn-primary float-end"><?= t('Save &amp; Close') ?></button>
            <button type="submit" class="btn btn-success float-end"
                    data-event-dialog-action="publish"><?= t('Publish Event') ?></button>

        <?php } else { ?>
            <button type="submit" style="margin-right: 0px" data-event-dialog-action="save"
                    class="float-end btn btn-primary"><?= t('Save &amp; Close') ?></button>
        <?php } ?>

    <script type="text/javascript">
        $(function () {
            var $eventForm = $('form.ccm-event-add');

            $('button[data-event-dialog-action=save]').on('click', function () {
                var formData = $eventForm.serializeArray();
                formData.push({'name': 'publishAction', 'value': 'save'});
                $.concreteAjax({
                    data: formData,
                    url: '<?=$controller->action('save');?>',
                    success: function (r) {
                        window.location.href = r.redirectURL;
                    }
                });
            });

            $('button[data-event-dialog-action=publish]').on('click', function () {
                var formData = $eventForm.serializeArray();
                formData.push({'name': 'publishAction', 'value': 'approve'});
                $.concreteAjax({
                    data: formData,
                    url: '<?=$controller->action('save');?>',
                    success: function (r) {
                        window.location.href = r.redirectURL;
                    }
                });
            });


        });
    </script>
</div>
