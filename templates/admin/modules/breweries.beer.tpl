<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="breweries" />
	<input type="hidden" name="action" value="beer" />

	<table class="tbl_form">
		<tr>
			<td>
				<select name="id">
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


<table class="tbl_list">
    {foreach $list_breweries as $item_brewery}
	<tr>
		<td>{$item_brewery.title}</td>
		<td>
            {if $list_beer[$item_brewery.id]}
                {foreach $list_beer[$item_brewery.id] as $item_beer}
                    {$item_beer.festival_session_number}.
					<strong>{$item_beer.title}</strong>
                    {if $item_beer.style}/ {$item_beer.style}{/if}
                    {if $item_beer.abv}/ {$item_beer.abv}%{/if}
                    <br />
                {/foreach}
            {/if}
		</td>
	</tr>
    {/foreach}
</table>



