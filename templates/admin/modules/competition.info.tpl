
<form action="{"/admin.php"|amake_url}" method="post">
	<input type="hidden" name="module" value="{$config_module}" />
	<input type="hidden" name="action" value="infoAct" />
	<input type="hidden" name="form2[id]" value="{$data.id}" />
	<table class="tbl_form">
		<tfoot>
			<tr>
				<td></td>
				<td><input type="submit" value="{"submit"|translate}" /></td>
			</tr>
		</tfoot>
		<tbody>
			<tr>
				<td>{"festival"|translate}</td>
				<td>
					{if $data}
						{$data.festival_title}
					{else}
						<select name="form[festival_id]" class="js-example-basic-single" style="width: 100px;">
		                    {foreach $list_festivals as $item}
								<option value="{$item.id}"{if $item.id == $data.festival_id} selected="selected"{/if}>{$item.title}</option>
		                    {/foreach}
						</select>
                    {/if}
				</td>
			</tr>
			<tr>
				<td>{"producer"|translate}</td>
				<td>
					<select name="form[owner_id]" class="js-example-basic-single">
						<option value=""></option>
                        {foreach $list_owners as $item}
							<option value="{$item.id}"{if $data.owner_id == $item.id} selected="selected"{/if}>{$item.title} / {$item.real_name}</option>
                        {/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="70" value="{$data.title}" /></td>
			</tr>
			<tr>
				<td>{"ingredients"|translate}</td>
				<td><input type="text" name="form[ingredients]" size="70" value="{$data.ingredients}" /></td>
			</tr>
			<tr>
				<td>{"category"|translate}</td>
				<td><input type="text" name="form[category]" size="70" value="{$data.category}" /></td>
			</tr>
			<tr>
				<td>{"abv"|translate}</td>
				<td><input type="text" name="form[abv]" size="6" value="{$data.abv}" />%</td>
			</tr>
			<tr>
				<td>{"sweetness"|translate}</td>
				<td>
					<select name="form[sweetness]" class="js-example-basic-single">
						<option value=""></option>
                        {for $i=1 to 3}
							<option value="{$i}"{if $data.sweetness == $i} selected="selected"{/if}>{"competition sweetness `$i`"|translate}</option>
                        {/for}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"carbonation"|translate}</td>
				<td>
					<select name="form[carbonation]" class="js-example-basic-single">
                        {for $i=1 to 3}
							<option value="{$i}"{if $data.carbonation == $i} selected="selected"{/if}>{"competition carbonation `$i`"|translate}</option>
                        {/for}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"medal"|translate}</td>
				<td>
					<select name="form[medal]" class="js-example-basic-single">
                        {for $i=0 to 3}
							<option value="{$i}"{if $data.medal == $i} selected="selected"{/if}>{"competition medal `$i`"|translate}</option>
                        {/for}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"medal category"|translate}</td>
				<td>
					<select name="form[medal_categories_id]" class="js-example-basic-single">
						<option value=""></option>
                        {foreach $list_medal_categories as $item}
							<option value="{$item.id}"{if $data.medal_categories_id == $item.id} selected="selected"{/if}>{$item.medal_title}</option>
                        {/foreach}
					</select>
				</td>
			</tr>
		</tbody>
	</table>
</form>


{if $data}
	<br /><br />
	<h3>{"send labels"|translate}</h3>

	<form action="{"/admin.php"|amake_url}" method="post">
		<input type="hidden" name="module" value="{$config_module}" />
		<input type="hidden" name="action" value="sendLabelAct" />
		<input type="hidden" name="form2[id]" value="{$data.id}" />
		<table class="tbl_form">
			<tbody>
				<tr>
					<td>{"email"|translate}</td>
					<td><input type="text" name="form[email]" size="20" value="{$data_owner.email}" /></td>
					<td><input type="submit" value="{"send"|translate}" /></td>
				</tr>
			</tbody>
		</table>
	</form>
{/if}



{actions_log table="competition_items" id=$data.id}


