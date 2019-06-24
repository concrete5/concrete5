<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');
$list = $result->getItemListObject();
?>

<script type="text/template" data-template="search-results-table-body">
    <% _.each(items, function (item) {%>
    <tr data-launch-search-menu="<%=item.treeNodeID%>"
        data-file-manager-tree-node="<%=item.treeNodeID%>"
        data-file-manager-tree-node-type="<%=item.treeNodeTypeHandle%>"
        data-file-manager-file="<%=item.fID%>">
        <td class="ccm-search-results-icon">
            <%=item.resultsThumbnailImg%>
        </td>
        <% for (i = 0; i < item.columns.length; i++) {
        var column = item.columns[i]; %>
            <% if (i == 0) { %>
            <td class="ccm-search-results-name"><%-column.value%></td>
            <% } else { %>
            <td><%-column.value%></td>
            <% } %>
        <% } %>
    </tr>
    <% }); %>
</script>


<div data-search-element="wrapper"></div>

<div data-search-element="results">
    <div class="table-responsive ccm-file-manager-list-wrapper">
        <table class="ccm-file-manager-list ccm-search-results-table ccm-search-results-table-icon">
        <thead>
        </thead>
        <tbody>
        </tbody>
        </table>
    </div>
    <div class="ccm-search-results-pagination"></div>
</div>

<script type="text/template" data-template="search-results-pagination">
<%=paginationTemplate%>
</script>

<?php /* ?>

<script type="text/template" data-template="search-results-table-head">
<tr>
    <th><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="select-all" /></span></th>
    <th class="ccm-file-manager-search-results-star"><span><i class="fa fa-star"></i></span></th>
    <th><span><?php echo t('Thumbnail')?></th>
    <%
    for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
        <% } else { %>
            <th><span><%-column.title%></span></th>
        <% } %>
    <% } %>
</tr>
</script>

 */ ?>

<script type="text/template" data-template="search-results-table-head">
    <tr>
        <th>
            <div class="dropdown">
                <button class="btn btn-menu-launcher" disabled data-toggle="dropdown"><i class="fa fa-chevron-down"></i></button>
            </div>
        </th>
        <%
        for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
        <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%-column.title%></a></th>
        <% } else { %>
        <th><span><%-column.title%></span></th>
        <% } %>
        <% } %>
    </tr>
</script>

