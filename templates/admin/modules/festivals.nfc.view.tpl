<h1>
	{"nfc log"|translate}: {$nfc_id}
</h1>

<table class="tbl_list">
	<thead>
	<tr>
		<td>{"time"|translate}</td>
		<td>{"amount"|translate}</td>
		<td>{"title"|translate}</td>
		<td>{"user"|translate}</td>
	</tr>
	</thead>
	<tbody>
	{foreach $list as $item}
		<tr>
			<td>{$item.rec_time|my_date_format:"middle"}</td>
			<td>{if $item.price > 0}+{$item.price|string_format:"%.2f"}{else}{$item.price|string_format:"%.2f"}{/if}</td>
			<td>{if $item.price > 0}{"topup"|translate}{elseif $item.price_title}{$item.price_title}{else}{"expense"|translate}{/if}</td>
			<td>
				{if $item.company_title}
					{$item.company_title} ({if $item.user_name}{$item.user_name}{else}{$item.user_email}{/if})
				{elseif $item.user_name}
					{$item.user_name}
				{else}
					{$item.user_email}
				{/if}
			</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="4">{"no nfc records"|translate}</td>
		</tr>
	{/foreach}
	</tbody>
</table>
