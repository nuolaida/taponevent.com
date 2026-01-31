
<div class="form-container form-horizontal">
	<form action="/admin.php" method="post">
		<input type="hidden" name="module" value="{$module_name}">
		<input type="hidden" name="action" value="infoAct">
		<input type="hidden" name="form2[id]" value="{$data.id}" />

		<div class="form-group">
			<label class="form-label" for="f_title">{"email"|translate}</label>
			<input class="form-control" type="email" id="f_title" name="form[email]" value="{$data.email}" size="30" required>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"password"|translate}</label>
			<input class="form-control" type="password" id="f_title" name="form2[password]" value="" size="30">
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"name"|translate}</label>
			<input class="form-control" type="text" id="f_title" name="form[name]" value="{$data.name}" size="30" required>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"administrator"|translate}</label>
			<div class="form-field">
				<label class="form-checkbox-item">
					<input type="checkbox" name="form2[admin]"{if !$data || $data.admin} checked{/if}>
				</label>
			</div>
		</div>

		<div class="form-group">
			<label class="form-label" for="f_title">{"active"|translate}</label>
			<div class="form-field">
				<label class="form-checkbox-item">
					<input type="checkbox" name="form2[is_active]"{if !$data || $data.is_active} checked{/if}>
				</label>
			</div>
		</div>

        {if $data.admin == 1}
	        <h3>{"rights"|translate}</h3>
            {foreach $list_menu as $item_menu}
		        <div class="form-group">
			        <label class="form-label">{$item_menu.name}</label>
			        <div class="form-field">
				        <label class="form-checkbox-item">
					        <input type="checkbox" name="rights[{$item_menu.id}][read]"{if $list_rights[$item_menu.module].read} checked{/if}> <span>{"rights read"|translate}</span>
					        <input type="checkbox" name="rights[{$item_menu.id}][write]"{if $list_rights[$item_menu.module].write} checked{/if}> <span>{"rights write"|translate}</span>
				        </label>
			        </div>
		        </div>
            {/foreach}
        {/if}

		<div class="form-actions">
            {* <a href="?module=festivals" class="btn-link">Atšaukti</a> *}
			<button type="submit" class="btn-submit">{"submit"|translate}</button>
		</div>


        {*
		<div class="form-row">
			<div class="form-group">
				<label class="form-label" for="f_date">Data</label>
				<input class="form-control" type="date" id="f_date" name="date">
			</div>
			<div class="form-group">
				<label class="form-label" for="f_city">Miestas</label>
				<select class="form-control" id="f_city" name="city">
					<option value="1">Vilnius</option>
					<option value="2">Kaunas</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="form-label" for="f_desc">Papildoma informacija</label>
			<textarea class="form-control" id="f_desc" name="description" rows="3"></textarea>
		</div>
		<div class="form-group">
		    <label class="form-label">Paskyros būsena</label>
		    <div class="form-field">
		        <div class="form-switch-wrapper">
		            <label class="form-switch">
		                <input type="checkbox" name="is_active" checked>
		                <span class="form-slider"></span>
		            </label>
		            <span class="form-help-inline">Aktyvus</span>
		        </div>
		    </div>
		</div>
		<div class="form-group">
		    <label class="form-label">Teisės</label>
		    <div class="form-field">
		        <label class="form-checkbox-item">
		            <input type="checkbox" name="p_read" checked> <span>Tik skaityti</span>
		        </label>
		        <label class="form-checkbox-item">
		            <input type="checkbox" name="p_write"> <span>Redaguoti duomenis</span>
		        </label>
		    </div>
		</div>
		*}
	</form>
</div>
