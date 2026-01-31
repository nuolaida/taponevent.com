<form action="/admin.php" method="get">
	<input type="hidden" name="module" value="pictures" />
	<input type="hidden" name="action" value="browse" />
	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search}" class="focus" /></td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<ul class="main_page_menu">
	<li><a href="/admin.php?module=pictures&action=info" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"id"|translate}</td>
			<td>{"picture"|translate}</td>
			<td>{"keywords"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$list item=item}
			<tr>
				<td>{$item.id}</td>
				<td><a class="lightbox" href="{"/index.php?module=images&action=show&id=`$item.id`&type=any1000x1000"|make_url}" title=""><img src="{"/index.php?module=images&action=show&id=`$item.id`&type=any200x200"|make_url}" border="0" /></a></td>
				<td>{$item.keywords}</td>
				<td>
					<a href="/admin.php?module=pictures&action=info&id={$item.id}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>

