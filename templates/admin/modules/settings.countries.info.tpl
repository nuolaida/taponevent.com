
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="countriesInfoAct" />
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
				<td>{"code"|translate}</td>
				<td class="bigger"><input type="text" name="form[keyword]" size="3" value="{$data.keyword}"></td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="30" value="{$data.title}"></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="countries" id=$data.id}



