

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
