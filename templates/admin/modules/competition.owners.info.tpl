
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="ownersInfoAct" />
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
				<td><input type="text" name="form[title]" size="70" value="{$data.title}" /></td>
			</tr>
			<tr>
				<td>{"name"|translate}</td>
				<td><input type="text" name="form[real_name]" size="70" value="{$data.real_name}" /></td>
			</tr>
			<tr>
				<td>{"email"|translate}</td>
				<td><input type="text" name="form[email]" size="70" value="{$data.email}" /></td>
			</tr>
			<tr>
				<td>{"phone"|translate}</td>
				<td><input type="text" name="form[phone]" size="70" value="{if $data.phone}{$data.phone}{else}+3706{/if}" /></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<select name="form[is_professional]" class="js-example-basic-single">
						<option value="0"{if !$data.is_professional} selected="selected"{/if}>{"competition amateur"|translate}</option>
						<option value="1"{if $data.is_professional} selected="selected"{/if}>{"competition professional"|translate}</option>
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</form>


{actions_log table="competition_owners" id=$data.id}


