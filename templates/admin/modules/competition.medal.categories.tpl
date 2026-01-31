<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="medalCat" />

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
	<li><a href="{"/admin.php?module=`$config_module`&action=medalCatInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
	    {foreach from=$list item=item}
			<tr>
				<td>
                    {$item.medal_title}
				</td>
				<td>
					<a href="{"/admin.php?module=`$config_module`&action=medalCatInfo&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
	    {/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>
