
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="settings" />
	<input type="hidden" name="action" value="indexSponsorsInfoAct" />
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
						<input type="hidden" class="form_gallery3_type" name="" value="any150x120" />
						<input type="hidden" class="form_gallery3_name" name="" value="{$data.title}" />
						<input type="hidden" class="form_gallery3_name_field" name="" value="" />
						<div class="form_gallery3_area"></div>
						<input type="button" value="{"select"|translate}" class="button form_gallery3_select" />
						<input type="button" value="{"empty"|translate}" class="button form_gallery3_deselect" />
					</div>
					150x120px
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



