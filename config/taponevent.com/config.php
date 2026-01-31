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
	            'password'	=>	'SaqAgT3r::5BuQ6hS',
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
	
	
	
	
	
	if (is_development_version()) {
	    $domain_config['domain'] = 'taponevent.com.localhost';
	    $domain_config['protocol'] = 'http';
	}