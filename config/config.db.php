<?php
	use BestPub\Utils\Services\Db;
	
	if (file_exists(SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/config.db.local.php')) {
		require_once SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/config.db.local.php';
	} else {
		require_once SERVER_PATH . 'config/' . $page_special_config['keyword'] . '/config.db.php';
	}
	
	$adodb = ADONewConnection('mysqli');
	$adodb->Connect($config['db']['host'], $config['db']['user'], $config['db']['password'] , $config['db']['database']);
	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	$adodb->debug = false;
	$adodb->Execute("SET NAMES 'utf8mb4'");
	$adodb->Execute("SET CHARACTER SET utf8mb4");
	$adodb->Execute("SET SESSION collation_connection ='utf8mb4_lithuanian_ci'");

	if(class_exists(Db::class)) {
		Db::setInstance($adodb);
		Db::setDbConfig($config['db']);
	}
