
{if $list_items}
	<table class="tbl_list">
		<tbody>
        {foreach $list_items as $key => $item}
			<tr>
				<td>{$item.category}</td>
				<td>{$item.gen_id}</td>
				<td><strong>{$key+1}</strong></td>
				<td>{if $item.sweetness}{"competition sweetness `$item.sweetness+0`"|translate}{/if}</td>
				<td>{if $item.carbonation}{"competition carbonation `$item.carbonation`"|translate}{/if}</td>
				<td>{$item.abv}%</td>
				<td>{$item.ingredients}</td>
				<td>{$item.title}</td>
				<td>{$item.owner_title}</td>
			</tr>
        {/foreach}
		</tbody>
	</table>
	<br /><br />
{/if}


{if $tbl1}
	<table class="tbl_list">
		<thead>
			<tr>
				<td></td>
	            {for $i=0 to 3}
					<td>{"competition sweetness `$i`"|translate}</td>
	            {/for}
			</tr>
		</thead>
		<tbody>
	        {foreach $tbl1 as $key => $item}
				<tr>
					<td>
	                    {$key}
					</td>
					{for $i=0 to 3}
						<td>{if $item[$i]}{$item[$i]}{/if}</td>
                    {/for}
				</tr>
	        {/foreach}
		</tbody>
	</table>
	<br /><br />
{/if}

{if $list_medals}
	<table class="tbl_list">
		{foreach $list_medals as $key => $value}
			<tr>
				<td colspan="3">{$value.title}</td>
			</tr>
			<tr>
                {for $i=1 to 3}
		            <td>
			            <strong>{$value.medals[$i].owner_title}</strong><br />
                        {$value.medals[$i].title}
		            </td>
				{/for}
			</tr>
		{/foreach}
	</table>
{/if}

