
<div class="form-container form-horizontal">
	<form action="/admin.php" method="post">
		<input type="hidden" name="module" value="{$module_name}">
		<input type="hidden" name="action" value="infoAct">
		<input type="hidden" name="form2[id]" value="{$data.id}" />

		<div class="form-group">
			<label class="form-label" for="f_title">{"title"|translate}</label>
			<div class="form-field">
				<div class="input-with-icon">
					<input class="form-control" type="text" id="f_title" name="form[title]" value="{$data.title}" required>
					<span class="lang-icon"><i>{$language_active}</i></span>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_date">{"date"|translate}</label>
			<input class="form-control date_time_selector" type="text" id="f_date" name="form2[time_starts]" value="{$data.time_starts|my_date_format:"middle"}">
			-
			<input class="form-control date_time_selector" type="text" id="f_date" name="form2[time_ends]" value="{$data.time_ends|my_date_format:"middle"}">
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"active"|translate}</label>
			<div class="form-field">
				<label class="form-checkbox-item">
					<input type="checkbox" name="form2[is_active]"{if !$data || $data.is_active} checked{/if}>
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"user"|translate}</label>
			<input class="form-control" type="email" id="f_title" name="form2[email]" value="{$data.user_email}" placeholder="{"email"|translate}" required>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn-submit">{"submit"|translate}</button>
            {if $data}<a href="?module={$module_name}&action=view&id={$data.id}">{"view"|translate}</a>{/if}
		</div>

	</form>
</div>
