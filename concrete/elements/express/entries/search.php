<?php defined('C5_EXECUTE') or die('Access Denied.'); ?>

<div data-search="express_entries">

    <script type="text/template" data-template="search-results-table-body">
        <% _.each(items, function(entity) {%>
        <tr data-entity-id="<%=entity.id%>">
            <% for(i = 0; i < entity.columns.length; i++) {
            var column = entity.columns[i];
            %>
            <td><%=column.value%></td>
            <% } %>
        </tr>
        <% }); %>
    </script>

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
            if (column.isColumnSortable) { %>
            <th class="<%=column.className%>"><a href="<%=column.sortURL%>"><%=column.title%></a></th>
            <% } else { %>
            <th><span><%=column.title%></span></th>
            <% } %>
            <% } %>
        </tr>
    </script>

</div>