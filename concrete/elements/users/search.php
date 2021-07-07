<?php defined('C5_EXECUTE') or die("Access Denied.");

$app = Concrete\Core\Support\Facade\Application::getFacadeApplication();

$form = $app->make('helper/form');
$ek = PermissionKey::getByHandle('edit_user_properties');
$ik = PermissionKey::getByHandle('activate_user');
$dk = PermissionKey::getByHandle('delete_user');
$gk = PermissionKey::getByHandle('assign_group');
?>

<script type="text/template" data-template="search-results-table-body">
<% _.each(items, function (user) {%>
	<tr>
		<td><span class="ccm-search-results-checkbox"><input type="checkbox" class="ccm-flat-checkbox" data-user-id="<%-user.uID%>" data-user-name="<%-user.uName%>" data-user-email="<%-user.uEmail%>" data-search-checkbox="individual" value="<%-user.uID%>" /></span></td>
		<% for (i = 0; i < user.columns.length; i++) {
			var column = user.columns[i];
			%>
			<td><%= column.value %></td>
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
		<th>
			<span class="ccm-search-results-checkbox">
				<select data-bulk-action="users" disabled class="ccm-search-bulk-action form-select">
					<option value=""><?php echo t('Items Selected'); ?></option>
					<?php
                    if ($ek->validate()) {
                        ?>
						<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Edit Properties'); ?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/properties'); ?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Edit Properties'); ?></option>
						<?php
                    }
                    if ($ik->validate()) {
                        ?>
						<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Activate Users'); ?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/activate'); ?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Activate Users'); ?></option>
						<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Deactivate Users'); ?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/deactivate'); ?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Deactivate Users'); ?></option>
						<?php
                    }
                    if ($gk->validate()) {
                        ?>
						<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Add to Group'); ?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/groupadd'); ?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Add to Group'); ?></option>
						<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Remove From Group'); ?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/groupremove'); ?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Remove From Group'); ?></option>
						<?php
                    }
                    if ($dk->validate()) {
                        ?>
						<option data-bulk-action-type="dialog" data-bulk-action-title="<?php echo t('Delete Users'); ?>" data-bulk-action-url="<?php echo URL::to('/ccm/system/dialogs/user/bulk/delete'); ?>" data-bulk-action-dialog-width="630" data-bulk-action-dialog-height="450"><?php echo t('Delete'); ?></option>
						<?php
                    }

                    /*
                    if (isset($mode) && $mode == 'choose_multiple') {
                        ?>
                        <option value="choose"><?php echo t('Choose')?></option>
                        <?php
                    }*/
                    ?>
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
