<?php

use Concrete\Core\Permission\IpAccessControlService;

defined('C5_EXECUTE') or die('Access Denied.');

/* @var Concrete\Core\Page\View\PageView $view */
/* @var Concrete\Core\Validation\CSRF\Token $token */
/* @var Concrete\Core\Form\Service\Form $form */
/* @var Concrete\Controller\SinglePage\Dashboard\System\Permissions\Blacklist\Range $controller */

/* @var int $type */
/* @var Concrete\Core\Permission\IpAccessControlService $service */
/* @var IPLib\Address\AddressInterface $myIPAddress */

$view->element('dashboard/system/permissions/blacklist/menu', ['category' => $service->getCategory(), 'type' => $type]);

if (($type & IpAccessControlService::IPRANGEFLAG_MANUAL) === IpAccessControlService::IPRANGEFLAG_MANUAL) {
    ?>
    <form class="row row-cols-auto g-0 align-items-center" id="ccm-form-new-range">
        <div class="col-auto">
            <fieldset>
                <legend><?= t('Add IP Range') ?></legend>
                <div class="form-group">
                  <div class="input-group mb-3">
                      <label for="new-range" class="input-group-text launch-tooltip col-form-label" data-html="true" title="<?= h(t(
                        'Enter a single address<br />(example: %s) or a range<br />(example: %s or %s).<br />Accept both IPv4 and IPv6 ranges.',
                        '<code>1.2.3.4</code>',
                        '<code>1.2.3.*</code>',
                        '<code>1.2.3.0/8</code>'
                    )) ?>"><?= t('IP Range') ?></label>
                    <input type="text" class="form-control" id="ccm-new-range" required="required" aria-describedby="button-addon2" />
                    <button type="submit" id="button-addon2" class="btn btn-outline-secondary"><?= t('Add') ?></button>
                  </div>
                </div>
                <br />
                <?php
                if (($type & IpAccessControlService::IPRANGEFLAG_WHITELIST) === IpAccessControlService::IPRANGEFLAG_WHITELIST) {
                    ?>
                    <p class="text-muted"><?= t('Your IP address:') ?> <a href="#" onclick="$('#ccm-new-range').val($(this).text());return false"><?= h((string) $myIPAddress) ?></a></p>
                    <?php
                }
                ?>
            </fieldset>
        </div>
    </form>
    <script>
    $(document).ready(function() {
        function submit(range, force) {
            var send = {
                ccm_token:<?= json_encode($token->generate('add_range/' . $type . '/' . $service->getCategory()->getIpAccessControlCategoryID())) ?>,
                range: range
            };
            if (force) {
                send.force = '1';
            }
            new ConcreteAjaxRequest({
                url: <?= json_encode($view->action('add_range', $type, $service->getCategory()->getIpAccessControlCategoryID())) ?>,
                data: send,
                success: function(data) {
                    if (data.require_force) {
                        if (window.confirm(data.require_force)) {
                            submit(range, true);
                        }
                        return;
                    }
                    $range = $('#ccm-new-range').val('');
                    $('#ccm-ranges-table>tbody').append(data.row);
                }
            });
               
        }
        $('#ccm-form-new-range').on('submit', function(e) {
            e.preventDefault();
            var $range = $('#ccm-new-range'), range = $.trim($range.val());
            if (range === '') {
                $range.focus();
                return;
            }
            submit(range);
        });
    });
    </script>
    <?php
}
?>
<table class="table table-hover" id="ccm-ranges-table">
    <colgroup>
        <col width="45" />
    </colgroup>
    <thead>
        <tr>
            <th></th>
            <th><?= t('Range') ?></th>
            <?php
            if ($type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
                ?>
                <th><?= t('Expires') ?></th>
                <th></th>
                <?php
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($service->getRanges($type) as $range) {
            echo $controller->formatRangeRow($range);
        }
        ?>
    </tbody>
</table>

<?php
if ($type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
    ?>
    <div style="display: none" data-dialog="ccm-blacklist-clear-data-dialog" class="ccm-ui">
        <form data-dialog-form="ccm-blacklist-clear-data-form" method="POST" action="<?= $view->action('clear_data', $service->getCategory()->getIpAccessControlCategoryID()) ?>">
            <?php $token->output('blacklist-clear-data/' . $service->getCategory()->getIpAccessControlCategoryID()) ?>
            <div class="form-check-inline">

                    <?= $form->checkbox('delete-failed-login-attempts', 'yes', false, ['class'=>'mb-sm-2']) ?>
                    <?= $form->label('delete-failed-login-attempts',t('Delete failed login attempts older than %s days', $form->number('delete-failed-login-attempts-min-age', 1, ['style' => 'width: 90px; display: inline-block', 'min' => '0']))) ?>

            </div>
            <div class="form-check">
                <?= $form->radio('delete-automatic-blacklist', 'yes-keep-current', true) ?>
                <?= $form->label('delete-automatic-blacklist1', t('Delete expired automatic bans')) ?>
            </div>
            <div class="form-check">
                <?= $form->radio('delete-automatic-blacklist', 'yes-all', false) ?>
                <?= $form->label('delete-automatic-blacklist2', t('Delete every automatic ban (including the current ones)')) ?>
            </div>
            <div class="form-check">
                <?= $form->radio('delete-automatic-blacklist', 'nope', false) ?>
                <?= $form->label('delete-automatic-blacklist3', t("Don't delete any automatic ban")) ?>
            </div>
        </form>
        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel"><?= t('Cancel') ?></button>
            <button class="btn btn-danger float-end" data-dialog-action="submit"><?= t('Delete') ?></button>
        </div>
    </div>
    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <a class="btn btn-danger float-end" data-launch-dialog="ccm-blacklist-clear-data-dialog"><?= t('Delete') ?></a>
        </div>
    </div>
    <?php
}
?>
<script>
$(document).ready(function() {
    $('#ccm-ranges-table>tbody')
        .on('click', 'a.ccm-iprange-delete', function(e) {
            e.preventDefault();
            var $tr = $(this).closest('tr'), id = $tr.data('range-id');
            new ConcreteAjaxRequest({
                url: <?= json_encode($view->action('delete_range', $type, $service->getCategory()->getIpAccessControlCategoryID())) ?>,
                data: {
                    ccm_token:<?= json_encode($token->generate('delete_range/' . $type . '/' . $service->getCategory()->getIpAccessControlCategoryID())) ?>,
                    id: id
                },
                success: function(data) {
                    $tr.hide('fast', function() {
                        $tr.remove();
                    });
                    if (typeof data === 'string') {
                        window.alert(data);
                    }
                }
            });
        })
        .on('click', 'a.ccm-iprange-makepermanent', function(e) {
            e.preventDefault();
            var $tr = $(this).closest('tr'), id = $tr.data('range-id');
            new ConcreteAjaxRequest({
                url: <?= json_encode($view->action('make_range_permanent', $type, $service->getCategory()->getIpAccessControlCategoryID())) ?>,
                data: {
                    ccm_token:<?= json_encode($token->generate('make_range_permanent/' . $type . '/' . $service->getCategory()->getIpAccessControlCategoryID())) ?>,
                    id: id
                },
                success: function(data) {
                    $tr.hide('fast', function() {
                        $tr.remove();
                    });
                }
            });

        })
    ;
    <?php
    if ($type === IpAccessControlService::IPRANGETYPE_BLACKLIST_AUTOMATIC) {
        ?>
        var $dialog = $('div[data-dialog="ccm-blacklist-clear-data-dialog"]');
        $('[data-launch-dialog="ccm-blacklist-clear-data-dialog"]').on('click', function(e) {
            e.preventDefault();
            jQuery.fn.dialog.open({
                element: $dialog,
                modal: true,
                width: 480,
                title: <?= json_encode(t('Removal confirmation')) ?>,
                height: 'auto'
            });
        });
        ConcreteEvent.subscribe('AjaxFormSubmitSuccess', function(e, data) {
            if (data.form === 'ccm-blacklist-clear-data-form') {
                window.location.reload();
            }
        });
        <?php
    }
    ?>
});
</script>
