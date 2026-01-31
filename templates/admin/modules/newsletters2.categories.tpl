
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module={$config_module}&action=categoriesInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td>{"subscribed"|translate}</td>
			<td>{"unsubscribed"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach $list as $item}
			<tr{if !$item.is_active} class="inactive"{/if}>
				<td>
					{$item.title}
					{if $item.gen_key}<div class="small">{$item.gen_key}</div>{/if}
				</td>
				<td>{$item.subscribed}</td>
				<td>{$item.unsubscribed}</td>
				<td>
					<a href="{"/admin.php?module={$config_module}&action=categoriesInfo&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

