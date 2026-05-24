

<h1>
	{$data_companies.title}
	<a href="?module={$module_name}&action=companiesInfo&id={$data_companies.id}"><i class="material-icons">edit</i></a>
</h1>


<br /><br /><br />
<h3>{"prices"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="?module={$module_name}&action=pricesInfo&company_id={$data_companies.id}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"price"|translate}</td>
			<td>{"title"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
	    {foreach $list_prices as $item}
			<tr{if !$item.is_active} class="inactive"{/if}>
				<td>{$item.price}</td>
				<td>{$item.title}</td>
				<td><a href="?module={$module_name}&action=pricesInfo&id={$item.id}"><i class="material-icons">edit</i></a></td>
			</tr>
	    {/foreach}
	</tbody>
</table>

<br /><br /><br />
<h3>{"users"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="?module={$module_name}&action=usersInfo&company_id={$data_companies.id}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"name"|translate}</td>
			<td>{"email"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
	    {foreach $list_users as $item}
			<tr{if !$item.is_active} class="inactive"{/if}>
				<td>{$item.user_name}</td>
				<td>{$item.user_email}</td>
				<td><a href="?module={$module_name}&action=usersInfo&id={$item.user_id}&festival_id={$item.festival_id}"><i class="material-icons">edit</i></a></td>
			</tr>
	    {/foreach}
	</tbody>
</table>

<br /><br /><br />
<form action="{"/admin.php"|amake_url}" method="get" class="form-inline" style="margin-bottom: 20px; padding: 15px; border: 1px solid #d9dee7; background: #f8fafc;">
	<input type="hidden" name="module" value="{$module_name}">
	<input type="hidden" name="action" value="companiesView">
	<input type="hidden" name="id" value="{$data_companies.id}">

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
	{"sold products"|translate}
	<a href="?module={$module_name}&action=companiesSalesEmail&id={$data_companies.id}&sales_from={$sales_from|escape:'url'}&sales_till={$sales_till|escape:'url'}" title="{"send sales report"|translate}" onclick="return confirm('{"send sales report"|translate}?');"><i class="material-icons">email</i></a>
</h3>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td>{"quantity"|translate}</td>
			<td>{"incomes"|translate}</td>
		</tr>
	</thead>
	<tbody>
	    {foreach $list_company_products_sales as $item}
			<tr>
				<td>{$item.title}</td>
				<td>{$item.quantity}</td>
				<td>{$item.incomes_total|string_format:"%.2f"}</td>
			</tr>
	    {foreachelse}
			<tr>
				<td colspan="3">{"no product sales for selected period"|translate}</td>
			</tr>
	    {/foreach}
	</tbody>
</table>

{if $list_company_users_sales}
	<br /><br /><br />
	<h3>{"sales by users"|translate}</h3>
	<table class="tbl_list">
		<thead>
			<tr>
				<td>{"name"|translate}</td>
				<td>{"email"|translate}</td>
				<td>{"receipts"|translate}</td>
				<td>{"incomes"|translate}</td>
			</tr>
		</thead>
		<tbody>
		{foreach $list_company_users_sales as $item}
			<tr>
				<td>{$item.user_name}</td>
				<td>{$item.user_email}</td>
				<td>{$item.receipts}</td>
				<td>{$item.incomes_total|string_format:"%.2f"}</td>
			</tr>
		{/foreach}
		</tbody>
	</table>
{/if}
