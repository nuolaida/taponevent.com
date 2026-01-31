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
	
	require_once('config.globals.php');
	
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
	
	$popup = false;
	$syspage = [];
	if (!$_SESSION['main_messages']) {
		$_SESSION['main_messages'] = [];
	}
	if (!$_SESSION['app']['language']) {
		$_SESSION['app']['language'] = $page_special_config['default_site_language'];
	}
	$page_module = (isset($url['_module_'])) ? $url['_module_'] : 'festivals';
	$page_action = (isset($url['_action_'])) ? $url['_action_'] : 'work';
	
	// Festival
	require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
	$Festivals = new Festivals();
	$Festivals->check_festival_user_selected();
	if (!$_SESSION['app']['festival'] && $_SESSION['user'] && $page_module != 'users') {
		$page_module = 'festivals';
		if ($page_action != 'selectAct') {
			$page_action = 'select';
		}
	}
	
	// User
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users();
	$Users->check_login();
	$smarty->assign("user_info", $_SESSION['user']);
	if ((!isset($_SESSION['user']) || !$_SESSION['user']['id']) && $page_module != 'users') {
		$page_module = 'users';
		$page_action = 'login';
	}
	
	switch ($page_module) {
		case 'festivals':
		case 'users':
			$module = $page_module;
			break;
		default:
			$module = 'users';
			break;
	}
	require_once(MODULES_PATH . 'app/' . $module . ".php");
	$template_file = $template_name . '.tpl';
	
	/*
	require_once(LIBS_MAIN_PATH . 'texts.class.php');
	$Texts = new Texts();
	$smarty->assign('list_texts', $Texts->get_list_associative());
	*/
	
	// Layout
	if ($_SESSION['app']['festival']) {
		//$smarty->assign('festival_info', $Festivals->get_festivals_item($_SESSION['app']['festival']));
	}
	
	// CSS
	$styles = ['/themes/app/styles.css'];
	if (isset($page_special_config['app']['styles'])) {
		$styles = array_merge($styles, $page_special_config['app']['styles']);
	}
	foreach ($styles as $key => $item) {
		$styles[$key] = $item . '?' . filemtime(SERVER_PATH . substr($item, 1));
	}
	$smarty->assign('page_styles', $styles);

	$smarty->assign('module_html', my_fetch($template_file));
	$smarty->assign('active_module', $url['_module_']);
	$smarty->assign('active_action', $url['_action_']);
	$smarty->assign('is_development_version', is_development_version());
	$smarty->assign('language_active', $_SESSION['app']['language']);
	$smarty->assign('page_messages', $_SESSION['main_messages']);
	$_SESSION['main_messages'] = [];
	echo my_fetch('layout.tpl');
	
