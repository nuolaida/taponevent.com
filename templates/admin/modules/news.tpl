<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="news" />
	<input type="hidden" name="action" value="browse" />

	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search}" class="focus" /></td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=news&action=info"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"date"|translate}</td>
			<td>{"title"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$list item=item}
			<tr{if !$item.rec_time} class="inactive"{/if}>
				<td>{$item.rec_time|my_date_format:"short"}</td>
				<td>{$item.title}</td>
				<td>
					<a href="{"/admin.php?module=news&action=info&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>

