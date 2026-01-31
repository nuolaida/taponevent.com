
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="medalCatInfoAct" />
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
				<td>{"festival"|translate}</td>
				<td class="bigger">{$data_festival.title}</td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="70" value="{$data.medal_title}" /></td>
			</tr>
		</tbody>
	</table>
</form>



{actions_log table="competition_medal_categories" id=$data.id}


