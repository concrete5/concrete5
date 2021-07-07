<?php /** @noinspection PhpComposerExtensionStubsInspection */

defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Support\Facade\Url;

/** @var Concrete\Controller\Panel\Page\Versions $controller */
/** @var Concrete\Core\View\DialogView $view */
/** @var Concrete\Core\Page\Collection\Version\EditResponse $response */
/** @var Concrete\Core\Page\Page $c */
/** @var bool|null $isDialogMode  */
?>

<script type="text/template" class="tbody">
    <% _.each(versions, function(cv) { %>
        <%=templateRow(cv) %>
    <% }); %>
</script>

<script type="text/template" class="version">
    <tr <% if (cvIsApproved) { %> class="ccm-panel-page-version-approved" <% } else if (cvIsScheduled == 1) { %> class="ccm-panel-page-version-scheduled" <% } %>>
        <td>
            <!--suppress HtmlFormInputWithoutLabel -->
            <input class="ccm-flat-checkbox" type="checkbox" name="cvID[]" value="<%-cvID%>"
                   data-version-active="<%- cvIsApproved ? true : false %>"/>
        </td>

        <td>
            <span class="ccm-panel-page-versions-version-id">
                <%-cvID%>
            </span>
        </td>

        <td class="ccm-panel-page-versions-details">
            <div class="ccm-panel-page-versions-actions">
                <a href="#" class="ccm-hover-icon ccm-panel-page-versions-menu-launcher"
                   data-launch-versions-menu="ccm-panel-page-versions-version-menu-<%-cvID%>">
                    <svg>
                        <use xlink:href="#icon-menu-launcher"/>
                    </svg>
                </a>

                <a href="#" class="ccm-hover-icon ccm-panel-page-versions-version-info" data-toggle="version-info">
                    <svg>
                        <use xlink:href="#icon-info"/>
                    </svg>
                </a>
            </div>

            <div class="ccm-panel-page-versions-status">
                <% if (cvIsApproved) { %>
                    <p>
                        <span class="badge bg-dark">
                            <?php echo t('Live') ?>
                        </span>
                    </p>
                <% } %>
            </div>

            <p>
                <span class="ccm-panel-page-versions-version-timestamp">
                    <?php echo t('Created on'); ?>
                    <%-cvDateVersionCreated%>
                </span>
            </p>

            <% if (cvComments) { %>
                <p class="ccm-panel-page-versions-description">
                    <%-cvComments%>
                </p>
            <% } %>

            <div class="ccm-panel-page-versions-more-info">
                <p>
                    <?php echo t('Edit by') ?>

                    <%-cvAuthorUserName%>
                </p>

                <% if (cvIsApproved) { %>
                    <% if (cvApprovedDate && cvApproverUserName) { %>
                        <p>
                            <?php echo t('Approved on'); ?>

                            <%-cvApprovedDate%> <?php echo t('by'); ?> <%-cvApproverUserName%>
                        </p>
                    <% } else if (cvApprovedDate) { %>
                        <p>
                            <?php echo t('Approved on'); ?>

                            <%-cvApprovedDate%>
                        </p>
                    <% } else if (cvApproverUserName) { %>
                        <p>
                            <?php echo t('Approved by'); ?>

                            <%-cvApproverUserName%>
                        </p>
                    <% } %>
                <% } %>

                <% if (cvIsScheduled) { %>
                    <p>
                        <?php echo t('Scheduled by') ?>
                        <%-cvApproverUserName%> <?php echo tc(/*i18n: In the sentence Scheduled by USERNAME for DATE/TIME*/
                            'ScheduledByFor', ' for ') ?> <%-cvPublishDate%>
                    </p>
                <% } %>
            </div>

            <div class="popover fade" data-menu="ccm-panel-page-versions-version-menu-<%-cvID%>">
                <div class="dropdown-menu">
                    <% if (cvIsApproved) { %>
                        <span class="dropdown-item">
                            <?php echo t('Approve') ?>
                        </span>
                    <% } else { %>
                        <a href="#" data-version-menu-task="approve" data-version-id="<%-cvID%>" class="dropdown-item">
                            <?php echo t('Approve') ?>
                        </a>
                    <%  } %>

                    <a href="#" data-version-menu-task="duplicate"  data-version-id="<%-cvID%>" class="dropdown-item">
                        <?php echo t('Duplicate') ?>
                    </a>

                    <div class="dropdown-divider"></div>

                    <% if ( ! cIsStack) { %>
                        <a href="#" data-version-menu-task="new-page" data-version-id="<%-cvID%>" class="dropdown-item">
                            <?php echo t('New Page') ?>
                        </a>
                    <% } %>

                    <% if (!cvIsApproved) { %>
                        <span class="dropdown-item">
                            <?php echo t('Unapprove') ?>
                        </span>
                    <% } else { %>
                        <a href="#" data-version-menu-task="unapprove" data-version-id="<%-cvID%>" class="dropdown-item">
                            <?php echo t('Unapprove') ?>
                        </a>
                    <% } %>

                    <% if (cpCanDeletePageVersions) { %>
                        <span <% if (!cvIsApproved) { %>style="display:none"<% } %> class="dropdown-item">
                            <?php echo t('Delete') ?>
                        </span>

                        <a <% if (cvIsApproved) { %>style="display:none"<% } %> href="#" data-version-menu-task="delete" data-version-id="<%-cvID%>" class="dropdown-item">
                            <?php echo t('Delete') ?>
                        </a>
                    <% } %>
                </div>
            </div>
        </td>
    </tr>
</script>

<script type="text/template" class="footer">
    <% if (hasPreviousPage == '1' || hasNextPage == '1') { %>
        <tr>
            <td colspan="3">
                <% if (hasPreviousPage == '1') { %>
                    <a href="#" class="float-start" data-version-navigation="<%=previousPageNum%>">
                        <?php echo t('&larr; Newer Versions') ?>
                    </a>
                <% } %>

                <% if (hasNextPage == '1') { %>
                    <a href="#" class="float-end" data-version-navigation="<%=nextPageNum%>">
                        <?php echo t('Older Versions &rarr;') ?>
                    </a>
                <% } %>
            </td>
        </tr>
    <% } %>
</script>

<!--suppress ES6ConvertVarToLetConst, SpellCheckingInspection, EqualityComparisonWithCoercionJS, JSUnusedAssignment, JSUnresolvedVariable -->
<script type="text/javascript">
    var ConcretePageVersionList = {
        sendRequest: function (url, data, onComplete) {
            var _data = [];

            $.each(data, function (i, dataItem) {
                _data.push({'name': dataItem.name, 'value': dataItem.value});
            });

            jQuery.fn.dialog.showLoader();

            $.ajax({
                type: 'post',
                dataType: 'json',
                data: _data,
                url: url,
                error: function (r) {
                    ConcreteAlert.dialog('Error', '<div class="alert alert-danger">' + r.responseText + '</div>');
                },
                success: function (r) {
                    if (r.error) {
                        ConcreteAlert.dialog('Error', '<div class="alert alert-danger">' + r.errors.join("<br>") + '</div>');
                    } else {
                        if (onComplete) {
                            onComplete(r);
                        }
                    }
                },
                complete: function () {
                    jQuery.fn.dialog.hideLoader();
                }
            });
        },

        handleVersionRemovalResponse: function (r) {
            $('button[data-version-action]').prop('disabled', true);

            for (var i = 0; i < r.versions.length; i++) {
                var $row = $('input[type=checkbox][value=' + r.versions[i].cvID + ']').parent().parent();

                $row.queue(function () {
                    $(this).addClass('bounceOutLeft animated');
                    $(this).dequeue();
                }).delay(600).queue(function () {
                    $(this).remove();
                    $(this).dequeue();

                    var menuItems = $('li.ccm-menu-item-delete');

                    if (menuItems.length == 1) {
                        menuItems.children('span').show();
                        menuItems.children('a').hide();
                    } else {
                        menuItems.children('a').show();
                        menuItems.children('span').hide();
                    }
                });
            }
        },

        previewSelectedVersions: function (checkboxes) {
            var panel = ConcretePanelManager.getByIdentifier('page');

            if (!panel) {
                return;
            }

            if (checkboxes.length > 0) {
                var src = <?php echo json_encode((string)Url::to("/ccm/system/panels/details/page/versions")) ?>;
                var data = '';

                $.each(checkboxes, function (i, cb) {
                    data += '&cvID[]=' + $(cb).val();
                });

                panel.openPanelDetail({'identifier': 'page-versions', 'data': data, 'url': src, target: null});
            } else {
                panel.closePanelDetail();
            }
        },

        handleVersionUpdateResponse: function (r) {
            for (var i = 0; i < r.versions.length; i++) {
                var $row = $('input[type=checkbox][value=' + r.versions[i].cvID + ']').parent().parent();

                if ($row.length) {
                    $row.replaceWith(templateRow(r.versions[i]));
                } else {
                    $('#ccm-panel-page-versions table tbody').prepend(templateRow(r.versions[i]));
                }

                this.setupMenus();
            }
        },

        setupMenus: function () {
            // the click proxy is kinda screwy on this
            $('[data-launch-versions-menu]').each(function () {
                $(this).concreteMenu({
                    enableClickProxy: false,
                    menu: 'div[data-menu=' + $(this).attr('data-launch-versions-menu') + ']'
                });
            });

            $('a[data-toggle=version-info]').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();

                var $parent = $(this).parentsUntil('.ccm-panel-page-versions-details').parent();

                $parent.find('.ccm-panel-page-versions-more-info').css('height', 'auto');
            });


            $('a[data-version-menu-task]').unbind('.vmenu').on('click.vmenu', function () {
                var cvID = $(this).attr('data-version-id');

                switch ($(this).attr('data-version-menu-task')) {
                    case 'delete':

                        ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("delete")) ?>, [{
                            'name': 'cvID[]',
                            'value': cvID
                        }], function (r) {
                            ConcreteAlert.notify({
                                'message': r.message
                            });
                            ConcretePageVersionList.handleVersionRemovalResponse(r);
                            ConcreteEvent.publish('PageVersionChanged.deleted', {
                                cID: <?php echo (int)$c->getCollectionID() ?>,
                                cvID: cvID
                            });
                        });

                        break;

                    case 'approve':
                        ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("approve")) ?>, [{
                            'name': 'cvID',
                            'value': cvID
                        }], function (r) {
                            ConcreteAlert.notify({
                                'message': r.message
                            });
                            ConcretePageVersionList.handleVersionUpdateResponse(r);
                            ConcreteEvent.publish('PageVersionChanged.approved', {
                                cID: <?php echo (int)$c->getCollectionID() ?>,
                                cvID: cvID
                            });
                        });

                        break;

                    case 'unapprove':
                        ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("unapprove")) ?>, [{
                            'name': 'cvID',
                            'value': cvID
                        }], function (r) {
                            ConcreteAlert.notify({
                                'message': r.message
                            });
                            ConcretePageVersionList.handleVersionUpdateResponse(r);
                            ConcreteEvent.publish('PageVersionChanged.unapproved', {
                                cID: <?php echo (int)$c->getCollectionID() ?>,
                                cvID: cvID
                            });
                        });

                        break;

                    case 'duplicate':
                        ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("duplicate")) ?>, [{
                            'name': 'cvID',
                            'value': cvID
                        }], function (r) {
                            ConcreteAlert.notify({
                                'message': r.message
                            });
                            ConcretePageVersionList.handleVersionUpdateResponse(r);
                            ConcreteEvent.publish('PageVersionChanged.duplicated', {
                                cID: <?php echo (int)$c->getCollectionID() ?>,
                                cvID: cvID
                            });
                        });

                        break;

                    case 'new-page':
                        ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("new_page")) ?>, [{
                            'name': 'cvID',
                            'value': cvID
                        }], function (r) {
                            window.location.href = r.redirectURL;
                        });

                        break;
                }


                return false;
            });

            var menuItems = $('li.ccm-menu-item-delete');

            if (menuItems.length == 1) {
                menuItems.children('span').show();
                menuItems.children('a').hide();
            } else {
                menuItems.children('a').show();
                menuItems.children('span').hide();
            }
        }

    }

    var templateBody = _.template(
        $('script.tbody').html()
    );
    var templateRow = _.template(
        $('script.version').html()
    );
    var templateFooter = _.template(
        $('script.footer').html()
    );

    var templateData = <?php /** @noinspection PhpUndefinedMethodInspection */echo $response->getJSON()?>;
    $('#ccm-panel-page-versions table tbody').html(
        templateBody(templateData)
    );
    $('#ccm-panel-page-versions table tfoot').html(
        templateFooter(templateData)
    );

    $(function () {
        ConcretePageVersionList.setupMenus();
        $('#ccm-panel-page-versions tr').on('click', 'input[type=checkbox]', function (e) {
            e.stopPropagation();
        });
        $('#ccm-panel-page-versions thead input[type=checkbox]').on('change', function () {
            var $checkboxes = $('#ccm-panel-page-versions tbody input[type=checkbox][data-version-active=false]');
            $checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
            Concrete.forceRefresh();
        });

        $('#ccm-panel-page-versions tbody').on('change', 'input[type=checkbox]', function () {
            if ($(this).is(':checked')) {
                $(this).parent().parent().addClass('ccm-panel-page-versions-version-checked');
            } else {
                $(this).parent().parent().removeClass('ccm-panel-page-versions-version-checked');
            }
            var allBoxes = $('#ccm-panel-page-versions tbody input[type=checkbox]'),
                checkboxes = allBoxes.filter(':checked'),
                notChecked = allBoxes.not(checkboxes);

            $('button[data-version-action]').prop('disabled', true);
            if (checkboxes.length > 1) {
                $('button[data-version-action=compare]').prop('disabled', false);
            }
            if (checkboxes.length > 0 && notChecked.length > 0 && !checkboxes.filter('[data-version-active=true]').length && $('#ccm-panel-page-versions tbody [data-version-menu-task=delete]').length) {
                $('button[data-version-action=delete]').prop('disabled', false);
            }

            ConcretePageVersionList.previewSelectedVersions(checkboxes);

        });

        $('#ccm-panel-page-versions tfoot').on('click', 'a', function () {
            var pageNum = $(this).attr('data-version-navigation');
            if (pageNum) {
                ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("get_json")) ?>, [{
                    'name': 'currentPage',
                    'value': $(this).attr('data-version-navigation')
                }], function (r) {
                    $('#ccm-panel-page-versions table tbody').html(
                        templateBody(r)
                    );
                    $('#ccm-panel-page-versions table tfoot').html(
                        templateFooter(r)
                    );
                    ConcretePageVersionList.setupMenus();
                });
            }
            return false;
        });

        $('button[data-version-action=delete]').on('click', function () {
            var checkboxes = $('#ccm-panel-page-versions tbody input[type=checkbox]:checked');
            var cvIDs = [];
            $.each(checkboxes, function (i, cb) {
                cvIDs.push({'name': 'cvID[]', 'value': $(cb).val()});
            });
            if (cvIDs.length > 0) {
                ConcretePageVersionList.sendRequest(<?php echo json_encode((string)$controller->action("delete")) ?>, cvIDs, function (r) {
                    ConcretePageVersionList.handleVersionRemovalResponse(r);
                    ConcreteEvent.publish('PageVersionChanged.deleted', {
                        cID: <?php echo (int)$c->getCollectionID() ?>,
                        cvID: cvIDs
                    });
                });
            }
        });
    });
</script>

<?php if ($isDialogMode): ?>
    <section id="ccm-panel-page-versions" class="ccm-ui">
        <header>
            <a href="" data-panel-navigation="back" class="ccm-panel-back">
                <svg>
                    <use xlink:href="#icon-arrow-left"/>
                </svg>
                <?php echo t('Page Settings') ?>
            </a>

            <h5><?php echo t('Versions') ?></h5>
        </header>

        <table>
            <thead></thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>

        <hr/>

        <div class="dialog-buttons">
            <button class="btn btn-secondary float-start" data-dialog-action="cancel">
                <?php echo t('Cancel') ?>
            </button>

            <button type="button" class="btn btn-danger float-end" disabled data-version-action="delete">
                <?php echo t('Delete') ?>
            </button>
        </div>
    </section>
<?php else: ?>
    <section id="ccm-panel-page-versions" class="ccm-ui">
        <header>
            <a href="" data-panel-navigation="back" class="ccm-panel-back">
                <svg>
                    <use xlink:href="#icon-arrow-left"/>
                </svg>
                <?php echo t('Page Settings') ?>
            </a>

            <h5><?php echo t('Versions') ?></h5>
        </header>

        <table>
            <thead></thead>
            <tbody></tbody>
            <tfoot></tfoot>
        </table>

        <hr/>

        <button type="button" class="btn btn-danger float-end" disabled data-version-action="delete">
            <?php echo t('Delete') ?>
        </button>
    </section>
<?php endif; ?>

