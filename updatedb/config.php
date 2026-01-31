<?php
	error_reporting(E_ERROR | E_PARSE);
	
	function is_development_version() {
		$is_development_version = false;
		preg_match("/.*\.(.*)$/", $_SERVER['HTTP_HOST'], $matches);
		if ($matches[1] == 'localhost') {
			$is_development_version = true;
		}
		
		return $is_development_version;
	}
	
	require __DIR__ . '/../vendor/autoload.php';
	
	
	require_once($_SERVER['DOCUMENT_ROOT'] . '/config.globals.php');
	require_once(CONFIG_FILES_PATH . 'config.bn2.php');
	require_once(CONFIG_FILES_PATH . 'config.db.php');
	
	
	require_once(SERVER_PATH . 'functions.php');
	
	require_once(LIBS_MAIN_PATH . 'translate.class.php');
	$Translate = new Translate;
	
	require_once(INCLUDES_FILES_PATH . "url.class.php");
	
	require_once(CONFIG_FILES_PATH . 'config.templates.php');
	
	require_once(LIBS_MAIN_PATH . 'actionslogs.class.php');
	$ActionsLogs = new ActionsLogs;


