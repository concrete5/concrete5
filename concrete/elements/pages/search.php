<?php
defined('C5_EXECUTE') or die('Access Denied.');
$form = Loader::helper('form');

?>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (page) {%>
<tr data-launch-search-menu="<%=page.cID%>" data-page-id="<%=page.cID%>" data-page-name="<%-page.cvName%>">
    <td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-search-checkbox="individual" value="<%=page.cID%>" /></span></td>
    <% for (i = 0; i < page.columns.length; i++) {
        var column = page.columns[i];
        if (column.key == 'cv.cvName') { %>
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
        <th><span class="ccm-search-results-checkbox">
            <select data-bulk-action="pages" disabled class="ccm-search-bulk-action form-control">
                <option value=""><?php echo t('Items Selected')?></option>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Page Properties')?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/page/bulk/properties')?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Edit Properties')?></option>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Move/Copy')?>" data-bulk-action-url="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/sitemap_search_selector" data-bulk-action-dialog-width="90%" data-bulk-action-dialog-height="70%"><?php echo t('Move/Copy')?></option>
                <?php /*	    <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Speed Settings')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/speed_settings" data-bulk-action-dialog-width="610" data-bulk-action-dialog-height="340"><?=t('Speed Settings')?></option>
                <?php if (Config::get('concrete.permissions.model') == 'advanced') { ?>
                    <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Change Permissions')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions" data-bulk-action-dialog-width="430" data-bulk-action-dialog-height="630"><?=t('Change Permissions')?></option>
                    <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Change Permissions - Add Access')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions_access?task=add" data-bulk-action-dialog-width="440" data-bulk-action-dialog-height="200"><?=t('Change Permissions - Add Access')?></option>
                    <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Change Permissions - Remove Access')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/permissions_access?task=remove" data-bulk-action-dialog-width="440" data-bulk-action-dialog-height="300"><?=t('Change Permissions - Remove Access')?></option>
                <?php } ?>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?=t('Design')?>" data-bulk-action-url="<?=REL_DIR_FILES_TOOLS_REQUIRED?>/pages/design" data-bulk-action-dialog-width="610" data-bulk-action-dialog-height="405"><?=t('Design')?></option>
     */ ?>
                <option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Delete')?>" data-bulk-action-url="<?php echo REL_DIR_FILES_TOOLS_REQUIRED?>/pages/delete" data-bulk-action-dialog-width="500" data-bulk-action-dialog-height="400"><?php echo t('Delete')?></option>
            </select>
            <input type="checkbox" data-search-checkbox="select-all" class="ccm-flat-checkbox" />
            </span>
        </th>
        <%
        for (i = 0; i < columns.length; i++) {
        var column = columns[i];
        if (column.isColumnSortable) { %>
        <th class="<%= column.className %>"><a href="<%=column.sortURL%>"><%- column.title %></a></th>
        <% } else { %>
        <th><span><%- column.title %></span></th>
        <% } %>
        <% } %>
    </tr>
</script>
