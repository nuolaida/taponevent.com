<?php
	require_once('./cron_config.php');
	require_once(LIBS_ADMIN_PATH . 'cron.class.php');
	$Cron = new Cron;
	$adodb->debug = true;

	if ($_GET['run']) {
		$file = preg_replace("/[^a-z0-9_\.]/", '', $_GET['run']);

		if (file_exists('./' . $file)) {
			include('./' . $file);
		} else {
			die('file not found');
		}

	} else {
		$cron = $Cron->get_cron_item();
		if ($adodb->debug) {
			debug($cron);
		}

		if ($cron) {
			$Cron->update_cron_item_run($cron['file_name']);

			if (file_exists('./' . $cron['file_name'])) {
				include('./' . $cron['file_name']);
			} else {
				die('file not found');
			}
		}
	}
?>