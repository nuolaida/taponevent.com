<div class="te-pos-wrapper te-error-screen"> <div class="te-result-container">
		<div class="te-error-icon">
			<span class="material-icons" style="font-size: 5rem; color: #e74c3c;">error_outline</span>
		</div>
		<div class="te-paid-amount" style="color: #e74c3c;">
			ERROR
		</div>
		<div class="te-divider"></div>
		<div class="te-error-msg">
            {$error_message}
		</div>
		<div class="te-balance-row">
			<span class="te-balance-label">Balance</span>
			<span class="te-balance-value">{$wallet|string_format:"%.2f"}</span>
		</div>
		<div class="te-actions">
			<a href="?module=festivals&action=work" class="te-back-btn" style="background: #e74c3c;">OK</a>
		</div>
	</div>
</div>

