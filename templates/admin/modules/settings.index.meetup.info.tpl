
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="indexMeetUpInfoAct" />
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
				<td><input type="text" name="form[icon]" size="30" value="{$data.icon}"></td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[varchar_title]" size="80" value="{$data.varchar_title}"></td>
			</tr>
			<tr>
				<td>{"number"|translate}</td>
				<td><input type="text" name="form[rec_number]" size="10" value="{$data.rec_number}"></td>
			</tr>
			<tr>
				<td>{"description"|translate}</td>
				<td><input type="text" name="form[varchar_description]" size="80" value="{$data.varchar_description}"></td>
			</tr>
			<tr>
				<td>{"position"|translate}</td>
				<td><input type="text" name="form[rec_position]" size="2" class="number" value="{$data.rec_position}" /></td>
			</tr>
		</tbody>
	</table>
</form>
<a href="https://fontawesome.com/icons?d=gallery" target="_blank">https://fontawesome.com/icons?d=gallery</a>

{actions_log table="index_pages" id=$data.id}



