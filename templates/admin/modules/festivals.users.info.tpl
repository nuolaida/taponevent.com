
<div class="form-container form-horizontal">
	<form action="/admin.php" method="post">
		<input type="hidden" name="module" value="{$module_name}">
		<input type="hidden" name="action" value="usersInfoAct">
		<input type="hidden" name="form2[id]" value="{$data.user_id}" />
		<input type="hidden" name="form2[company_id]" value="{$data_companies.id}" />

		<div class="form-group">
			<label class="form-label" for="f_title">{"user"|translate}</label>
			<input class="form-control" type="email" id="f_title" name="form2[email]" value="{$data.user_email}" placeholder="{"email"|translate}" required{if $data} readonly{/if}>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"active"|translate}</label>
			<div class="form-field">
				<label class="form-checkbox-item">
					<input type="checkbox" name="form2[is_active]"{if !$data || $data.is_active} checked{/if}>
				</label>
			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn-submit">{"submit"|translate}</button>
		</div>

	</form>
</div>
