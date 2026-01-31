<div class="te-pos-wrapper te-success-screen">
	<div class="te-result-container">
		<div class="te-paid-amount">
            {$topup|string_format:"%.2f"}
		</div>

		<div class="te-divider"></div>

		<div class="te-balance-row">
			<span class="te-balance-label">Balance</span>
			<span class="te-balance-value">{$wallet|string_format:"%.2f"}</span>
		</div>

		<div class="te-actions">
			<a href="?module=festivals&action=work" class="te-back-btn">OK</a>
		</div>
	</div>
</div>