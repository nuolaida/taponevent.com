
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="usersAddAct" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
		<tr>
			<td>{"category"|translate}</td>
			<td>
				<select name="form[category_id]" class="js-example-basic-single">
					<option value=""></option>
                    {foreach $list_categories as $id => $title}
						<option value="{$id}">{$title}</option>
                    {/foreach}
				</select>
			</td>
		</tr>
			<tr>
				<td>{"email"|translate} </td>
				<td>
					<textarea name="form[email]" class="notwysiwyg" style="width: 565px; height: 400px;">{$data.text_body}</textarea>
				</td>
			</tr>
		</tbody>
	</table>
</form>





