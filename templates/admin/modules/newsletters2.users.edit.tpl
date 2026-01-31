
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="usersEditAct" />
	<input type="hidden" name="form2[id]" value="{$data.id}" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>{"email"|translate}</td>
				<td><input type="text" name="form[email]" size="40" value="{$data.email}" /></td>
			</tr>
		</tbody>
	</table>
</form>


{if $data}
	<br /><br />
	<h3>{"subscriptions"|translate}</h3>

	<table class="tbl_list">
		<thead>
		<tr>
			<td>{"title"|translate}</td>
			<td class="actions"></td>
		</tr>
		</thead>
		<tbody>
	        {foreach $list_categories as $item}
				<tr{if $item.is_unsubscribed} class="inactive"{/if}>
					<td>
						{$item.category_title}
                        {if $item.category_key}<div class="small">{$item.category_key}</div>{/if}
					</td>
					<td>
						<a href="{"/admin.php?module={$config_module}&action=usersEditSubscriptionAct&id={$item.user_id}&category={$item.category_id}"|amake_url}"><i class="material-icons">{if $item.is_unsubscribed}check_circle_outline{else}highlight_off{/if}</i></a>
					</td>
				</tr>
	        {/foreach}
		</tbody>
	</table>


	<br /><br />
	<h3>{"sent"|translate}</h3>

	<table class="tbl_list">
		<thead>
		<tr>
			<td>{"date"|translate}</td>
			<td>{"title"|translate}</td>
			<td>{"opened"|translate}</td>
			<td class="actions"></td>
		</tr>
		</thead>
		<tbody>
		{foreach from=$list_sent item=item}
			<tr>
				<td>
					{if $item.sent_time}
						{$item.sent_time|my_date_format:"middle"}
					{else}
						<i class="material-icons">access_time</i>
					{/if}
				</td>
				<td>{$item.email_subject}</td>
				<td>{$item.opened_times}</td>
				<td>
					<a href="{"/admin.php?module={$config_module}&action=info&id={$item.email_id}"|amake_url}"><i class="material-icons">search</i></a>
				</td>
			</tr>
		{/foreach}
		</tbody>
	</table>

	<div class="main_paging">{$paging}</div>


	{actions_log table="newsletters2_users" id=$data.id}
{/if}






