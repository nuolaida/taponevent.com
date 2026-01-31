
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="indexPlaceInfoAct" />
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
				<td>{"icon"|translate}</td>
				<td><input type="text" name="form[icon]" size="20" class="number" value="{$data.icon}" /></td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[varchar_title]" size="30" value="{$data.varchar_title}" /></td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[varchar_description]" size="30" value="{$data.varchar_description}" /></td>
			</tr>
			<tr>
				<td>{"position"|translate}</td>
				<td><input type="text" name="form[rec_position]" size="2" class="number" value="{$data.rec_position}" /></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="index_pages" id=$data.id}



