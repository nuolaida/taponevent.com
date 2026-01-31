
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="judgesInfoAct" />
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
				<td>{"name"|translate}</td>
				<td class="bigger"><input type="text" name="form[name]" size="40" value="{$data.name}"></td>
			</tr>
			<tr>
				<td>{"description"|translate}</td>
				<td><input type="text" name="form[description]" size="70" value="{$data.description}"></td>
			</tr>
			<tr>
				<td>{"email"|translate}</td>
				<td><input type="text" name="form[email]" size="70" value="{$data.email}"></td>
			</tr>
			<tr>
				<td>{"website"|translate}</td>
				<td><input type="text" name="form[social_web]" size="70" value="{$data.social_web}"></td>
			</tr>
			<tr>
				<td>{"linkedin"|translate}</td>
				<td><input type="text" name="form[social_linkedin]" size="70" value="{$data.social_linkedin}"></td>
			</tr>
			<tr>
				<td>{"facebook"|translate}</td>
				<td><input type="text" name="form[social_facebook]" size="70" value="{$data.social_facebook}"></td>
			</tr>
			<tr>
				<td>{"instagram"|translate}</td>
				<td><input type="text" name="form[social_instagram]" size="70" value="{$data.social_instagram}"></td>
			</tr>
			<tr>
				<td>{"youtube"|translate}</td>
				<td><input type="text" name="form[social_youtube]" size="70" value="{$data.social_youtube}"></td>
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
						<input type="hidden" name="module" value="{$config_module}" />
						<input type="hidden" name="action" value="judgesFestivalsAddAct" />
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
						</td>
						<td>
							{if !$list_beer[$item.id]}
								<a href="/admin.php?module={$config_module}&action=judgesFestivalsDeleteAct&id={$item.relation_id}" class="delete"><i class="material-icons">delete</i></a>
                            {/if}
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</fieldset>
{/if}


{actions_log table="competition_judges" id=$data.id}
