
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="speakersInfoAct" />
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
				<td>{"image"|translate}</td>
				<td class="gallery3_select">
					<div class="gallery3_select_item">
						<input type="hidden" class="form_gallery3_id" name="form[gallery_id]" value="{$data.gallery_id}" />
						<input type="hidden" class="form_gallery3_type" name="" value="crop100x100" />
						<input type="hidden" class="form_gallery3_name" name="" value="{$data.title}" />
						<input type="hidden" class="form_gallery3_name_field" name="" value="form[title]" />
						<div class="form_gallery3_area"></div>
						<input type="button" value="{"select"|translate}" class="button form_gallery3_select" />
						<input type="button" value="{"empty"|translate}" class="button form_gallery3_deselect" />
					</div>
				</td>
			</tr>
			<tr>
				<td>{"name"|translate}</td>
				<td><input type="text" name="form[name]" size="70" value="{$data.name}" /></td>
			</tr>
			<tr>
				<td>{"company"|translate}</td>
				<td><input type="text" name="form[company]" size="70" value="{$data.speaker_company}" /></td>
			</tr>
			<tr>
				<td>{"email"|translate}</td>
				<td><input type="text" name="form[email]" size="70" value="{$data.email}" /></td>
			</tr>
		</tbody>
	</table>
</form>


{actions_log table="conference_speakers" id=$data.id}


