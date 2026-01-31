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
	<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<meta http-equiv="content-language" content="lt"/>
	<meta name="copyright" content="VAF" />
	{if $page_og_site_name}<meta property="og:site_name" content="{$page_og_site_name}"/>{/if}
	{if $page_keywords}<meta name="keywords" content="{$page_keywords}" />{/if}
	{if $page_canonical}<link rel="canonical" href="//{$conf_site_domain}{$page_canonical}" />{/if}
	{if $page_refresh}<meta http-equiv="refresh" content="{$page_refresh.time};url={$page_refresh.url}"/>{/if}
	{if $page_og_image}
		<meta property="og:image" content="{$page_og_image}"/>
	{/if}
	{if $page_description}<meta name="description" content="{$page_description}" />{/if}
	{if $page_og_description}<meta property="og:description" content="{$page_og_description}"/>{/if}
	{if $page_og_title}<meta property="og:title" content="{$page_og_title}"/>{/if}
	{if $page_og_url}<meta property="og:url" content="{$page_og_url}"/>{/if}
	{if $page_facebook_app_id}<meta property="fb:app_id" content="{$page_facebook_app_id}"/>{/if}
	{if $page_og_type}<meta property="og:type" content="{$page_og_type}"/>{/if}
	{if $page_og_lat}<meta property="place:location:latitude" content="{$page_og_lat}"/>{/if}
	{if $page_og_lon}<meta property="place:location:longitude" content="{$page_og_lon}"/>{/if}

	<title>{$page_title}</title>

	<!-- favicon -->
	{$layout_favicon}

	{*<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">*}
	 <link href="/themes/default/css/bootstrap.min.css" rel="stylesheet" type="text/css">
	 <link href="/themes/default/css/jquery-ui.min.css" rel="stylesheet" type="text/css">
	<link href="/themes/default/css/animate.css" rel="stylesheet" type="text/css">
	<link href="/themes/default/css/css-plugin-collections.css" rel="stylesheet"/>
	<!-- CSS | menuzord megamenu skins -->
	<link id="menuzord-menu-skins" href="/themes/default/css/menuzord-skins/menuzord-boxed.css" rel="stylesheet"/>
	<!-- CSS | Main style file -->
	<link href="/themes/default/css/style-main.css" rel="stylesheet" type="text/css">
	<!-- CSS | Theme Color -->
	<link href="/themes/default/css/colors/{$styles_template_color.css_file}" rel="stylesheet" type="text/css">
	<!-- CSS | Preloader Styles -->
	<link href="/themes/default/css/preloader.css" rel="stylesheet" type="text/css">
	<!-- CSS | Custom Margin Padding Collection -->
	<link href="/themes/default/css/custom-bootstrap-margin-padding.css" rel="stylesheet" type="text/css">
	<!-- CSS | Responsive media queries -->
	<link href="/themes/default/css/responsive.css" rel="stylesheet" type="text/css">
	<!-- CSS | For Dark Layout -->
	<link href="/themes/default/css/style-main-dark.css" rel="stylesheet" type="text/css">

	<link href="/themes/default/css/style.css" rel="stylesheet" type="text/css"/>

	<link href="/themes/style.css" rel="stylesheet" type="text/css"/>

	<!-- Revolution Slider 5.x CSS settings -->
	<link  href="/web/js/revolution-slider/css/settings.css" rel="stylesheet" type="text/css"/>
	<link  href="/web/js/revolution-slider/css/layers.css" rel="stylesheet" type="text/css"/>
	<link  href="/web/js/revolution-slider/css/navigation.css" rel="stylesheet" type="text/css"/>

	<!-- external javascripts -->
	<script src="/web/js/jquery-2.2.0.min.js"></script>
	<script src="/web/js/jquery-ui.min.js"></script>
	<script src="/web/js/bootstrap.min.js"></script>
	<!-- JS | jquery plugin collection for this theme -->
	<script src="/web/js/jquery-plugin-collection.js?2"></script>

	<!-- Revolution Slider 5.x SCRIPTS -->
	<script src="/web/js/revolution-slider/js/jquery.themepunch.tools.min.js"></script>
	<script src="/web/js/revolution-slider/js/jquery.themepunch.revolution.min.js"></script>

	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
	<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
	<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->

    {if $site_header_theme_add}
        {foreach $site_header_theme_add as $item}
			<link  href="{$item}" rel="stylesheet" type="text/css"/>
        {/foreach}
    {/if}

	{if $page_add_header}{$page_add_header}{/if}

	{if isset($site_header_html)}
		{foreach item=item from=$site_header_html}
			{$item}
		{/foreach}
	{/if}

	<script>
        (function() {
            // Tik blokuojame smooth scroll peršokimus, bet paliekame listenerius
            const originalScrollIntoView = Element.prototype.scrollIntoView;
            Element.prototype.scrollIntoView = function(arg) {
                // Jei argumentas yra smooth, ignoruojame
                if (arg && typeof arg === 'object' && arg.behavior === 'smooth') {
                    return;
                }
                return originalScrollIntoView.apply(this, arguments);
            };

            const originalScrollTo = window.scrollTo;
            window.scrollTo = function(x, y) {
                // Jei vienas argumentas yra object su behavior: smooth, ignoruojame
                if (typeof x === 'object' && x.behavior === 'smooth') return;
                return originalScrollTo.apply(window, arguments);
            };

            // CSS smooth scroll išjungti
            document.documentElement.style.scrollBehavior = 'auto';
        })();
	</script>

	<link rel="stylesheet" type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.css" />
	<script src="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.1.0/cookieconsent.min.js"></script>
	<script>
		window.addEventListener("load", function(){
			window.cookieconsent.initialise({
				"palette": {
					"popup": {
						"background": "#efefef",
						"text": "#404040"
					},
					"button": {
						"background": "#8ec760",
						"text": "#ffffff"
					}
				},
				"theme": "edgeless",
				"content": {
					"message": "This website uses cookies to ensure you get the best experience on our website. Šiame puslapyje naudojame sausainiukus, tik dėl jūsų patogumo",
					"dismiss": "Ok"
				}
			})});
	</script>
	<style>
        #about > div > div:nth-child(2) > div.col-md-6.mb-sm-30 > ul > li > a {
            color: #6E9C30;
        }
        #about > div > div:nth-child(2) > div.col-md-6.mb-sm-30 > ul > li > a:hover {
            text-decoration: underline;
        }
	</style>
</head>
<body class="dark {if $display_layout}popup{elseif $display_title_page}title{/if}">
<div id="wrapper">
	<!-- preloader -->
	<div id="preloader">
		<div id="spinner">
			<div class="preloader-dot-loading">
				<div class="cssload-loading"><i></i><i></i><i></i><i></i></div>
			</div>
		</div>
		<div id="disable-preloader" class="btn btn-default btn-sm">{"disable preloader"|translate}</div>
	</div>

	<!-- Header -->
	<header class="header">
		<div class="header-nav navbar-fixed-top navbar-dark navbar-transparent navbar-sticky-animated animated-active">
			<div class="header-nav-wrapper">
				<div class="container">
					<nav id="menuzord" class="menuzord {$styles_template_color.menu_color}">
						<a class="menuzord-brand pull-left flip" href="javascript:void(0)">{* <img src="{"/index.php?module=images&action=show&id=9&type=any200x200"|make_url}" alt=""> *}</a>

						{$layout_main_menu}

						<ul class="social-icons icon-sm mt-20 m-0 sm-text-center text-right">
							{if $layout_list_social_links.facebook.link}<li><a href="{$layout_list_social_links.facebook.link}" target="_blank"><i class="fa fa-facebook text-white"></i></a></li>{/if}
							{if $layout_list_social_links.instagram.link}<li><a href="{$layout_list_social_links.instagram.link}" target="_blank"><i class="fa fa-instagram text-white"></i></a></li>{/if}
							{if $layout_list_social_links.twitter.link}<li><a href="{$layout_list_social_links.twitter.link}" target="_blank"><i class="fa fa-twitter text-white"></i></a></li>{/if}
							{if $layout_list_social_links.youtube.link}<li><a href="{$layout_list_social_links.youtube.link}" target="_blank"><i class="fa fa-youtube text-white"></i></a></li>{/if}
						</ul>
					</nav>
				</div>
			</div>
		</div>
	</header>

	<div class="main-content">
   		{$index_html}
	</div>

	<section class="divider parallax layer-overlay overlay-deep" data-stellar-background-ratio="0.5" data-bg-img="">
		<div class="container pt-70">
			<div class="section-content">
				<div class="row">
					<div class="col-md-10 col-md-offset-1">
						<div class="row sponsors-style2 pt-30 pb-20">
							<div class="col-xs-6 col-md-3 text-center">
								<a href="https://vafest.lt" target="_blank"><img src="https://alausfestivalis.lt{"/index.php?module=images&action=show&id=398&type=any200x100"|make_url}" alt="" /></a>
							</div>
							<div class="col-xs-6 col-md-3 text-center">
								<a href="https://alausfestivalis.lt" target="_blank"><img src="https://alausfestivalis.lt{"/index.php?module=images&action=show&id=397&type=any200x100"|make_url}" alt="" /></a>
							</div>
							{if $conf_site_domain == 'alausfestivalis.lt' || $conf_site_domain == 'alausfestivalis.lt.localhost'}
								<div class="col-xs-6 col-md-3 text-center">
									<a href="http://zmogsala.lt" target="_blank"><img src="https://vafest.lt{"/index.php?module=images&action=show&id=449&type=any200x100"|make_url}" alt="" /></a>
								</div>
							{else}
								<div class="col-xs-6 col-md-3 text-center">
									<a href="https://midausfestivalis.lt" target="_blank"><img src="https://alausfestivalis.lt{"/index.php?module=images&action=show&id=399&type=any200x100"|make_url}" alt="" /></a>
								</div>
                            {/if}
							<div class="col-xs-6 col-md-3 text-center">
								<a href="https://balticciderfestival.com" target="_blank"><img src="https://alausfestivalis.lt{"/index.php?module=images&action=show&id=435&type=any200x100"|make_url}" alt="" /></a>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>

	<!-- Footer -->
	<footer id="footer" class="footer" data-bg-color="#121212">
		<div class="container pt-60 pb-30">
			<div class="row">
				<div class="col-md-6 col-md-offset-3 text-center">
                    {if $conf_footer_line_image_id}
						<img src="{"/index.php?module=images&action=show&id={$conf_footer_line_image_id}&type=any150x150"|make_url}" alt="" />
					{/if}
					<ul class="social-icons flat medium list-inline mb-40">
						{if $layout_list_social_links.facebook.link}<li><a href="{$layout_list_social_links.facebook.link}" target="_blank"><i class="fa fa-facebook"></i></a></li>{/if}
						{if $layout_list_social_links.instagram.link}<li><a href="{$layout_list_social_links.instagram.link}" target="_blank"><i class="fa fa-instagram"></i></a></li>{/if}
						{if $layout_list_social_links.twitter.link}<li><a href="{$layout_list_social_links.twitter.link}" target="_blank"><i class="fa fa-twitter"></i></a></li>{/if}
						{if $layout_list_social_links.youtube.link}<li><a href="{$layout_list_social_links.youtube.link}" target="_blank"><i class="fa fa-youtube"></i></a></li>{/if}
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<a class="scrollToTop" href="#"><i class="fa fa-angle-up"></i></a>
</div>
<!-- end wrapper -->

<!-- Footer Scripts -->
<script>
	jQuery(document).ready(function(){

		//jQuery for page scrolling feature - requires jQuery Easing plugin
		$('.demo-smooth-scroll').on('click', function(event) {
			var $anchor = $(this);
			$('html, body').stop().animate({
				scrollTop: $($anchor.attr('href')).offset().top
			}, 1500, 'easeInOutExpo');
			event.preventDefault();
		});

	});
</script>


{*<!-- JS | Custom script for all pages -->*}
<script src="/web/js/custom.js"></script>
<!-- SLIDER REVOLUTION 5.0 EXTENSIONS
      (Load Extensions only on Local File Systems !
       The following part can be removed on Server for On Demand Loading) -->
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.actions.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.carousel.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.kenburn.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.layeranimation.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.migration.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.navigation.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.parallax.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.slideanims.min.js"></script>
<script type="text/javascript" src="/web/js/revolution-slider/js/extensions/revolution.extension.video.min.js"></script>


</body>
</html>
