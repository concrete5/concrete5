<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div class="ccm-express-entry-search-dialog">
  <?php $headerMenu->render(); ?>

  <div data-search="express_entries" class="ccm-ui">
      <?php View::element('express/entries/search', array('controller' => $searchController, 'selectMode' => true)) ?>
  </div>
</div>

<script type="text/javascript">
    $(function () {
        $('.ccm-express-entry-search-dialog').concreteAjaxSearch({
            result: <?=$result?>,
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
