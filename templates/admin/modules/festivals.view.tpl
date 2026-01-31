

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
				<td><a href="?module={$module_name}&action=companiesCheckout&company_id={$item.id}">{$item.incomes_total}</a></td>
			</tr>
	    {/foreach}
	</tbody>
</table>
