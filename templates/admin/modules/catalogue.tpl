<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="catalogue_festivals" />
	<input type="hidden" name="action" value="browse" />

	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search}" class="focus" /></td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=`$config_module`&action=info"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td>{"type"|translate}</td>
			<td>{"country"|translate}</td>
			<td></td>
			<td></td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$list item=item}
			<tr>
				<td>{$item.title}</td>
				<td>{if $item.drink_type}{$list_drink_types[$item.drink_type]}{/if}</td>
				<td>{$item.country_title}</td>
				<td>
                    {if !$item.event_finished}
	                    {$item.last_date_start|my_date_format:"short"}
	                    {if $item.last_date_end}
                            / {$item.last_date_end|my_date_format:"short"}
	                    {/if}
                    {/if}
				</td>
				<td>
                    {if $item.link_social_website}<a href="{$item.link_social_website}" target="_blank"><span class="material-symbols-outlined">language</span></a>{/if}
                    {if $item.link_social_facebook}<a href="{$item.link_social_facebook}" target="_blank"><span class="material-symbols-outlined">person</span></a>{/if}
				</td>
				<td>
					<a href="{"/admin.php?module=`$config_module`&action=info&id={$item.id}"|amake_url}" title="{"edit"|translate}"><i class="material-icons">edit</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>

