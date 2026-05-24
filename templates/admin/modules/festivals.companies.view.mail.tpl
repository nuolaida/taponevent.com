{"sales report email greeting"|translate}<br /><br />

{"sales report email intro before"|translate} {$data_festivals.title} {"sales report email intro after"|translate}<br /><br />

{"total sales amount"|translate}: <strong>{$total_sales|string_format:"%.2f"}</strong><br />
{foreach $list_company_daily_sales as $item}
	{$item.sales_date} - {$item.incomes_total|string_format:"%.2f"}<br />
{foreachelse}
	{"no sales for selected period"|translate}<br />
{/foreach}
<br />

{"most popular products"|translate}:<br />
<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
	<thead>
		<tr>
			<th align="left">{"title"|translate}</th>
			<th align="right">{"quantity"|translate}</th>
			<th align="right">{"incomes"|translate}</th>
		</tr>
	</thead>
	<tbody>
	{foreach $list_company_products_sales as $item}
		<tr>
			<td>{$item.title}</td>
			<td align="right">{$item.quantity}</td>
			<td align="right">{$item.incomes_total|string_format:"%.2f"}</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="3">{"no product sales for selected period"|translate}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

{if $list_company_users_sales}
	<br /><br />
	{"sales by users"|translate}:<br />
	<table border="1" cellpadding="6" cellspacing="0" style="border-collapse: collapse;">
		<thead>
			<tr>
				<th align="left">{"name"|translate}</th>
				<th align="left">{"email"|translate}</th>
				<th align="right">{"receipts"|translate}</th>
				<th align="right">{"incomes"|translate}</th>
			</tr>
		</thead>
		<tbody>
		{foreach $list_company_users_sales as $item}
			<tr>
				<td>{$item.user_name}</td>
				<td>{$item.user_email}</td>
				<td align="right">{$item.receipts}</td>
				<td align="right">{$item.incomes_total|string_format:"%.2f"}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}
