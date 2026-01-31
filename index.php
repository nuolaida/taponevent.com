<?php
	error_reporting(E_ERROR | E_PARSE);
	require __DIR__ . '/vendor/autoload.php';
	date_default_timezone_set('Europe/Vilnius');
	
	function is_development_version() {
		$is_development_version = false;
		preg_match("/.*\.(.*)$/", $_SERVER['HTTP_HOST'], $matches);
		if ($matches[1] == 'localhost') {
			$is_development_version = true;
		}
	
		return $is_development_version;
	}
	
	ini_set('display_errors', 1);
	// Testing
	if (isset($_GET['testing'])) {
		if ((int)$_GET['testing']) {
			setcookie('testing', $_GET['testing'], time() + 60 * 60 * 24 * 30, '/');
		} else {
			setcookie('testing', $_GET['testing'], time() - 1, '/');
		}
	}
	
	
	require_once('config.globals.php');
	$popup = false;
	$syspage = array();
	
	
	
	
	ini_set( "session.gc_maxlifetime", 28800 );
	ini_set( "session.cookie_lifetime", 10800 );
	ini_set( "session.cookie_domain", "" );
	session_cache_limiter( "must-revalidate" );
	session_set_cookie_params( time() + 3600 );
	session_start();
	
	include_once(CONFIG_FILES_PATH . 'config.bn2.php');

	// Translation class
	require_once(LIBS_MAIN_PATH . 'Translate.class.php');
	$Translate = new Translate;
	
	include_once(CONFIG_FILES_PATH . 'config.php');
	
	
	$url = all_params_to_url();
	require_once(INCLUDES_FILES_PATH . "url.class.php");
	if (isset($attributes) && is_array($attributes)) {
		$url = array_merge($url, $attributes);
	}
	
	if ($_GET['change_language']) {
	    setcookie('language', $_GET['change_language'], time() + (90 * 24 * 60 * 60));
	    $_SESSION['language'] = $_GET['change_language'];
	    $Translate->change_language($_GET['change_language']);
	    $dispatch = new Url([]);
	    $url_data = array_merge(['action' => $url['_action_'], 'module' => $url['_module_']], $url);
	    unset($url_data['_action_'], $url_data['_module_'], $url_data['rec_url_name']);
	    $new_url = smarty_modifier_make_url($url_data);
	
	    if($new_url === false) {
	        Location('/');
	    } else {
	        Location($new_url);
	    }
	    die();
	}

	$smarty->assign('now', time());
	
	// Puslapis
	
	switch ($url['_module_']) {
		case 'texts':
		case 'users':
			$module = $url['_module_'];
			break;
	}
	$smarty->assign("page", $module);
	
	
	
	// Pranesimai vartotojui
	if (isset($_SESSION['main_messages']) && $_SESSION['main_messages']) {
		if (!is_array($_SESSION['main_messages'])) {
			$_SESSION['main_messages'] = array($_SESSION['main_messages']);
		}
		$smarty->assign("main_messages", $_SESSION['main_messages']);
		unset($_SESSION['main_messages']);
	}
	
	$smarty->assign('cookie_testing', $_COOKIE['testing']);
	$smarty->assign('site_theme', (isset($_SESSION['site_theme']) ? $_SESSION['site_theme'] : ''));
	$smarty->assign('conf_site_protocol', $page_special_config['protocol']);
	$smarty->assign('conf_site_domain', $page_special_config['domain']);
	$smarty->assign('conf_site_domain_mobile', $page_special_config['domain_mobile']);
	
	// Vartotojo duomenys
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users;
	$Users->check_login();
	$smarty->assign("user_info", $_SESSION['user']);
	
	$server_path_info = server_path_info();
	$smarty->assign('server_path_info', $server_path_info);
	
	if (file_exists(MODULES_PATH . $module . ".php")) {
		include_once(MODULES_PATH . $module . ".php");
	} else {
		die('...');
	}
	
	$smarty->assign("is_first_page", ($_SERVER['REQUEST_URI'] == '/' || $_SERVER['REQUEST_URI'] == '/index.php') ? true : false);
	
	
	
	// Layout
	$smarty->assign('layout_list_social_links', $page_special_config['social']);
	require_once(LIBS_MAIN_PATH . 'texts.class.php');
	$Texts = new Texts();
	$smarty->assign('list_texts', $Texts->get_list_associative());
	$smarty->assign('layout_main_menu', my_fetch('layout.menu.main.tpl'));
	$smarty->assign('layout_favicon', my_fetch('layout.favicon.tpl'));
	$smarty->assign('styles_template_color', $page_special_config['styles_template_color']);
	$smarty->assign('conf_footer_line_image_id', $page_special_config['footer_line_image_id']);
	$smarty->assign('conf_google_analytics_id', $page_special_config['google_analytics_id']);
	
	
	
	// CSS
	$arr = array();
	if (isset($page_special_config['styles_clear']) && $page_special_config['styles_clear'] === true) {
		$arr = array();
	}
	if (isset($page_special_config['styles']) || $_COOKIE['site_theme']) {
		if (isset($page_special_config['styles']) && is_array($page_special_config['styles'])) {
			foreach ($page_special_config['styles'] as $value) {
				if (is_array($value)) {
					$css_file = '/config/' . $page_special_config['keyword'] . '/' . $value['file'];
					$css_file .= '?' . filemtime(SERVER_PATH . substr($css_file, 1));
					$value['file'] = $css_file;
					$arr[] = $value;
				} else {
					$css_file = $value;
					$css_file .= '?' . filemtime(SERVER_PATH . substr($css_file, 1));
					$arr[] = $css_file;
				}
			}
		}
		// User Themes
		if ($_COOKIE['site_theme']) {
			$arr[] = '/config/' . $page_special_config['keyword'] . '/' . $_COOKIE['site_theme'];
		}
	}
	$smarty->assign('site_header_theme_add', $arr);
	
	
	
	// Header additional info
	if (isset($page_special_config['header_html'])) {
		if (!is_array($page_special_config['header_html'])) {
			$page_special_config['header_html'] = array($page_special_config['header_html']);
		}
		$smarty->assign('site_header_html', $page_special_config['header_html']);
	}
	
	
	
	// Footer additional info
	if (isset($page_special_config['footer_html'])) {
		if (!is_array($page_special_config['footer_html'])) {
			$page_special_config['footer_html'] = array($page_special_config['footer_html']);
		}
		$smarty->assign('site_footer_html', $page_special_config['footer_html']);
	}
	
	
	
	// Header favicon
	if (isset($page_special_config['favicon'])) {
		$smarty->assign('site_header_favicon', '/config/' . $page_special_config['keyword'] . '/' . $page_special_config['favicon']);
	}
	
	
	
	// Page title
	$title_prefix = ($Translate->get_item('page title prefix', true)) ?: $page_special_config['header_title']['prefix'];
	$title_suffix = ($Translate->get_item('page title suffix', true)) ?: $page_special_config['header_title']['sufix'];
	if ($syspage['page_title']) {
		if (!is_array($syspage['page_title'])) {
			$syspage['page_title'] = [$syspage['page_title']];
		}
		$syspage['page_title'] = htmlspecialchars($title_prefix . str_replace('&quot;', '"', implode(' / ', $syspage['page_title']))) . $title_suffix;
	} else if ($title) {
		$syspage['page_title'] = htmlspecialchars($title_prefix . $title . $title_suffix);
	} else {
		$syspage['page_title'] = ($Translate->get_item('page title default', true)) ?: $page_special_config['header_title']['default'];
		$syspage['page_title'] = $title_prefix . $syspage['page_title'] . $title_suffix;
	}
	$smarty->assign('page_title', $syspage['page_title']);
	$smarty->assign('page_og_title', $syspage['page_title']);
	
	
	
	// Page keywords
	if ($syspage['page_keywords']) {
		if (!is_array($syspage['page_keywords'])) {
			$syspage['page_keywords'] = array($syspage['page_keywords']);
		}
		foreach ($syspage['page_keywords'] as $key => $value) {
			$syspage['page_keywords'][$key] = htmlspecialchars($value);
		}
		$smarty->assign('page_keywords', implode(', ', $syspage['page_keywords']));
	}
	
	
	
	// Page url
	if ($syspage['page_og_url']) {
		if (substr($syspage['page_og_url'], 0, 1) == '/') {
			$syspage['page_og_url'] = $page_special_config['protocol'] . '://' . $page_special_config['domain'] . $syspage['page_og_url'];
		}
		$smarty->assign('page_og_url', $syspage['page_og_url']);
	}
	
	if (isset($syspage['place:location:longitude'])) {
	    $smarty->assign('page_og_lon', $syspage['place:location:longitude']);
	}
	
	if (isset($syspage['place:location:latitude'])) {
	    $smarty->assign('page_og_lat', $syspage['place:location:latitude']);
	}
	
	// Page type
	if ($syspage['page_og_type']) {
		$smarty->assign('page_og_type', $syspage['page_og_type']);
	}
	
	
	// Page author
	if ($syspage['page_article_author']) {
		$smarty->assign('page_article_author', $syspage['page_article_author']);
	}
	
	
	// Page description
	if ($syspage['page_description']) {
		$smarty->assign('page_description', trim(htmlspecialchars($syspage['page_description'])));
	}
	if ($syspage['page_og_description']) {
		$smarty->assign('page_og_description', trim(htmlspecialchars($syspage['page_og_description'])));
	}
	// Page canonical
	if ($syspage['page_canonical'] && trim($syspage['page_canonical'])) {
		$smarty->assign('page_canonical', $syspage['page_canonical']);
	}
	// Page html tag attributes
	if (isset($syspage['page_html_attr'])) {
		if (!is_array($syspage['page_html_attr'])) {
			$syspage['page_html_attr'] = array($syspage['page_html_attr']);
		}
		$smarty->assign('page_html_attr', implode(' ', $syspage['page_html_attr']));
	}
	
	
	// Link Image
	if (!$syspage['page_og_image'] && $syspage['page_fb_image']) {
		$syspage['page_og_image'] = $syspage['page_fb_image'];
	} else if (!$syspage['page_og_image']) {
		$syspage['page_og_image'] = $page_special_config['protocol'] . '://' . $page_special_config['domain'] . $dispatch->buildUrl('/index.php?module=images&action=show&id=' . $page_special_config['default_sharing_image_id']['default'] . '&type=any1000x1000');;;
	}
	if ($syspage['page_og_image']) {
		if (substr($syspage['page_og_image'], 0, 1) == '/') {
			$syspage['page_og_image'] = $page_special_config['protocol'] . '://' . $page_special_config['domain'] . $syspage['page_og_image'];
		}
		$smarty->assign('page_og_image', $syspage['page_og_image']);
		$smarty->assign('page_fb_image', $syspage['page_og_image']);
	}
	
	
	// Additional headers
	if ($syspage['page_add_header']) {
		if (!is_array($syspage['page_add_header'])) {
			$syspage['page_add_header'] = array($syspage['page_add_header']);
		}
		$syspage['page_add_header'] = implode("\n", $syspage['page_add_header']);
		$smarty->assign('page_add_header', $syspage['page_add_header']);
	}
	
	
	// Page Refresh
	if ($syspage['page_refresh']) {
		$smarty->assign('page_refresh', $syspage['page_refresh']);
	}
	
	
	
	if ($page_special_config['administration_logo']) {
		$smarty->assign('administration_logo', $page_special_config['administration_logo']);
	}
	
	
	
	###########################
	$smarty->assign('page_og_site_name', $page_special_config['name']);
	$smarty->assign('page_google_client_id', $page_special_config['google_client_id']);
	$smarty->assign('page_facebook_app_id', $page_special_config['facebook_app_id']);
	$smarty->assign('page_facebook_graph_version', $page_special_config['facebook_graph_version']);
	//$smarty->assign('html_admin_login_block', $Users->html_admin_login_block());
	$smarty->assign('advertisement_league_id', $advertisement_league_id);
	$smarty->assign('content_2cols', ($syspage['content_2cols']) ? $syspage['content_2cols'] : '');
	$smarty->assign('content_3cols', ($syspage['content_3cols']) ? $syspage['content_3cols'] : '');
	$smarty->assign('content', (!empty($mod)) ? $mod : '&nbsp;');
	$smarty->assign('domain_keyword', $domain_keyword);
	$smarty->assign('display_layout', ($popup || $syspage['popup']) ? true : false);
	$smarty->assign('active_module', $url['_module_']);
	$smarty->assign('active_action', $url['_action_']);
	$smarty->assign('is_development_version', is_development_version());
	$smarty->assign('display_title_page', ($active_module_config['page'] == 'title') ? 'title' : '');
	$smarty->assign('language_active', $Translate->language);
	$smarty->assign('index_html', my_fetch(($active_module_config['index']) ? $active_module_config['index'] : 'index.tpl'));
	$smarty->display('layout.tpl');
	
