
<div class="form-container form-horizontal">
	<form action="/admin.php" method="post">
		<input type="hidden" name="module" value="{$module_name}">
		<input type="hidden" name="action" value="pricesInfoAct">
		<input type="hidden" name="form2[id]" value="{$data.id}" />
		{if !$data && $data_companies}<input type="hidden" name="form[company_id]" value="{$data_companies.id}" />{/if}

		<div class="form-group">
			<label class="form-label" for="f_title">{"title"|translate}</label>
			<input class="form-control" type="text" id="f_title" name="form[title]" value="{$data.title}" required>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_price">{"price"|translate}</label>
			<input class="form-control" type="text" id="f_price" name="form[price]" value="{$data.price}" required>
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
