<?php
	$config['db'] = array(
		'user'		=>	'taponevent',
		'password'	=>	'0*7775d7G775fgjao',
		'database'	=>	'taponevent',
		'host'		=>	'127.0.0.1',
	);
	
	if (is_development_version()) {
		$cfg_this = $config['db'];
		$file_local = __DIR__ . '/config.db.local.php';
		if (file_exists($file_local)) {
			include $file_local;
			$config['db'] = array_replace_recursive($cfg_this, $config['db']);
		}
	}
