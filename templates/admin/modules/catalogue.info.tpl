
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="infoAct" />
	<input type="hidden" name="form2[type]" value="{$config_type}" />
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
				<td>{"city"|translate}</td>
				<td>
					<select name="form2[country_id]" id="brewery_country_select" class="js-example-basic-single" style="width: 250px;">
						<option value=""></option>
						{foreach $list_countries as $item}
							<option value="{$item.id}"{if $item.id == $data.country_id} selected="selected"{/if}>{$item.title}</option>
						{/foreach}
					</select>
					<select name="form[city_id]" id="brewery_city_select" class="js-example-basic-single" style="width: 250px;">
					</select>
				</td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td class="bigger"><input type="text" name="form[title]" size="30" value="{$data.title}"></td>
			</tr>
			<tr>
				<td>{"type"|translate}</td>
				<td>
					<select name="form[drink_type]" class="js-example-basic-single">
						<option value=""></option>
                        {foreach $list_drink_types as $key => $value}
							<option value="{$key}"{if $key == $data.drink_type} selected="selected"{/if}>{$value}</option>
                        {/foreach}
					</select>
				</td>
			</tr>
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[description]" class="wysiwyg" style="width: 565px; height: 400px;">{$data.description}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"website"|translate}</td>
				<td><input type="text" name="form[link_social_website]" size="70" value="{$data.link_social_website}"></td>
			</tr>
			<tr>
				<td>{"facebook"|translate}</td>
				<td><input type="text" name="form[link_social_facebook]" size="70" value="{$data.link_social_facebook}"></td>
			</tr>
			<tr>
				<td>{"instagram"|translate}</td>
				<td><input type="text" name="form[link_social_instagram]" size="70" value="{$data.link_social_instagram}"></td>
			</tr>
			<tr>
				<td>{"twitter"|translate}</td>
				<td><input type="text" name="form[link_social_twitter]" size="70" value="{$data.link_social_twitter}"></td>
			</tr>
			<tr>
				<td>{"youtube"|translate}</td>
				<td><input type="text" name="form[link_social_youtube]" size="70" value="{$data.link_social_youtube}"></td>
			</tr>
		</tbody>
	</table>
</form>


{if $data}
	<br /><br />
	<fieldset>
		<legend><i class="material-icons">event</i> {"date"|translate}</legend>
		<form action="{"/admin.php"|amake_url}" method="post">
			<input type="hidden" name="module" value="{$config_module}" />
			<input type="hidden" name="action" value="datesInfoAct" />
			<input type="hidden" name="form[item_id]" value="{$data.id}" />
			<input type="hidden" name="form2[id]" value="{$data_dates.id}" />
			<table class="tbl_form">
				<tr>
					<td>
	                    {"from"|translate}:
						<input type="text" name="form[date_start]" size="10" value="{$data_dates.date_start|my_date_format:"short"}" class="date_selector" autocomplete="off" />
					</td>
					<td>
	                    {"till"|translate}:
						<input type="text" name="form[date_end]" size="10" value="{$data_dates.date_end|my_date_format:"short"}" class="date_selector" autocomplete="off" />
					</td>
					<td>
						<input type="submit" value="{"save"|translate}" />
					</td>
				</tr>
			</table>
		</form>

		<table class="tbl_list">
			<thead>
				<tr>
					<td>{"starts"|translate}</td>
					<td>{"ends"|translate}</td>
					<td class="actions"></td>
				</tr>
			</thead>
			<tbody>
				{foreach from=$list_dates item=item_dates}
					<tr>
						<td>{$item_dates.date_start|my_date_format:"short"}</td>
						<td>{if $item_dates.date_end}{$item_dates.date_end|my_date_format:"short"}{/if}</td>
						<td>
							<a href="/admin.php?module={$config_module}&action=info&id={$data.id}&dates_id={$item_dates.id}" title="{"edit"|translate}"><i class="material-icons">edit</i></a>
							<a href="/admin.php?module={$config_module}&action=datesDeleteAct&id={$item_dates.id}" title="{"delete"|translate}" class="delete"><i class="material-icons">delete</i></a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</fieldset>
{/if}


{actions_log table="catalogue_items" id=$data.id}




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
			$.getJSON("/admin.php?module={$config_module}&action=citiesAjax",
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
