<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="beer" />
	<input type="hidden" name="action" value="browse" />

	<table class="tbl_form">
		<tr>
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
<br /><br /><br />


{foreach $list_breweries as $item_brewery}
	<div><strong>{$item_brewery.title}</strong></div>
	{if $list_beer[$item_brewery.id]}
		{foreach $list_beer[$item_brewery.id] as $item_beer}
            {$item_beer.festival_session_number}.
	        {$item_beer.title}
            {if $item_beer.style}/ {$item_beer.style}{/if}
            {if $item_beer.abv}/ {$item_beer.abv}%{/if}
			<br />
			{if $item_beer.description}
				<em>{$item_beer.description}</em><br />
			{/if}
		{/foreach}
    {/if}
	<br /><br />
{/foreach}