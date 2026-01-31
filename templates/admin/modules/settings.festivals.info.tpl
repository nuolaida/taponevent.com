
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="festivalsInfoAct" />
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
				<td class="bigger"><input type="text" name="form[title]" size="50" value="{$data.title}" /></td>
			</tr>
			<tr>
				<td>{"starts"|translate}</td>
				<td><input type="text" name="form2[time_starts]" size="20" value="{$data.time_starts|my_date_format:"middle"}" class="date_time_selector" /></td>
			</tr>
			<tr>
				<td>{"ends"|translate}</td>
				<td><input type="text" name="form2[time_ends]" size="20" value="{$data.time_ends|my_date_format:"middle"}" class="date_time_selector" /></td>
			</tr>
			<tr>
				<td>{"active"|translate}</td>
				<td><input type="checkbox" name="form2[is_active]"{if $data.is_active} checked="checked"{/if}{if $disable_activation} disabled="disabled"{/if} /></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="countries" id=$data.id}



