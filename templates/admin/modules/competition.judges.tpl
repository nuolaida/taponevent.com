<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="judges" />

	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search}" class="focus" /></td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=`$config_module`&action=judgesInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"name"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
	    {foreach from=$list item=item}
			<tr>
				<td>
                    {$item.name}
                    {if $item.description}<div class="small">{$item.description}</div>{/if}
				</td>
				<td>
					<a href="{"/admin.php?module=`$config_module`&action=judgesInfo&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
	    {/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>
