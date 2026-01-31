<?php
	$domain_config = [
	    'keyword' => 'taponevent.com',
	    'name' => 'taponevent.com',
	    'protocol' => 'https',
	    'domain' => 'taponevent.com',
	    'email'						=>	[
	        'from_name'		=>	'VAF - Vilnius Beer Festival',
	        'from_email'	=>	'info@vafest.lt',
	        'use_smtp'		=>	[
	            'host'		=>	'ceres.hosty.lt',
	            'port'		=>	465,
	            'secure'	=>	'ssl',
	            'username'	=>	'info@vafest.lt',
	        ],
	        'logo_file'	=>	SERVER_PATH . 'config/taponevent.com/email_logo.png',
	    ],
	    'default_site_language' => 'en',
	    'header_title' => [
	        'sufix' => ' - VAF',
	        'default' => 'Vilnius Beer Festival',
	    ],
	    'styles' => ['/config/taponevent.com/themes/styles.css'],
	    'styles_admin' => ['/config/taponevent.com/themes/styles_admin.css'],
	    'social'    =>  [
	        'facebook'  =>  ['link' => 'https://www.facebook.com/vafest/'],
	        'instagram' =>  ['link' => 'https://www.instagram.com/vafbeerfest/'],
	        'youtube'   =>  ['link' => 'https://www.youtube.com/channel/UCEEe94NQ50S2AlBv6IVI_wg'],
	     ],
		'default_sharing_image_id' => [
			'default' => 310,
			'catalogue_festivals' => 309,
			'catalogue_competitions' => 312,
		],
		'styles_template_color' => [
			'css_file' => 'theme-skin-green.css',
			'menu_color' => 'green',
		],
		'google_analytics_id' => '',
		'app' => [
			'styles' => [],
		],
	];
	
	$file_local = __DIR__ . '/config.email.php';
	if (file_exists($file_local)) {
		include $file_local;
	}
	
	if (is_development_version()) {
		$domain_config_this = $domain_config;
		$file_local = __DIR__ . '/config.local.php';
		if (file_exists($file_local)) {
			include $file_local;
			$domain_config = array_replace_recursive($domain_config_this, $domain_config);
		}
	}
	
