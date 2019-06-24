<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div class="ccm-dashboard-content-full">

    <?php if ($list->getTotalResults()) {
        $result = $searchController->getSearchResultObject()->getJSONObject();
        $result = json_encode($result);
        ?>

        <div class="table-responsive">
            <?php View::element('express/entries/search');?>
        </div>

    <?php } else { ?>

        <div class="ccm-dashboard-content-full-inner">
            <p><?= t('None created yet.') ?></p>
        </div>

    <?php } ?>

</div>


<script type="text/javascript">
    $(function() {
        ConcreteEvent.subscribe('SelectExpressEntry', function(e, data) {
            var url = '<?= $view->action('view_entry', 'ENTRY_ID'); ?>';
            url = url.replace('ENTRY_ID', data.exEntryID);
            if (data.event.metaKey) {
                window.open(url);
            } else {
                window.location.href = url;
            }
        });

        $('#ccm-dashboard-content').concreteAjaxSearch({
            result: <?= (!empty($result)) ? $result : 'null'; ?>,
            onUpdateResults: function(concreteSearch) {
                concreteSearch.$element.on('mouseover', 'tr[data-entity-id]', function(e) {
                    e.stopPropagation();
                    $(this).addClass('ccm-search-select-hover');
                });
                concreteSearch.$element.on('mouseout', 'tr[data-entity-id]', function(e) {
                    e.stopPropagation();
                    $(this).removeClass('ccm-search-select-hover');
                });

                concreteSearch.$element.unbind('click.expressEntries');
                concreteSearch.$element.on('click.expressEntries', 'tr[data-entity-id]', function(e) {
                    e.stopPropagation();
                    ConcreteEvent.publish('SelectExpressEntry', {
                        exEntryID: $(this).attr('data-entity-id'),
                        event: e
                    });
                    return false;
                });
            }
        });
    });
</script>