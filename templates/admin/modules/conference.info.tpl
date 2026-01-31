
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
					<select name="form[festival_id]" class="js-example-basic-single" style="width: 100px;">
	                    {foreach $list_festivals as $item}
							<option value="{$item.id}"{if $item.id == $data.festival_id} selected="selected"{/if}>{$item.title}</option>
	                    {/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"time"|translate}</td>
				<td><input type="text" name="form[rec_time]" size="16" value="{$data.rec_time|my_date_format:"middle"}" class="date_time_selector"></td>
			</tr>
			<tr>
				<td>{"title"|translate}</td>
				<td><input type="text" name="form[title]" size="70" value="{$data.conference_title}"></td>
			</tr>
			<tr>
				<td>{"speaker"|translate}</td>
				<td>
					<select name="form[speaker_id]" class="js-example-basic-single">
						<option value=""></option>
                        {foreach $list_speakers as $item}
							<option value="{$item.id}"{if $data.speaker_id == $item.id} selected="selected"{/if}>{$item.name}</option>
                        {/foreach}
					</select>
				</td>
			</tr>
			<tr>
				<td>{"language"|translate}</td>
				<td><input type="text" name="form[language]" size="40" value="{$data.conference_language}"></td>
			</tr>
		</tbody>
	</table>
</form>


{actions_log table="conference_list" id=$data.id}


