
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="texts" />
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
				<td>{"title"|translate}</td>
				<td class="bigger"><input type="text" name="form[title]" size="30" value="{$data.title}"></td>
			</tr>
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[description]" class="wysiwyg" style="width: 565px; height: 400px;">{$data.description}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"image"|translate}</td>
				<td class="gallery3_select">
					<div class="gallery3_select_item">
						<input type="hidden" class="form_gallery3_id" name="form[gallery_id]" value="{$data.gallery_id}" />
						<input type="hidden" class="form_gallery3_type" name="" value="any200x200" />
						<input type="hidden" class="form_gallery3_name" name="" value="{$data.title}" />
						<input type="hidden" class="form_gallery3_name_field" name="" value="form[title]" />
						<div class="form_gallery3_area"></div>
						<input type="button" value="{"select"|translate}" class="button form_gallery3_select" />
						<input type="button" value="{"empty"|translate}" class="button form_gallery3_deselect" />
					</div>
				</td>
			</tr>
			<tr>
				<td>{"active"|translate}</td>
				<td><input type="checkbox" name="form[is_active]"{if $data.is_active || !$data} checked="checked"{/if} /></td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="texts" id=$data.id}



