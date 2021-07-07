<?php defined('C5_EXECUTE') or die("Access Denied."); ?>

<?php
$list = $category->getPendingWorkflowProgressList();
$items = $list->get();

if (count($items) > 0): ?>

    <div id="ccm-workflow-waiting-for-me-wrapper">
        <?php View::element('workflow/progress/categories/user/table_data', array('items' => $items, 'list' => $list)) ?>
    </div>

    <script type="text/javascript">
        $(function() {
            var init = function() {
                $('.dialog-launch').dialog();
                $('.ccm-workflow-progress-actions form').ajaxForm({
                    dataType: 'json',
                    beforeSubmit: function() {
                        jQuery.fn.dialog.showLoader();
                    },
                    success: function(r) {
                        var wpID = r.wpID;
                        var statusBar = $('#ccm-dashboard-result-message');
                        var alertInnerContent = "<button type='button' class='btn-close' data-bs-dismiss='alert'></button>" + r.message;

                        if (statusBar.length == 0) {
                            $('.ccm-dashboard-page-header').after("<div class='ccm-ui' id='ccm-dashboard-result-message' style='display:block'>"
                                + "<div class='row'><div class='span12'><div class='alert alert-info alert-dismissible'>"
                                + alertInnerContent + "</div></div></div></div>");
                        } else {
                            $('.alert', statusBar).html(alertInnerContent);
                        }

                        $('.ccm-workflow-waiting-for-me-row' + wpID).fadeOut(300, function() {
                            jQuery.fn.dialog.hideLoader();
                            $("#ccm-workflow-waiting-for-me-wrapper").html(r.tableData);
                            init();
                        });
                    }
                });
            }

            init();
        });
    </script>

<?php  else: ?>
    <p><?php echo t('None.')?></p>
<?php endif; ?>
