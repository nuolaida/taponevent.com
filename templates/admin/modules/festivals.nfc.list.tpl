<h1>
	{"nfc list"|translate}
</h1>

<form action="{"/admin.php"|amake_url}" method="get">
	<input type="hidden" name="module" value="{$module_name}" />
	<input type="hidden" name="action" value="nfcList" />
	<input type="hidden" name="id" value="{$data.id}" />

	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search|escape:'html'}" class="focus" placeholder="{"nfc number"|translate}" /></td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<table class="tbl_list">
	<thead>
	<tr>
		<td>{"nfc number"|translate}</td>
		<td>{"topup amount"|translate}</td>
		<td>{"spent amount"|translate}</td>
	</tr>
	</thead>
	<tbody>
	{foreach $list as $item}
		<tr>
			<td><a href="?module={$module_name}&action=nfcView&id={$data.id}&nfc_id={$item.nfc_id|escape:'url'}">{$item.nfc_id}</a></td>
			<td>{$item.topup_total|string_format:"%.2f"}</td>
			<td>{$item.spent_total|string_format:"%.2f"}</td>
		</tr>
	{foreachelse}
		<tr>
			<td colspan="3">{"no nfc records"|translate}</td>
		</tr>
	{/foreach}
	</tbody>
</table>

<div class="main_paging">{$paging}</div>
