
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="categoriesInfoAct" />
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
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="40" value="{$data.title}" /></td>
			</tr>
			<tr>
				<td>{"key"|translate}</td>
				<td>{$data.gen_key}</td>
			</tr>
			<tr>
				<td>{"active"|translate}</td>
				<td><input type="checkbox" name="form[is_active]"{if $data.is_active || !$data} checked="checked"{/if} /></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="newsletters2_categories" id=$data.id}






