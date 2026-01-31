<?php
class CronMain {

	// Item
	function get_cron_item($file_name=false) {
		if ($file_name !== false) {
			$sql = "SELECT * FROM bn2_cron WHERE file_name = '" . addslashes($file_name) . "'";
		} else {
			$sql = "
					SELECT *
					FROM bn2_cron
					WHERE do_not_run_this_day < DATE(NOW()) AND last_run + run_every_minutes * 60 < " . time() . " AND is_disabled = 0
					ORDER BY last_run + run_every_minutes * 60 ASC
				";
		}
		return execute_sql_query($sql, 'get row');
	}



	// Update last run time
	function update_cron_item_run($file_name) {
		$sql = "
				UPDATE bn2_cron
				SET last_run = " . time() . "
				WHERE file_name = '" . addslashes($file_name) . "'
			";
		return execute_sql_query($sql, 'update');
	}



	// Update last run time
	function update_cron_item_stop($file_name) {
		$sql = "
				UPDATE bn2_cron
				SET do_not_run_this_day = DATE(NOW())
				WHERE file_name = '" . addslashes($file_name) . "'
			";
		return execute_sql_query($sql, 'update');
	}


}
?>