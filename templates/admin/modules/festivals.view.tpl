

<h1>
	{$data.title}
	<a href="?module={$module_name}&action=info&id={$data.id}"><i class="material-icons">edit</i></a>
</h1>
{if !$data.is_active}
	<div class="color_inactive">{$data.time_starts|my_date_format:"middle"} - {$data.time_ends|my_date_format:"middle"}</div>
{elseif $data.time_starts < $smarty.now && $data.time_ends > $smarty.now}
	<div class="color_ok">{$data.time_starts|my_date_format:"middle"} - {$data.time_ends|my_date_format:"middle"}</div>
{else}
	<div class="">{$data.time_starts|my_date_format:"middle"} - {$data.time_ends|my_date_format:"middle"}</div>
{/if}
<div class=""><i class="material-icons">account_circle</i> {$data.user_email}</div>

<br /><br /><br />
<h3>{"companies"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="?module={$module_name}&action=companiesInfo&festival_id={$data.id}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td>{"users"|translate}</td>
			<td>{"incomes"|translate}</td>
		</tr>
	</thead>
	<tbody>
	    {foreach $list_companies as $item}
			<tr{if !$item.is_active} class="inactive"{/if}>
				<td><a href="?module={$module_name}&action=companiesView&id={$item.id}">{$item.title}</a></td>
				<td>{$item.users_total}</td>
				<td>{$item.incomes_total}</td>
			</tr>
	    {/foreach}
	</tbody>
</table>

<br /><br /><br />
<form action="{"/admin.php"|amake_url}" method="get" class="form-inline" style="margin-bottom: 20px; padding: 15px; border: 1px solid #d9dee7; background: #f8fafc;">
	<input type="hidden" name="module" value="{$module_name}">
	<input type="hidden" name="action" value="view">
	<input type="hidden" name="id" value="{$data.id}">

	<label>
		{"from"|translate}
		<input type="date" name="sales_from" value="{$sales_from|escape:'html'}">
	</label>
	<label style="margin-left: 10px;">
		{"till"|translate}
		<input type="date" name="sales_till" value="{$sales_till|escape:'html'}">
	</label>
	<button type="submit" class="btn-submit" style="margin-left: 10px;">{"show"|translate}</button>
</form>

<h3>
	{"sales by sellers"|translate}
	<a href="?module={$module_name}&action=companiesSalesExport&id={$data.id}&sales_from={$sales_from|escape:'url'}&sales_till={$sales_till|escape:'url'}" title="{"export sales by sellers"|translate}"><i class="material-icons">table_chart</i></a>
</h3>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"title"|translate}</td>
		<td>{"receipts"|translate}</td>
		<td>{"quantity"|translate}</td>
		<td>{"incomes"|translate}</td>
	</tr>
	</thead>
	<tbody>
	{foreach $list_companies_sales as $item}
		<tr>
			<td><a href="?module={$module_name}&action=companiesView&id={$item.id}">{$item.title}</a></td>
			<td>{$item.receipts}</td>
			<td>{$item.quantity}</td>
			<td>{$item.incomes_total|string_format:"%.2f"}</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="4">{"no sales for selected period"|translate}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<br /><br /><br />
<h3>{"top selling products"|translate}</h3>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"title"|translate}</td>
		<td>{"companies"|translate}</td>
		<td>{"quantity"|translate}</td>
		<td>{"incomes"|translate}</td>
	</tr>
	</thead>
	<tbody>
	{foreach $list_products_sales as $item}
		<tr>
			<td>{$item.title}</td>
			<td><a href="?module={$module_name}&action=companiesView&id={$item.company_id}">{$item.company_title}</a></td>
			<td>{$item.quantity}</td>
			<td>{$item.incomes_total|string_format:"%.2f"}</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="4">{"no product sales for selected period"|translate}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<br /><br /><br />
<h3>{"nfc statistics"|translate}</h3>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"used nfc cards"|translate}</td>
		<td>{"topup amount"|translate}</td>
		<td>{"spent amount"|translate}</td>
		<td>{"unused amount"|translate}</td>
	</tr>
	</thead>
	<tbody>
		<tr>
			<td><a href="?module={$module_name}&action=nfcList&id={$data.id}">{$nfc_stats.nfc_total}</a></td>
			<td>{$nfc_stats.topup_total|string_format:"%.2f"}</td>
			<td>{$nfc_stats.spent_total|string_format:"%.2f"}</td>
			<td>{$nfc_stats.unused_total|string_format:"%.2f"}</td>
		</tr>
	</tbody>
</table>
