<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=festivalsInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"active"|translate}</td>
			<td>{"title"|translate}</td>
			<td>{"starts"|translate}</td>
			<td>{"ends"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$list item=item}
			<tr>
				<td>{if $item.is_active}<i class="material-icons">done</i>{/if}</td>
				<td>{$item.title}</td>
				<td>{if $item.time_starts}{$item.time_starts|my_date_format:"middle"}{/if}</td>
				<td>{if $item.time_ends}{$item.time_ends|my_date_format:"middle"}{/if}</td>
				<td>
					<a href="{"/admin.php?module=settings&action=festivalsInfo&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>

