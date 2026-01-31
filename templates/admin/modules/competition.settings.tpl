<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=`$config_module`&action=medalCat"|amake_url}">{"medal categories"|translate}</a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
	    {foreach $list_festivals as $item}
			<tr>
				<td>{$item.title}</td>
				<td>
					<a href="{"/admin.php?module=`$config_module`&action=settingsInfo&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
	    {/foreach}
	</tbody>
</table>
