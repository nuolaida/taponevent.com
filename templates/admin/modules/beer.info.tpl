
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="beer" />
	<input type="hidden" name="action" value="infoAct" />
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
				<td>{"brewery"|translate}</td>
				<td>
					<input type="hidden" name="form[brewery_id]" value="{if $data}{$data.brewery_id}{else}{$data_brewery.id}{/if}">
                    {if $data}{$data.brewery_title}{else}{$data_brewery.title}{/if}
				</td>
			</tr>
			<tr>
				<td>{"festival"|translate}</td>
				<td>
					<input type="hidden" name="form[festival_id]" value="{if $data}{$data.festival_id}{else}{$data_festival.id}{/if}">
                    {if $data}{$data.festival_title}{else}{$data_festival.title}{/if}
				</td>
			</tr>
			<tr>
				<td>{"session"|translate}</td>
				<td><input type="text" name="form[festival_session_number]" size="4" value="{$data.festival_session_number}"></td>
			</tr>
			<tr>
				<td>{"untappd"|translate}</td>
				<td><input type="text" name="form[untappd_id]" size="10" value="{$data.untappd_id}"></td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="40" value="{$data.title}"></td>
			</tr>
			<tr>
				<td>{"style"|translate}</td>
				<td><input type="text" name="form[style]" size="40" value="{$data.style}"></td>
			</tr>
			<tr>
				<td>{"abv"|translate}</td>
				<td><input type="text" name="form[abv]" size="5" value="{$data.abv}">%</td>
			</tr>
			<tr>
				<td>{"description"|translate}</td>
				<td><textarea name="form[description]" rows="3" cols="100" class="notwysiwyg">{$data.description}</textarea></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="beer" id=$data.id}



