
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="newsletters" />
	<input type="hidden" name="action" value="usersAddAct" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
			<tr valign="top">
				<td>{"email"|translate} </td>
				<td>
					<textarea name="form[email]" class="notwysiwyg" style="width: 565px; height: 400px;">{$data.text_body}</textarea>
				</td>
			</tr>
		</tbody>
	</table>
</form>





