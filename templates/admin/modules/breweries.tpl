<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="breweries" />
	<input type="hidden" name="action" value="browse" />

	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search}" class="focus" /></td>
			<td>
				<select name="search_festival">
					<option value=""></option>
                    {foreach $list_festivals as $item}
						<option value="{$item.id}"{if $item.id == $search_festival} selected="selected"{/if}>{$item.title}</option>
                    {/foreach}
				</select>
			</td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=breweries&action=info"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td>{"country"|translate}</td>
			<td></td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$list item=item}
			<tr>
				<td>{$item.title}</td>
				<td>{$item.country_title}</td>
				<td>
                    {if !$item.email}<span class="material-icons">unsubscribe</span>{/if}
                    {if !$item.link_social_untappd}<span class="material-icons">wine_bar</span>{/if}
				</td>
				<td>
					<a href="{"/admin.php?module=breweries&action=info&id={$item.id}"|amake_url}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>

