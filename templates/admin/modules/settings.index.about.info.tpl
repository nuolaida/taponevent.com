
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="indexAboutInfoAct" />
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
				<td><input type="text" name="form[varchar_title]" size="80" value="{$data.varchar_title}"></td>
			</tr>
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[text_description]" class="wysiwyg" style="width: 565px; height: 200px;">{$data.text_description}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"image"|translate}</td>
				<td class="gallery3_select">
					<div class="gallery3_select_item">
						<input type="hidden" class="form_gallery3_id" name="form[gallery_id_1]" value="{$data.gallery_id_1}" />
						<input type="hidden" class="form_gallery3_type" name="" value="crop590x220" />
						<input type="hidden" class="form_gallery3_name" name="" value="{$data.title}" />
						<input type="hidden" class="form_gallery3_name_field" name="" value="form[varchar_title_1]" />
						<div class="form_gallery3_area"></div>
						<input type="button" value="{"select"|translate}" class="button form_gallery3_select" />
						<input type="button" value="{"empty"|translate}" class="button form_gallery3_deselect" />
					</div>
					590x230px
				</td>
			</tr>
			<tr>
				<td>{"image"|translate}</td>
				<td class="gallery3_select">
					<div class="gallery3_select_item">
						<input type="hidden" class="form_gallery3_id" name="form[gallery_id_2]" value="{$data.gallery_id_2}" />
						<input type="hidden" class="form_gallery3_type" name="" value="any280x230" />
						<input type="hidden" class="form_gallery3_name" name="" value="{$data.title}" />
						<input type="hidden" class="form_gallery3_name_field" name="" value="form[varchar_title_1]" />
						<div class="form_gallery3_area"></div>
						<input type="button" value="{"select"|translate}" class="button form_gallery3_select" />
						<input type="button" value="{"empty"|translate}" class="button form_gallery3_deselect" />
					</div>
					280x230px
				</td>
			</tr>
			<tr>
				<td>{"image"|translate}</td>
				<td class="gallery3_select">
					<div class="gallery3_select_item">
						<input type="hidden" class="form_gallery3_id" name="form[gallery_id_3]" value="{$data.gallery_id_3}" />
						<input type="hidden" class="form_gallery3_type" name="" value="any280x230" />
						<input type="hidden" class="form_gallery3_name" name="" value="{$data.title}" />
						<input type="hidden" class="form_gallery3_name_field" name="" value="form[varchar_title_1]" />
						<div class="form_gallery3_area"></div>
						<input type="button" value="{"select"|translate}" class="button form_gallery3_select" />
						<input type="button" value="{"empty"|translate}" class="button form_gallery3_deselect" />
					</div>
					280x230px
				</td>
			</tr>
			<tr>
				<td>{"button"|translate}</td>
				<td>
					<input type="text" name="form[varchar_button_title]" size="40" value="{$data.varchar_button_title}"> {"title"|translate}<br />
					<input type="text" name="form[button_link]" size="40" value="{$data.button_link}"> {"link"|translate}
				</td>
			</tr>
		</tbody>
	</table>
</form>

{actions_log table="index_pages" id=$data.id}



