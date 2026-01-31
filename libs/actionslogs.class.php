<?php
class ActionsLogs {

	function add($table, $rec_id, $action, $description = null, $backup=false) {
		if ($table && $action) {
			$form = [
				'rec_table'   =>  $table,
				'rec_id'      =>  $rec_id,
				'user_id'     =>  $_SESSION['user']['id'],
				'rec_time'    =>  time(),
				'rec_action'  =>  $action,
				'description' =>  $description,
				'user_ip'     =>  $_SERVER['REMOTE_ADDR'],
			];
			$sql = make_insert_query('actions_log', $form);
			$log_id = execute_sql_query($sql, 'insert');

			if ($backup) {
				$this->add_backup($log_id, $backup);
			}

			return true;
		}

		return false;
	}



	private function add_backup($log_id, $data) {
		if ($log_id && $data) {
			if (!is_array($data)) {
				$data = array($data);
			}
			$backup = json_encode($data);

			$form = [
				'log_id'   => $log_id,
				'rec_data' => $backup,
			];
			$sql = make_insert_query('actions_log_backups', $form);
			execute_sql_query($sql, 'insert');

			return true;
		}

		return false;
	}



	function get_list($table, $rec_id, $from=0, $items=15) {
		$sql = "
				SELECT al.*, u.name as user_name, alb.rec_data AS backup
				FROM actions_log al
				LEFT JOIN users u ON u.id = al.user_id
				LEFT JOIN actions_log_backups alb ON alb.log_id = al.id
				WHERE al.rec_table = '" . addslashes($table) . "' AND al.rec_id = " . (int)$rec_id . "
				ORDER BY al.id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		return execute_sql_query($sql, 'get all');
	}



	function get_list_cnt($table, $rec_id) {
		$sql = "
				SELECT COUNT(id) AS cnt
				FROM actions_log
				WHERE rec_table = '" . addslashes($table) . "' AND rec_id = " . addslashes($rec_id) . "
			";
		return execute_sql_query($sql, 'get one');
	}



	function html_list($table, $rec_id) {
		global $Translate;

		$list = $this->get_list($table, $rec_id);

		$html = '';

		if ($list) {
			$html .= '<br /><br />';
			$html .= '<fieldset class="actions_log">';
			$html .= "<legend>" . $Translate->get_item('log') . "</legend>";
			$html .= "<ul>";
			foreach ($list as $item) {
				$html .= "<li>";
				$html .= my_date_format($item['rec_time'], 'middle');
				$html .= " - ";
				$html .= $item['user_name'];
				$html .= " - ";
				$html .= $Translate->get_item('actions log ' . $item['rec_action']);
				if ($item['description']) {
					$html .= " - ";
					$html .= $item['description'];
				}
				if ($item['backup']) {
					$html .= ' - <i class="material-icons">code</i>';
				}
				$html .= "</li>";
			}
			$html .= "</ul>";
			$html .= "</fieldset>";
		}

		return $html;
	}
}
?>
