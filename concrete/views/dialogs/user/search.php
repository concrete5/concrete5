<?php
defined('C5_EXECUTE') or die('Access Denied.');
?>

<div data-search="users" class="ccm-ui">

    <?php
    $header->render();
    ?>

    <?php Loader::element('users/search', array('result' => $result))?>

</div>

<script type="text/javascript">
    $(function() {
        $('div[data-search=users]').concreteAjaxSearch({
            result: <?=json_encode($result->getJSONObject())?>,
            onUpdateResults: function (concreteSearch) {
                concreteSearch.$element.find('select[data-bulk-action=users] option:eq(0)').after('<option value="select_users"><?=t('Choose Users')?></option>');
                concreteSearch.$element.on('click', 'a[data-user-id]', function () {
                    ConcreteEvent.publish('UserSearchDialogSelectUser', {
                        uID: $(this).attr('data-user-id'),
                        uEmail: $(this).attr('data-user-email'),
                        uName: $(this).attr('data-user-name')
                    });
                    ConcreteEvent.publish('UserSearchDialogAfterSelectUser');
                    return false;
                });

                concreteSearch.subscribe('SearchBulkActionSelect', function (e, data) {
                    if (data.value == 'select_users') {
                        $.each(data.items, function (i, item) {
                            var $item = $(item);
                            ConcreteEvent.publish('UserSearchDialogSelectUser', {
                                uID: $item.attr('data-user-id'),
                                uEmail: $item.attr('data-user-email'),
                                uName: $item.attr('data-user-name')
                            });
                        });
                        ConcreteEvent.publish('UserSearchDialogAfterSelectUser');
                    }
                });
            }
        });
    });
</script>