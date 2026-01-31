<div class="te-pos-wrapper">
	<div class="te-container">
		<h3 class="te-page-title">Iš viso</h3>
		<div class="te-history-list">
			<div class="te-history-card">
				<div class="te-card-total">
					<span class="te-card-amount">{$incomes_company|abs|string_format:"%.2f"}</span>
					{if $incomes_company != $incomes_user}
						<span class="te-card-part">tavo dalis {$incomes_user|abs|string_format:"%.2f"}</span>
                    {/if}
				</div>
			</div>
		</div>

		<h3 class="te-page-title">Paskutinės operacijos</h3>
		<div class="te-history-list">
            {foreach $list as $item}
				<div class="te-history-card">
					<div class="te-card-top">
						<span class="te-time-ago">{$item.rec_time|my_date_format:"before24"}</span>
					</div>
					<div class="te-card-main">
						<span class="te-card-amount">{$item.price|abs|string_format:"%.2f"}</span>
						<span class="te-card-title">{$item.price_title}</span>
					</div>
				</div>
            {/foreach}
		</div>
	</div>
</div>