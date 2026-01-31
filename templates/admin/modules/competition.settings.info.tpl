
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="settingsInfoAct" />
	<input type="hidden" name="form2[festival_id]" value="{$data.festival_id}" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
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
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="70" value="{$data.title}" /></td>
			</tr>
			<tr>
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[description]" class="wysiwyg" style="width: 565px; height: 100px;">{$data.description}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"judges"|translate}</td>
				<td><input type="checkbox" name="form[show_judges]"{if $data.show_judges || !$data} checked="checked"{/if} /></td>
			</tr>
			<tr>
				<td>{"results"|translate}</td>
				<td><input type="checkbox" name="form[show_results]"{if $data.show_results || !$data} checked="checked"{/if} /></td>
			</tr>
		</tbody>
	</table>
</form>


{actions_log table="competition_settings" id=$data.festival_id}


