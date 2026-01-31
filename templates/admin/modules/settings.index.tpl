<br /><br />
<h3>{"index top images"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=indexTopImagesInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
		<tr>
			<td>{"image"|translate}</td>
			<td class="actions"></td>
		</tr>
	</thead>
	<tbody>
		{foreach from=$list_index.topimages key=key item=item}
			<tr>
				<td><a class="lightbox" href="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=any1000x1000"|make_url}" title=""><img src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=any200x200"|make_url}" border="0" /></a></td>
				<td>
					<a href="{"/admin.php?module=settings&action=indexTopImagesInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
					<a href="{"/admin.php?module=settings&action=indexDeleteAct&id={$key}"|amake_url}" class="delete"><i class="material-icons">delete</i></a>
				</td>
			</tr>
		{/foreach}
	</tbody>
</table>



<br /><br /><br /><br />
<h3>{"meet up in vaf"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=indexMeetUpInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"title"|translate}</td>
		<td class="actions"></td>
	</tr>
	</thead>
	<tbody>
	{foreach from=$list_index.meetup key=key item=item}
		<tr>
			<td>{$item.varchar_title}</td>
			<td>
				<a href="{"/admin.php?module=settings&action=indexMeetUpInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
				<a href="{"/admin.php?module=settings&action=indexDeleteAct&id={$key}"|amake_url}" class="delete"><i class="material-icons">delete</i></a>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>



<br /><br /><br /><br />
<h3>{"about"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=indexAboutInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"title"|translate}</td>
		<td class="actions"></td>
	</tr>
	</thead>
	<tbody>
	{foreach from=$list_index.about key=key item=item}
		<tr>
			<td>{$item.varchar_title}</td>
			<td>
				<a href="{"/admin.php?module=settings&action=indexAboutInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
				<a href="{"/admin.php?module=settings&action=indexDeleteAct&id={$key}"|amake_url}" class="delete"><i class="material-icons">delete</i></a>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>



<br /><br /><br /><br />
<h3>{"tickets"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=indexTicketsInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"price"|translate}</td>
		<td>{"title"|translate}</td>
		<td class="actions"></td>
	</tr>
	</thead>
	<tbody>
	{foreach from=$list_index.tickets key=key item=item}
		<tr>
			<td class="number">{$item.price}€</td>
			<td>{$item.varchar_title}</td>
			<td>
				<a href="{"/admin.php?module=settings&action=indexTicketsInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
				<a href="{"/admin.php?module=settings&action=indexDeleteAct&id={$key}"|amake_url}" class="delete"><i class="material-icons">delete</i></a>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>



<br /><br /><br /><br />
<h3>{"sponsors"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=indexSponsorsInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"image"|translate}</td>
		<td class="actions"></td>
	</tr>
	</thead>
	<tbody>
	{foreach from=$list_index.sponsors key=key item=item}
		<tr>
			<td><a class="lightbox" href="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=any1000x1000"|make_url}" title=""><img src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=any200x200"|make_url}" border="0" /></a></td>
			<td>
				<a href="{"/admin.php?module=settings&action=indexSponsorsInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
				<a href="{"/admin.php?module=settings&action=indexDeleteAct&id={$key}"|amake_url}" class="delete"><i class="material-icons">delete</i></a>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>



<br /><br /><br /><br />
<h3>{"place"|translate}</h3>
<ul class="main_page_menu">
	<li><a href="{"/admin.php?module=settings&action=indexPlaceInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>
<table class="tbl_list">
	<thead>
	<tr>
		<td>{"title"|translate}</td>
		<td>{"description"|translate}</td>
		<td class="actions"></td>
	</tr>
	</thead>
	<tbody>
	{foreach from=$list_index.place key=key item=item}
		<tr>
			<td>{$item.varchar_title}</td>
			<td>{$item.varchar_description}</td>
			<td>
				<a href="{"/admin.php?module=settings&action=indexPlaceInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
				<a href="{"/admin.php?module=settings&action=indexDeleteAct&id={$key}"|amake_url}" class="delete"><i class="material-icons">delete</i></a>
			</td>
		</tr>
	{/foreach}
	</tbody>
</table>





<br /><br /><br /><br />
<h3>{"map"|translate}</h3>
{if !$list_index.map}
	<ul class="main_page_menu">
		<li><a href="{"/admin.php?module=settings&action=indexMapInfo"|amake_url}" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
	</ul>
{/if}
<table class="tbl_list">
	<thead>
		<tr>
			<th>{"latitude"|translate}</th>
			<th>{"longitude"|translate}</th>
			<th>{"address"|translate}</th>
			<th class="actions"></th>
		</tr>
	</thead>
	<tbody>
    {foreach from=$list_index.map key=key item=item}
		<tr>
			<td>{$item.latitude}</td>
			<td>{$item.longitude}</td>
			<td>{$item.varchar_address}</td>
			<td>
				<a href="{"/admin.php?module=settings&action=indexMapInfo&id={$key}"|amake_url}"><i class="material-icons">edit</i></a>
			</td>
		</tr>
    {/foreach}
	</tbody>
</table>


<br /><br /><br /><br />



