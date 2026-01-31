
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="indexMapInfoAct" />
	<input type="hidden" name="form2[id]" value="{$data.id}" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>{"latitude"|translate}</td>
				<td><input type="text" name="form[latitude]" size="20" class="number" value="{$data.latitude}" /></td>
			</tr>
			<tr>
				<td>{"longitude"|translate}</td>
				<td><input type="text" name="form[longitude]" size="20" class="number" value="{$data.longitude}" /></td>
			</tr>
			<tr>
				<td>{"address"|translate}</td>
				<td><input type="text" name="form[varchar_address]" size="40" value="{$data.varchar_address}" /></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="index_pages" id=$data.id}



