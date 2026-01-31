<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
	<title>
		{if $title}
			{foreach $title as $item}
                {if is_array($item)}
					{$item.title} /
				{else}
                    {$item} /
				{/if}
			{/foreach}
		{/if}
		{"administration"|translate} /
		{$site_header_name}
	</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

    {$layout_favicon}

	<link href="//fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
	<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
	{foreach $site_header_theme_add as $item}
		<link rel="stylesheet" href="{$item}" type="text/css">
	{/foreach}
	<script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
	<link href="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet" type="text/css"/>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" language="javascript" type="text/javascript"></script>
	<script language="javascript" type="text/javascript" src="/includes/jquery_lightbox/jquery.lightbox.js"></script>
	<link rel="stylesheet" href="/includes/jquery_lightbox/css/lightbox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
	<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
	<script src="https://npmcdn.com/flatpickr/dist/l10n/lt.js"></script>
	<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
	<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
	<script type='text/javascript'>
		document.www_url = '{$conf_site_protocol}://{$conf_site_domain}';
		document.templades_path = '{$conf_site_templates}';
	</script>
	<script src="/includes/Trumbowyg/dist/trumbowyg.min.js"></script>
	<link rel="stylesheet" href="/includes/Trumbowyg/dist/ui/trumbowyg.min.css">
	<script src="/includes/Trumbowyg/dist/plugins/cleanpaste/trumbowyg.cleanpaste.min.js"></script>

	<script language="javascript" type="text/javascript">

		function ConvertToClearValue(val) {
			var test = val.replace(/[[]/g,'\\[');
			return test.replace(/]/g,'\\]');
		}


		$(document).ready(function(){

            $('textarea.wysiwyg').trumbowyg();

			// Decorate list tables
			decorate_tables();

			// Approve for delete icon
			$(".delete, .delete").click(function() {
				return confirm('{"delete approve"|translate}');
			});
			$(".delete_ajax").click(function() {
				if (confirm('{"delete approve"|translate}')) {
					var this_link = $(this);
					$.get(this_link.attr('href'), function(data){
						this_link.parent().hide();
						//alert(data);
					});
				}
				return false;
			});


			$("input:checkbox.delete").click(function() {
				if ($(this).is(':checked')) {
					return confirm('{"delete approve"|translate}');
				}
			});

			// Select image functions
			function_gallery3_update_selectable_images();

			$(".lightbox").lightbox();

			// select2
			$('.js-example-basic-single').select2();

			// Calendar
			$(".date_time_selector").flatpickr({ enableTime: true, dateFormat: "Y-m-d H:i", time_24hr: true, allowInput: true });
			$(".date_selector").flatpickr({ enableTime: false, dateFormat: "Y-m-d", time_24hr: true, allowInput: true });
			$(".time_selector").flatpickr({ enableTime: true, noCalendar: true, dateFormat: "H:i", time_24hr: true, allowInput: true });

			var inputval = $('input.focus').val();
			$('input.focus').focus().val('').val(inputval);

			$('select.focus').focus();
		});


		// ajax loading image
		$(document).ajaxStart(function(){
			$(".main_ajax_loading").css("display", "block");
		});

		$(document).ajaxComplete(function(){
			$(".main_ajax_loading").css("display", "none");
		});


		// Select buttons actions
		$(document).on('click', ".gallery3_select .form_gallery3_select", function() {
			popup_window($(this).parents('.gallery3_select').find('[name=form_gallery3_select_popup_link]').val());
		});
		// Deselect button
		$(document).on('click', ".gallery3_select .form_gallery3_deselect", function() {
			function_gallery3_id($(this).parents('.gallery3_select').attr('rel'), $(this).parents('.gallery3_select').find(".form_gallery3_type").val(), 0);
		});
		// Select image functions
		function function_gallery3_update_selectable_images() {
			$(".gallery3_select").each(function(index) {
				if (!$(this).hasClass('gallery3_celect_block_rel') && $(this).is(":visible")) {
					block_this = $(this);

					// unique block rel
					block_rel_number = 0;
					$(".gallery3_celect_block_rel").each(function() {
						patt=/\d+$/g;
						block_rel_number_new = parseInt(patt.exec($(this).attr('rel')));
						if (block_rel_number <= block_rel_number_new) {
							block_rel_number = block_rel_number_new + 1;
						}
					});

					block_this.addClass('gallery3_celect_block_rel');
					gallery3_celect_block_rel = 'gallery3_celect_block_' + block_rel_number;
					block_this.attr('rel', gallery3_celect_block_rel);
					form_gallery3_type = ($('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_type").val()) ? $('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_type").val() : 'any200x200';
					function_gallery3_area(gallery3_celect_block_rel, form_gallery3_type);
					// select button link
					galery3_open_window_url = '/admin.php?module=pictures&action=browse&popup=1&type=' + form_gallery3_type + '&rel=' + gallery3_celect_block_rel;
					if ($('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_name").val()) {
						galery3_open_window_url = galery3_open_window_url + '&kw=' + $('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_name").val();
					}
					$('[rel='+gallery3_celect_block_rel+']').append('<input type="hidden" name="form_gallery3_select_popup_link" value="' + galery3_open_window_url + '" />');
					// search keyword
					if ($('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_name_field").val()) {
						if ($('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_name")) {
							form_gallery3_name_field = $('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_name_field").val();
							$('[name='+ConvertToClearValue(form_gallery3_name_field)+']').keyup(function() {
								$('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_name").val($(this).val());
							});
						}
					};
				}
			});
		}
		// Select image functions
		function function_gallery3_area(gallery3_celect_block_rel, form_gallery3_type) {
			form_gallery3_img_code = '{"/index.php?module=images&action=show&id=00000&type=0type"|make_url}';
			if ($('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_id").val()) {
				form_gallery3_area = '<a class="lightbox" href="' + form_gallery3_img_code.replace('00000', $('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_id").val()).replace('0type', 'any1000x1000') + '" target="_blank"><img src="' + form_gallery3_img_code.replace('00000', $('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_id").val()).replace('0type', form_gallery3_type) + '" alt="" border="0" /></a>';
				$('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_area").html(form_gallery3_area);
			} else {
				$('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_area").html('');
			}
		}
		function function_gallery3_id(gallery3_celect_block_rel, form_gallery3_type, image_id) {
			$('[rel='+gallery3_celect_block_rel+']').find(".form_gallery3_id").val(image_id);
			function_gallery3_area(gallery3_celect_block_rel, form_gallery3_type);
		}

		// Decorates tables
		function decorate_tables() {
			if ($("table.tbl_list tbody tr").hasClass("row1")) {
				$("table.tbl_list tbody tr").removeClass("row1");
			}
			if ($("table.tbl_list tbody tr").hasClass("row2")) {
				$("table.tbl_list tbody tr").removeClass("row2");
			}
			$("table.tbl_list tbody tr, table.tbl_form tbody tr").addClass("row1");
			$("table.tbl_list tbody tr:even, table.tbl_form tbody tr:even").addClass("row2");
			$("table.tbl_list tbody tr, table.tbl_form tbody tr, table.results_statistics tbody tr").hover(
				function () {
					$(this).addClass("hover");
				},
				function () {
					$(this).removeClass("hover");
				}
			);
		}



		// Relations
		function RelationsAddToList(id, name, relations_select_name=false) {
			if (relations_select_name) {
				relations_select_name = 'select[name="' + relations_select_name + '"]';
			} else {
				relations_select_name = '.form_relations3_area';
			}
			$(".relations3_select " + relations_select_name).append(
					$('<option>',
						{
							value: id,
							text : name
						}
					)
			);
		}


	</script>
	<script type="text/javascript" src="../templates/admin/scripts.js"></script>

	<script language="javascript">
		function TrintiC(){ if (!confirm('{"delete approve"|translate}')) return false; else return true; }
	</script>


</head>

<body bottommargin="0" leftmargin="0" topmargin="0" rightmargin="0" marginheight="0" marginwidth="0" bgcolor="#fff">
<div class="main_ajax_loading"></div>
{if !$popup}
	<div class="main_container">
		<div class="main_header">
			<div class="main_header_top">
				<div class="logo"><a href="/"></a></div>
				<div class="info"></div>
				<div class="languages">
					<ul>
						<li><a href="{"/admin.php?change_language=lt"|amake_url}" class="lt{if $language_active == 'lt'} active{/if}"></a></li>
						<li><a href="{"/admin.php?change_language=en"|amake_url}" class="en{if $language_active == 'en'} active{/if}"></a></li>
					</ul>
				</div>
				<div class="stuff">
					{$smarty.now|my_date_format:"middle"}
					<br />
					<a href="{"/index.php?module=users&action=logout"|make_url}"><i class="material-icons">account_circle</i></a>
				</div>
			</div>
			<div class="main_header_menu">
				<div class="username">{$admin_username}</div>
				<div class="menu">
					{if $page_submenu}
						<ul>
							{foreach from=$page_submenu item="item"}
								<li>
									<a href="{$item.link}">{$item.name}</a>
									{if $item.subitems}
										<ul>
											{foreach from=$item.subitems item="subitem"}
												<li><a href="{$subitem.link}">{$subitem.name}</a></li>
											{/foreach}
										</ul>
									{/if}
								</li>
							{/foreach}
						</ul>
					{/if}
				</div>
			</div>
		</div>
		
		<div class="main_body">
			<div class="main_body_left">
				<div class="menu">
					<ul>
						{foreach from=$admin_menu item="item"}
							{if $item.link}
								<li><a href="{$item.link}">{$item.name}</a></li>
							{else}
								<li class="spacer"></li>
							{/if}
						{/foreach}
					</ul>
				</div>

				{$smarty.capture.menu}
			</div>
			<div class="main_body_right">
				<nav class="nav_module">
					<ol>
						{foreach $title as $item}
							<li>
                                {if is_array($item)}
									<a href="{$item.link}">{$item.title}</a>
                                {else}
                                    {$item}
                                {/if}
							</li>
                        {/foreach}
					</ol>
				</nav>
{/if}
		
				{if $main_messages}
					<fieldset class="main_messages">
						<legend>{"messages"|translate}</legend>
						<ul>
							{foreach from=$main_messages item=item}
								<li>{$item}</li>
							{/foreach}
						</ul>
					</fieldset>
				{/if}

				{$block_html}
{if !$popup}
			</div>
			<div class="main_clear"></div>
		</div>
		<br /><br /><br /><br />
	</div>
{/if}

	<div class="wait_while_loading"></div>
</body>
</html>
