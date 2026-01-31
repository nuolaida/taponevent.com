<!DOCTYPE HTML>
<html lang="en">
<head>
	{if $conf_google_analytics_id}
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id={$conf_google_analytics_id}"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){ dataLayer.push(arguments); }
			gtag('js', new Date());

			gtag('config', '{$conf_google_analytics_id}');
		</script>
    {/if}
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<title></title>

	{foreach $page_styles as $item}
		<link href="{$item}" rel="stylesheet" type="text/css"/>
	{/foreach}

	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script>
        if (typeof jQuery == 'undefined') {
            document.write('<script src="/includes/jquery-3.7.1.min.js"><\/script>');
        }
	</script>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<script language="javascript" type="text/javascript">
        $(document).ready(function() {
            const $input = $('#price-input');
            const $button = $('#pay-button');

            $button.prop('disabled', true);

            $input.on('input', function() {
                let val = $(this).val().replace(',', '.');
                $(this).val(val);
                const isValid = val !== '' && !isNaN(val) && parseFloat(val) > 0;
                $button.prop('disabled', !isValid);
            });
        });

        $(function() {
            $('#menu-icon').click(function(e){
                e.stopPropagation();
                $('#menu-dropdown').toggle();
            });

            $(document).click(function(e){
                if(!$(e.target).closest('.menu').length){
                    $('#menu-dropdown').hide();
                }
            });
        });
	</script>
</head>
<body>
	<div class="center-wrapper">
		{if $page_messages}
			<div class="messages messages-error">
				<ul>
		            {foreach $page_messages as $item}
						<li>{$item}</li>
		            {/foreach}
				</ul>
			</div>
		{/if}

		<div class="page-center">
	        {$module_html}
		</div>
	</div>

	<div class="footer-bar">
		<div></div>
		<div class="logo">
			<a href="/app.php"></a>
		</div>
		<div class="menu">
			<span class="material-icons" id="menu-icon">menu</span>
			<ul class="menu-dropdown" id="menu-dropdown">
				<li><a href="?module=festivals&action=work">{"cash machine"|translate}</a></li>
				<li><a href="?module=festivals&action=checkoutList">{"checkout list"|translate}</a></li>
				<li><a href="?module=festivals&action=select">{"festivals"|translate}</a></li>
				{if $user_info}
					<li><a href="?module=users&action=logoutAct">{"logout"|translate}</a></li>
				{else}
					<li><a href="?module=users&action=login">{"login"|translate}</a></li>
				{/if}
			</ul>
		</div>
	</div>

</body>
</html>
