<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="browse" />

	<table class="tbl_form">
		<tr>
			<td>
				<select name="search_festival">
                    {foreach $list_festivals as $item}
						<option value="{$item.id}"{if $item.id == $search_festival} selected="selected"{/if}>{$item.title}</option>
                    {/foreach}
				</select>
			</td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>
<br /><br /><br />


<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=`$config_module`&action=info"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"id"|translate}</td>
			<td>{"producer"|translate}</td>
			<td>{"title"|translate}</td>
			<td>{"sweetness"|translate}</td>
			<td>{"medal"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
	    {foreach from=$list item=item}
			<tr>
				<td>{$item.gen_id}</td>
				<td>{$item.owner_title}</td>
				<td>{$item.title}</td>
				<td>{if $item.sweetness}{"competition sweetness `$item.sweetness`"|translate}{/if}</td>
				<td>{if $item.medal}<span class="material-symbols-outlined">counter_{$item.medal}</span>{/if}</td>
				<td>
					<a href="{"/admin.php?module=`$config_module`&action=info&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
	    {/foreach}
	</tbody>
</table>

<strong>{"total"|translate}: {$list|count}</strong><br /><br />
<div class="main_paging">{$paging}</div>
