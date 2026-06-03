<h1>
	{"nfc log"|translate}: {$nfc_id}
</h1>

<div style="margin: 15px 0; padding: 15px; border: 1px solid #d9dee7; background: #f8fafc;">
	<div style="font-size: 18px; margin-bottom: 12px;">
		{"current balance"|translate}: <strong>{$nfc_balance|string_format:"%.2f"}</strong>
	</div>

	<form action="{"/admin.php"|amake_url}" method="post" class="form-inline">
		<input type="hidden" name="module" value="{$module_name}">
		<input type="hidden" name="action" value="nfcAdjustAct">
		<input type="hidden" name="id" value="{$data.id}">
		<input type="hidden" name="nfc_id" value="{$nfc_id|escape:'html'}">

		<label>
			{"amount"|translate}
			<input type="number" step="0.01" name="amount" value="" placeholder="10.00 / -10.00" style="width: 140px;">
		</label>
		<button type="submit" class="btn-submit" style="margin-left: 10px;">{"submit"|translate}</button>
	</form>
</div>

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
