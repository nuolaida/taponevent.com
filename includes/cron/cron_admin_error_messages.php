<?php
	if (is_development_version()) {
		$adodb->debug = true;
	}


	require_once($_SERVER['DOCUMENT_ROOT'] . '/config.globals.php');

	require_once(INCLUDES_FILES_PATH . "url.class.php");

	require_once(CONFIG_FILES_PATH . 'config.php');
	require_once(CONFIG_FILES_PATH . 'config.bn2.php');

	if (file_exists(CONFIG_FILES_PATH . $domain_keyword . '/config.php')) {
		require_once CONFIG_FILES_PATH . $domain_keyword . '/config.php';
		$page_special_config = $domain_config;
	}


	require_once(LIBS_MAIN_PATH . 'admin.main.class.php');
	$AdminMain = new AdminMain;


	// Check is email send works fine
	require_once(LIBS_USER_PATH . 'users.email.class.php');
	$UsersEmail = new UsersEmail;
	$emails_cnt = $UsersEmail->get_list_cnt();
	if ($emails_cnt > 10) {
		$form = array(
			'rec_name'	=>	$Translate->get_item('error users email send list too long'),
		);
		$AdminMain->add_error_messages_list($form);
	}



	// Check last quotation time
	require_once(LIBS_USER_PATH . 'quotations.class.php');
	$Quotations = new Quotations;
	$item = $Quotations->get_item_new();
	if ($item['rec_date'] < time()-7*24*60*60) {
		$form = array(
			'rec_name'	=>	$Translate->get_item('error quotation too old'),
			'link'		=>	'/admin.php?module=quotations&action=browse',
		);
		$AdminMain->add_error_messages_list($form);
	}



	$Cron->update_cron_item_stop($cron['file_name']);
?>