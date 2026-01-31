<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$(".photo_select").click(function() {
			this_this = this;
			window.opener.function_gallery3_id('{$srch.rel}', "{$srch.type}", $(this_this).attr("rel"));
			window.close();
			return false;
		});
	});
</script>


<form action="/admin.php" method="get">
	<input type="hidden" name="module" value="pictures" />
	<input type="hidden" name="action" value="browse" />
	<input type="hidden" name="popup" value="{$popup}" />
	<input type="hidden" name="type" value="{$srch.type}" />
	<input type="hidden" name="rel" value="{$srch.rel}" />

	<table class="tbl_form">
		<tr>
			<td><input type="search" name="search" size="30" value="{$search}" class="focus" /></td>
			<td><input type="submit" value="{"search"|translate}" /></td>
		</tr>
	</table>
</form>

<ul class="main_page_menu">
	<li><a href="/admin.php?module=pictures&action=info&popup=1" title="{"add"|translate}"><i class="material-icons">add</i></a></li>
</ul>


<div class="page_pictures">
	<div class="page_pictures_list">
		{foreach from=$list item=item}
			<div class="item">
				<div class="image"><a class="lightbox" href="{"/index.php?module=images&action=show&id=`$item.id`&type=any1000x1000"|make_url}" title=""><img src="{"/index.php?module=images&action=show&id=`$item.id`&type=any200x200"|make_url}" border="0" /></a></div>
				<div class="button"><a href="" rel="{$item.id}" class="photo_select">{"select"|translate}</a></div>
			</div>
		{/foreach}
		<div class="main_clear"></div>
	</div>
</div>



<div class="main_paging">{$paging}</div>

