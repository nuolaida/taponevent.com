<?php
	error_reporting(E_ERROR | E_PARSE);
	require __DIR__ . '/vendor/autoload.php';
	date_default_timezone_set('Europe/Vilnius');
	
	ini_set( "session.gc_maxlifetime", 28800 );
	ini_set( "session.cookie_lifetime", 10800 );
	ini_set( "session.cookie_domain", "" );
	session_cache_limiter( "must-revalidate" );
	session_set_cookie_params( time() + 3600 );
	session_start();

	function is_development_version() {
		$is_development_version = false;
		preg_match("/.*\.(.*)$/", $_SERVER['HTTP_HOST'], $matches);
		if ($matches[1] == 'localhost') {
			$is_development_version = true;
		}
	
		return $is_development_version;
	}
	
	require_once('config.globals.php');
	
	$title = '';
	$submenu = [];
	$popup = false;
	
	include_once(CONFIG_FILES_PATH . 'config.admin.php');

	require_once(LIBS_MAIN_PATH . 'Translate.class.php');
	$Translate = new Translate(false);

	require_once(INCLUDES_FILES_PATH . 'paging/paging.admin.class.php');
	$Paging = new paging;

	require_once(LIBS_MAIN_PATH . 'actionslogs.class.php');
	$ActionsLogs = new ActionsLogs;

	$url = all_params_to_url();

	require_once(INCLUDES_FILES_PATH . "url.class.php");

	if ($_GET['change_language']) {
		$Translate->change_language($_GET['change_language']);
		$new_url = admin_make_url($_SERVER['HTTP_REFERER']);
		Location($new_url);
		die();
	}
	
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users;
	$Users->check_login();
	if ($_SESSION['user']['admin']) {
		$id = $_SESSION['user']['id'];
	} else {
		Location($dispatch->buildUrl('/index.php?module=users&action=loginForm'));
		die();
	}
	
	switch ($url['_module_']) {
		case 'texts':
		case 'users':
	        $module = $url['_module_'];
			break;
	}
	if (!$module) {
		$module = array_key_first($_SESSION['user']['admin_rights']);
	}

	$page = $module;
	$GLOBALS['module'] = $module;
	$this_page = $page_special_config['protocol'] . "://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}/{$module}";
	
	if (!$_SESSION['user']['rights'] || !is_array($_SESSION['user']['rights']) || count($_SESSION['user']['rights']) == 0) {
		Location("/");
		die();
	}

	// Text fields class
	require_once(LIBS_MAIN_PATH . 'TextLanguages.class.php');
	$TextLanguages = new TextLanguages();


	##### temp #####
	$user_info = ($rs) ? $_SESSION['user'] : false;
	$admin = ($user_info) ? $user_info['admin'] : '';
	$smarty->assign('adm', $admin);
	$smarty->assign('session', 'ok');
	//$smarty->assign('server_url', SERVER_URL);
	$server_path_info = server_path_info('admin');
	$smarty->assign('server_path_info', $server_path_info);

	require_once(MODULES_PATH . "admin/" . $module . ".php");

	if (isset($_SESSION['main_messages']) && $_SESSION['main_messages']) {
		if (!is_array($_SESSION['main_messages'])) {
			$_SESSION['main_messages'] = array($_SESSION['main_messages']);
		}
		$smarty->assign("main_messages", $_SESSION['main_messages']);
		unset($_SESSION['main_messages']);
	}

	if (!is_array($title)) {
		$title = [$title];
	}
	$smarty->assign('title', $title);
	$smarty->assign('popup', ($popup || $main_page['popup']) ? true : false);
	$smarty->assign('conf_site_protocol', $page_special_config['protocol']);
	$smarty->assign('conf_site_domain', $page_special_config['domain']);
	$smarty->assign('conf_site_domain_mobile', $page_special_config['domain_mobile']);
	$smarty->assign('conf_site_templates', SMARTY_TEMPLATES_PATH . 'admin/');
	$smarty->assign('is_development_version', is_development_version());
	//$block_html = $smarty->fetch(TMPL_PATH . 'admin/modules/' . $page . '.tpl');
	$smarty->assign('layout_favicon', my_fetch('layout.favicon.tpl', null, null, null, 'frontend'));
	$block_html = my_fetch($page . '.tpl');
	$smarty->assign('block_html', $block_html);
	$smarty->assign('self', $page_special_config['protocol'] . "://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}/{$module}");
	$smarty->assign('script', $page_special_config['protocol'] . "://{$_SERVER['HTTP_HOST']}{$_SERVER['SCRIPT_NAME']}");
	$smarty->assign('site_domain', $page_special_config['domain']);
	$smarty->assign('admin_username', $_SESSION['user']['name']);
	$smarty->assign('site_header_name', $page_special_config['keyword']);
	// Header favicon
	if (isset($page_special_config['favicon'])) {
		$smarty->assign('site_header_favicon', '/config/' . $page_special_config['keyword'] . '/' . $page_special_config['favicon']);
	}


	// CSS
	$arr = [];
	$css_file = '/themes/admin/styles.css';
	$css_file = $css_file . '?' . filemtime(SERVER_PATH . $css_file);
	$arr[] = $css_file;
	if (isset($page_special_config['styles_admin'])) {
		$page_special_config['styles_admin'] = (is_array($page_special_config['styles_admin'])) ? $page_special_config['styles_admin'] : array($page_special_config['styles_admin']);
		foreach ($page_special_config['styles_admin'] as $value) {
			$arr[] = $value;
		}
	}
	$smarty->assign('site_header_theme_add', $arr);
	
	// Menu
	$smarty->assign('admin_menu', $Users->get_admin_menu());
	if ($submenu && is_array($submenu)) {
		$page_submenu = array();
		foreach ($submenu as $key => $value) {
			if ($key && !is_int($key)) {
				if (substr($value, 0, 1) == '/') {
					$page_submenu[] = array(
							'name'	=>	$key,
							'link'	=>	$value,
						);
				} else {
					$page_submenu[] = array(
							'name'	=>	$key,
							'link'	=>	admin_make_url('/admin.php?module=' . $module . '&action=' . $value),
						);
				}
			} else {
				$page_submenu[] = $value;
			}
		}
		$smarty->assign('page_submenu', $page_submenu);
	}

	$smarty->assign('page_module', $module);
	$smarty->assign('language_active', $Translate->language);

	echo my_fetch('index.tpl');
