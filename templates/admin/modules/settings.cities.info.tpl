
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="citiesInfoAct" />
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
				<td>{"country"|translate}</td>
				<td>
					<select name="form[country_id]">
						<option value=""></option>
						{foreach $list_countries as $item}
							<option value="{$item.id}"{if $data.country_id == $item.id} selected="selected"{/if}>{$item.title}</option>
						{/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td class="bigger"><input type="text" name="form[title]" size="30" value="{$data.title}"></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="cities" id=$data.id}



