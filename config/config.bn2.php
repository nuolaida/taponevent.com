<?php
	$HTTP_SERVER_VARS = &$_SERVER;
	require_once(SERVER_PATH . 'functions.php');
	
	$conf["crypt_password"] = 'drgdfg2DF858:QMm';
	$conf["site_domain"] = $domain_config['domain'];
	$conf["webmaster_email"] = 'vidmantas@kitoks.lt';
	
	// Smarty caching
	$conf['disable_all_smarty_caching'] = false; // on progress to apply everywhere
	$conf['smarty_caching_default_time'] = 5 * 60;
	

	$conf['domain_keyword'] = [
		'taponevent.com.localhost' => 'taponevent.com',
	];
	// Page config
	$page_special_part = '';
	$domain_keyword = $_SERVER['HTTP_HOST'];
	if (substr($domain_keyword, 0, 4) == 'www.') {
		$domain_keyword = substr($domain_keyword, 4);
	}
	if (substr($domain_keyword, 0, 2) == 'm.') {
		$domain_keyword = substr($domain_keyword, 2);
	}
	if (isset($conf['domain_keyword'][$domain_keyword])) {
		$domain_keyword = $conf['domain_keyword'][$domain_keyword];
	}
	
	
	// Domain configs
	$active_module_config = [];
	if (file_exists(SERVER_PATH . 'config/' . $domain_keyword . '/config.php')) {
		require_once SERVER_PATH . 'config/' . $domain_keyword . '/config.php';
	
		if ($_SERVER['HTTP_HOST'] != $domain_config['domain'] && $_SERVER['HTTP_HOST'] != $domain_config['domain_mobile']) {
			my_header_code(301);
			Location("http://" . $domain_config['domain'] . $_SERVER['REQUEST_URI']);
			die();
		} else {
			$page_special_config = $domain_config;
			$page_special_config['site_config_path'] = SERVER_PATH . 'config/' . $domain_keyword . '/';
		}
	
	
		// redirect to https
		if ($domain_config['protocol'] == 'https') {
			$redirect = false;
			if (isset($_SERVER["HTTP_X_FORWARDED_PROTO"])) {
				if ($_SERVER["HTTP_X_FORWARDED_PROTO"]  == 'http') {
					$redirect = true;
				}
			} else if (!$_SERVER['HTTPS']) {
				$redirect = true;
			}
			if ($redirect) {
				my_header_code(301);
				Location("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
				die('');
			}
		}
	
	
	} else {
		die('www');
		
		my_header_code(301);
		Location("//" . $conf['http_host'] . $_SERVER['REQUEST_URI']);
		die();
	}
