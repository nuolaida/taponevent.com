
<div class="form-container form-horizontal">
	<form action="/admin.php" method="post">
		<input type="hidden" name="module" value="{$module_name}">
		<input type="hidden" name="action" value="companiesInfoAct">
		<input type="hidden" name="form2[id]" value="{$data.id}" />
		<input type="hidden" name="form2[festival_id]" value="{$data_festival.id}" />

		<div class="form-group">
			<label class="form-label" for="f_title">{"title"|translate}</label>
			<input class="form-control" type="text" id="f_title" name="form[title]" value="{$data.title}" required>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_app_type">{"date"|translate}</label>
			<select class="form-control" id="f_app_type" name="form[app_type]">
				<option value="seller"{if !$data || $data['app_type'] == 'seller'} selected{/if}>{"app type seller"|translate}</option>
				<option value="cashmachine"{if $data['app_type'] == 'cashmachine'} selected{/if}>{"app type cashmachine"|translate}</option>
			</select>
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
			{if $data}<a href="?module={$module_name}&action=companiesView&id={$data.id}">{"view"|translate}</a>{/if}
		</div>

	</form>
</div>
