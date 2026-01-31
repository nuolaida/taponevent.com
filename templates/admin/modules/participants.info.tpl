
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="participants" />
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
				<td class="bigger"><input type="text" name="form[title]" size="30" value="{$data.title}"></td>
			</tr>
			<tr>
				<td>{"description short"|translate}</td>
				<td><input type="text" name="form[description_short]" size="50" value="{$data.description_short}"></td>
			</tr>
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[description]" class="wysiwyg" style="width: 565px; height: 400px;">{$data.description}</textarea>
				</td>
			</tr>
		</tbody>
	</table>
</form>


{if $data}
	<br /><br />
	<fieldset>
		<legend><i class="material-icons">location_city</i> {"festivals"|translate}</legend>
		{if $list_festivals_unrelated}
			<ul class="main_page_menu">
				<li>
					<form action="{"/admin.php"|amake_url}" method="post">
						<input type="hidden" name="module" value="participants" />
						<input type="hidden" name="action" value="festivalsAddAct" />
						<input type="hidden" name="form[rec_id]" value="{$data.id}" />
						<i class="material-icons">add</i>
						<select name="form[festival_id]">
							{foreach $list_festivals_unrelated as $item}
								<option value="{$item.id}">{$item.title}</option>
							{/foreach}
							<input type="submit" value="{"add"|translate}" />
						</select>
			        </form>
				</li>
			</ul>
		{/if}
		<table class="tbl_list">
			<thead>
				<tr>
					<td>{"title"|translate}</td>
					<td class="actions"></td>
				</tr>
			</thead>
			<tbody>
				{foreach from=$list_festivals_related item=item}
					<tr>
						<td>{$item.title}</td>
						<td>
							<a href="/admin.php?module=participants&action=festivalsDeleteAct&id={$item.relation_id}"><i class="material-icons">delete</i></a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</fieldset>
{/if}


{actions_log table="breweries" id=$data.id}




<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		set_cities();

		$("#brewery_country_select").change(function() {
			set_cities();
		});


		function set_cities() {
			country_id = $("#brewery_country_select").find(":selected").val();
			city_id = {if $data.city_id}{$data.city_id}{else}0{/if};
			$("#brewery_city_select").empty();
			$.getJSON("/admin.php?module=breweries&action=citiesAjax",
					{
						country_id: country_id,
						format: "json"
					},
					function(data) {
						$("#brewery_city_select").append('<option value="0">---</option>');
						$.each(data, function(i,item) {
							if (item.id == city_id) {
								$("#brewery_city_select").append('<option value="' + item.id + '" selected="selected">' + item.title + '</option>');
							} else {
								$("#brewery_city_select").append('<option value="' + item.id + '">' + item.title + '</option>');
							}
						});
					});
		}

	});
</script>
