
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="breweries" />
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
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[description]" class="wysiwyg" style="width: 565px; height: 400px;">{$data.description}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"email"|translate}</td>
				<td><input type="text" name="form[email]" size="70" value="{$data.email}"></td>
			</tr>
			<tr>
				<td>{"website"|translate}</td>
				<td><input type="text" name="form[link_social_website]" size="70" value="{$data.link_social_website}"></td>
			</tr>
			<tr>
				<td>{"untappd"|translate}</td>
				<td><input type="text" name="form[link_social_untappd]" size="70" value="{$data.link_social_untappd}"></td>
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
		<legend><i class="material-icons">location_city</i> {"festivals"|translate}</legend>
		{if $list_festivals_unrelated}
			<ul class="main_page_menu">
				<li>
					<form action="{"/admin.php"|amake_url}" method="post">
						<input type="hidden" name="module" value="breweries" />
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
						<td>
							<strong>{$item.title}</strong>
							<table class="tbl_list">
								<tbody>
                                {foreach from=$list_beer[$item.id] item=item_beer}
									<tr>
										<td>{$item_beer.festival_session_number}</td>
										<td>{$item_beer.title}</td>
										<td>{$item_beer.style}</td>
										<td>{$item_beer.abv}%</td>
										<td><i>{$item_beer.description}</i></td>
										<td nowrap="nowrap">
											{if $item_beer.untappd_id}
												<a href="https://untappd.com/b/a/{$item_beer.untappd_id}" target="_blank" class="delete"><i class="material-icons">open_in_new</i></a>
											{/if}
											<a href="/admin.php?module=beer&action=info&id={$item_beer.id}"><i class="material-icons">edit</i></a>
											<a href="/admin.php?module=beer&action=deleteAct&id={$item_beer.id}" class="delete"><i class="material-icons">delete</i></a>
										</td>
									</tr>
                                {/foreach}
                                <tr>
	                                <td></td>
	                                <td></td>
	                                <td></td>
	                                <td></td>
	                                <td></td>
	                                <td>
		                                <a href="/admin.php?module=beer&action=info&festival_id={$item.id}&brewery_id={$data.id}"><i class="material-icons">add</i></a>
	                                </td>
                                </tr>
								</tbody>
							</table>

						</td>
						<td>
							{if !$list_beer[$item.id]}
								<a href="/admin.php?module=breweries&action=festivalsDeleteAct&id={$item.relation_id}" class="delete"><i class="material-icons">delete</i></a>
                            {/if}
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
