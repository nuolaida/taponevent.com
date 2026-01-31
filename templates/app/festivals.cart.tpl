<div class="login-box">
	<ul class="module-list">
	{if $list_prices}
		{foreach $list_prices as $item}
			<li><a href="?module=festivals&action=cart&id={$item.id}">
					{$item.title}
					{if $item.title}
						<span class="small">({$item.price})</span>
					{else}
						{$item.price}
					{/if}
				</a>
			</li>
		{/foreach}
    {/if}
		<li>
			<br>
			<form action="/app.php" method="get">
				<input type="hidden" name="module" value="festivals">
				<input type="hidden" name="action" value="cart">
				<input type="text" name="price" value="" id="price-input" inputmode="decimal" placeholder="0.00">
				<button type="submit" id="pay-button">{"pay"|translate}</button>
			</form>
		</li>
	</ul>
</div>