
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="indexTicketsInfoAct" />
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
				<td>{"price"|translate}</td>
				<td><input type="text" name="form[price]" size="4" class="number" value="{$data.price}">€</td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[varchar_title]" size="30" value="{$data.varchar_title}"></td>
			</tr>
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[text_description]" class="wysiwyg" style="width: 300px; height: 90px;">{$data.text_description}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"link"|translate}</td>
				<td><input type="text" name="form[link]" size="70" value="{$data.link}"></td>
			</tr>
			<tr>
				<td>{"position"|translate}</td>
				<td><input type="text" name="form[rec_position]" size="2" class="number" value="{$data.rec_position}" /></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="index_pages" id=$data.id}



