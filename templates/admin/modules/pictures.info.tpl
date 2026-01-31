
<form action="/admin.php" method="post" enctype="multipart/form-data">
	<input type="hidden" name="module" value="pictures" />
	<input type="hidden" name="action" value="infoAct" />
	<input type="hidden" name="form2[id]" value="{$data.id}" />
	<input type="hidden" name="referer" value="{$form_referer}" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>{"picture"|translate}:</td>
				<td>
					{if $data}
						<a class="lightbox" href="{"/index.php?module=images&action=show&id=`$data.id`&type=any1000x1000"|make_url}" title="{$data.rec_name}"><img src="{"/index.php?module=images&action=show&id=`$data.id`&type=any200x200"|make_url}" border="0" /></a>
					{else}
						<input type="file" name="image" size="30" />
					{/if}
				</td>
			</tr>
			<tr>
				<td>{"keywords"|translate}</td>
				<td><input type="text" name="form[keywords]" size="80" value="{$data.keywords}"></td>
			</tr>
			{if $is_last_record}
				<tr>
					<td>{"delete"|translate}</td>
					<td><input type="checkbox" name="form2[delete]" /></td>
				</tr>
			{/if}
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
						<input type="hidden" name="module" value="pictures" />
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
							<a href="/admin.php?module=pictures&action=festivalsDeleteAct&id={$item.relation_id}"><i class="material-icons">delete</i></a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</fieldset>
{/if}


{if $data}
	<br /><br />
	<fieldset>
		<legend><i class="material-icons">picture_in_picture</i> {"breweries"|translate}</legend>
		{if $list_breweries_unrelated}
			<ul class="main_page_menu">
				<li>
					<form action="{"/admin.php"|amake_url}" method="post">
						<input type="hidden" name="module" value="pictures" />
						<input type="hidden" name="action" value="relationsAddAct" />
						<input type="hidden" name="form[gallery_id]" value="{$data.id}" />
						<input type="hidden" name="form[rec_table]" value="breweries" />
						<i class="material-icons">add</i>
						<select name="form[rec_id]">
							{foreach $list_breweries_unrelated as $item}
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
				{foreach from=$list_breweries_related item=item}
					<tr>
						<td>{$item.title}</td>
						<td>
							<a href="/admin.php?module=pictures&action=relationsDeleteAct&id={$item.relation_id}"><i class="material-icons">delete</i></a>
						</td>
					</tr>
				{/foreach}
			</tbody>
		</table>
	</fieldset>

	<br /><br />
	<fieldset>
		<legend><i class="material-icons">store_mall_directory</i> {"participants"|translate}</legend>
		{if $list_participants_unrelated}
			<ul class="main_page_menu">
				<li>
					<form action="{"/admin.php"|amake_url}" method="post">
						<input type="hidden" name="module" value="pictures" />
						<input type="hidden" name="action" value="relationsAddAct" />
						<input type="hidden" name="form[gallery_id]" value="{$data.id}" />
						<input type="hidden" name="form[rec_table]" value="participants" />
						<i class="material-icons">add</i>
						<select name="form[rec_id]">
							{foreach $list_participants_unrelated as $item}
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
			{foreach from=$list_participants_related item=item}
				<tr>
					<td>{$item.title}</td>
					<td>
						<a href="/admin.php?module=pictures&action=relationsDeleteAct&id={$item.relation_id}"><i class="material-icons">delete</i></a>
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	</fieldset>
{/if}


{actions_log table="gallery" id=$data.id}

