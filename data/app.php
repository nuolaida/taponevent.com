<?php
	error_reporting(E_ERROR | E_PARSE);
	require __DIR__ . '/vendor/autoload.php';
	date_default_timezone_set('Europe/Vilnius');
	
	function is_development_version() {
	    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
	    $host = preg_replace('/:\d+$/', '', (string)$host);
	    $host = strtolower(trim($host));
	    if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
	        return true;
	    }
	    if (substr($host, -10) === '.localhost') {
	        return true;
	    }
	    return false;
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

	// --- Safe defaults / guards -------------------------------------------------
	// Ensure page_special_config exists so we can read default_site_language
	if (!isset($page_special_config) || !is_array($page_special_config)) {
		$page_special_config = [];
	}

	// Ensure session arrays exist
	if (!isset($_SESSION['main_messages']) || !is_array($_SESSION['main_messages'])) {
		$_SESSION['main_messages'] = [];
	}
	if (!isset($_SESSION['app']) || !is_array($_SESSION['app'])) {
		$_SESSION['app'] = [];
	}

	// Ensure app language is set (use page_special_config default or 'lt')
	if (empty($_SESSION['app']['language'])) {
		$_SESSION['app']['language'] = $page_special_config['default_site_language'] ?? 'lt';
	}

	// Ensure $url comes from request when not provided by bootstrap
	if (!isset($url) || !is_array($url)) {
		$url = $_REQUEST ?? [];
	}

	// Ensure $smarty exists (modules check is_object before using)
	if (!isset($smarty) || !is_object($smarty)) {
		if (class_exists('Smarty')) {
			$smarty = new Smarty();
			// Optionally configure template/compile dirs here if needed
		} else {
			$smarty = null;
		}
	}

	// Ensure template_name exists to avoid notices later
	if (!isset($template_name)) {
		$template_name = null;
	}
	// --------------------------------------------------------------------------
	
	$popup = false;
	$syspage = [];
	
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
	if (!empty($_SESSION['app']['festival'])) {
		// Optionally assign festival info when needed. Kept intentionally empty to avoid analyzer warnings.
		// $smarty->assign('festival_info', $Festivals->get_festivals_item($_SESSION['app']['festival']));
		$_tmp_noop = true; unset($_tmp_noop);
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

	// Make development flag available to templates before they are fetched
	$smarty->assign('is_development_version', is_development_version() ? 1 : 0);
	$smarty->assign('module_html', my_fetch($template_file));
	$smarty->assign('active_module', $url['_module_']);
	$smarty->assign('active_action', $url['_action_']);
	$smarty->assign('language_active', $_SESSION['app']['language']);
	$smarty->assign('page_messages', $_SESSION['main_messages']);
	$_SESSION['main_messages'] = [];
	echo my_fetch('layout.tpl');
