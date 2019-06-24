<?php
defined('C5_EXECUTE') or die('Access Denied.');

$tp = new TaskPermission();
if (!$tp->canAccessGroupSearch()) {
    ?>
    <p><?=t('You do not have access to the group search.')?></p>
    <?php
} else {
    $app = Concrete\Core\Support\Facade\Application::getFacadeApplication();
    $form = $app->make('helper/form');
    $request = Request::getInstance();
    $result = json_encode($controller->getSearchResultObject()->getJSONObject());
    $tree = GroupTree::get();
    $guestGroupNode = GroupTreeNode::getTreeNodeByGroupID(GUEST_GROUP_ID);
    $registeredGroupNode = GroupTreeNode::getTreeNodeByGroupID(REGISTERED_GROUP_ID);
    ?>
    <style>
        div[data-search=groups] form.ccm-search-fields {
            margin-left: 0px !important;
        }
    </style>
    <div data-search="groups">
        <script type="text/template" data-template="search-form">
            <form role="form" data-search-form="groups" action="<?=URL::to('/ccm/system/search/groups/submit')?>" class="ccm-search-fields ccm-search-fields-none">
                <input type="hidden" name="filter" value="<?=h($request->request('filter'))?>" />
                <div class="form-group">
                    <div class="ccm-search-main-lookup-field">
                        <i class="fa fa-search"></i>
                        <?=$form->search('keywords', $request->request('keywords'), ['placeholder' => t('Name')])?>
                        <button type="submit" class="ccm-search-field-hidden-submit" tabindex="-1"><?=t('Search')?></button>
                    </div>
                </div>
            </form>
        </script>
        <script type="text/template" data-template="search-results-table-body">
            <% _.each(items, function(group) {%>
                <tr>
                    <%
                    for(i = 0; i < group.columns.length; i++) {
                        var column = group.columns[i];
                        %>
                        <td><%=column.value%></td>
                        <%
                    }
                    %>
                </tr>
            <% }); %>
        </script>

        <div data-search-element="wrapper"></div>

        <div class="group-tree" data-group-tree="<?=$tree->getTreeID()?>"></div>

        <div data-search-element="results">
            <table border="0" cellspacing="0" cellpadding="0" class="ccm-search-results-table">
                <thead>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="ccm-search-results-pagination"></div>
        </div>
        
        <script type="text/template" data-template="search-results-pagination">
            <%=paginationTemplate%>
        </script>

        <script type="text/template" data-template="search-results-table-head">
            <tr>
                <%
                for (i = 0; i < columns.length; i++) {
                    var column = columns[i];
                    if (column.isColumnSortable) {
                        %>
                        <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%=column.title%></a></th>
                        <%
                    } else {
                        %>
                        <th><span><%=column.title%></span></th>
                        <%
                    }
                }
                %>
            </tr>
        </script>

        <script>
            $(function() {
                $('[data-group-tree]').concreteTree({
                    <?php
                    if ($request->request('filter') == 'assign') {
                        ?>
                        'removeNodesByKey': ['<?=$guestGroupNode->getTreeNodeID()?>','<?=$registeredGroupNode->getTreeNodeID()?>'],
                        <?php
                    }
                    if ($selectMode) {
                        ?>
                        onClick: function(node) {
                            if (node.data.gID) {
                                ConcreteEvent.publish('SelectGroup', {'gID': node.data.gID, 'gName': node.title});
                            } else {
                                return false;
                            }
                        },
                        'enableDragAndDrop': false,
                        <?php
                    }
                    ?>
                    'treeID': '<?=$tree->getTreeID()?>'
                });
                $('div[data-search=groups]').concreteAjaxSearch({
                    result: <?=$result?>,
                    onLoad: function(concreteSearch) {
                        var handleSubmit = function() {
                            var $input = concreteSearch.$element.find('input[name=keywords]');
                            if ($input.val() != '') {
                                concreteSearch.$element.find('[data-group-tree]').hide();
                                concreteSearch.$results.show();
                            } else {
                                concreteSearch.$element.find('[data-group-tree]').show();
                                concreteSearch.$results.hide();
                            }
                        }
                        concreteSearch.$element.on('submit', 'form[data-search-form=groups]', handleSubmit);
                        handleSubmit();
                        concreteSearch.$element.on('keyup', 'input[name=keywords]', function(e) {
                            if ($(this).val() == '') {
                                handleSubmit();
                            }
                        });
                        <?php
                        if ($selectMode) {
                            ?>
                            concreteSearch.$element.on('click', 'a[data-group-id]', function() {
                                ConcreteEvent.publish('SelectGroup', {
                                    gID: $(this).attr('data-group-id'),
                                    gName: $(this).attr('data-group-name')
                                });
                                return false;
                            });
                            <?php
                        }
                        ?>
                    }
                });
            });
        </script>
    </div>
    <?php
}
