
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="newsletters" />
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
						<input type="hidden" class="form_gallery3_type" name="" value="crop400x200" />
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
				<td class="bigger"><input type="text" name="form[text_subject]" size="40" value="{$data.text_subject}"></td>
			</tr>
			<tr valign="top">
				<td>{"text"|translate} </td>
				<td>
					<textarea name="form[text_body]" class="wysiwyg" style="width: 565px; height: 400px;">{$data.text_body}</textarea>
				</td>
			</tr>
			<tr>
				<td>{"time"|translate}</td>
				<td><input type="text" name="form2[rec_time]" size="10" value="{$data.rec_time|my_date_format:"short"}" class="date_selector"></td>
			</tr>
		</tbody>
	</table>
</form>

{if $data}
	<br /><br />
	<h3>{"test"|translate}</h3>

	<form action="{"/admin.php"|amake_url}" method="post">
		<input type="hidden" name="module" value="newsletters" />
		<input type="hidden" name="action" value="sendTestAct" />
		<input type="hidden" name="form2[id]" value="{$data.id}" />
		<table class="tbl_form">
			<tbody>
			<tr>
				<td>{"email"|translate}</td>
				<td><input type="text" name="form[email]" size="20" value=""></td>
				<td><input type="submit" value="{"send"|translate}" /></td>
			</tr>
			</tbody>
		</table>
	</form>




	<br /><br />
	<h3>{"sent"|translate}</h3>

	{if !$data_sent.total}
		{"newsletter not sent"|translate}
		<i class="material-icons">add</i>
		<a href="{"/admin.php?module=newsletters&action=sendStartAct&id={$data.id}"|amake_url}" title="{"send"|translate}">{"visitors"|translate}</a>
		<a href="{"/admin.php?module=newsletters&action=sendStartBreweriesAct&id={$data.id}"|amake_url}" title="{"send"|translate}">{"breweries"|translate}</a>
	{else}
		{$data_sent.done}/{$data_sent.total} ({"opened"|translate}: {$data_sent.opened})
		<a href="{"/index.php?module=newsletters&action=send&id=100"|make_url}" title="{"send"|translate}"><i class="material-icons">email</i></a>
		<br /><br />

		<table class="tbl_list">
			<thead>
			<tr>
				<td>{"date"|translate}</td>
				<td>{"user"|translate}</td>
				<td>{"opened"|translate}</td>
				<td class="actions"></td>
			</tr>
			</thead>
			<tbody>
			{foreach from=$list_sent item=item}
				<tr>
					<td>{$item.sent_time|my_date_format:"middle"}</td>
					<td>{if $item.user_email}{$item.user_email}{else}{$item.email}{/if}</td>
					<td>{$item.opened_times}</td>
					<td>
						<a href="{"/admin.php?module=newsletters&action=usersEdit&id={$item.user_id}"|amake_url}"><i class="material-icons">search</i></a>
						{if !$item.sent_time}
							<a href="{"/admin.php?module=newsletters&action=sentDeleteAct&email_id={$data.id}&user_id={$item.user_id}"|amake_url}"><i class="material-icons">delete</i></a>
						{/if}
					</td>
				</tr>
			{/foreach}
			</tbody>
		</table>
	{/if}


	<div class="main_paging">{$paging}</div>


	{actions_log table="newsletters_emails" id=$data.id}
{/if}






