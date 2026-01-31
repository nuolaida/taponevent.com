<?php
class Festivals {
	var array $module_config;
	var string $site_language;
	var int $max_topup_ammount;
	var string $test_nfc_card;

	function  __construct() {
		global $page_special_config, $Translate;
		
		$this->site_language = $Translate->language;
		$this->max_topup_ammount = 500;
		$this->test_nfc_card = 'TEST-KORTELE-123456';
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/festivals.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/festivals.config.php');
			/** @var array $festivals_config */
			$this->module_config = $festivals_config;
		}
	}


	function get_festivals_list($from, $items, $params=[])
	{
		$params = $this->get_festivals_list_params($params);

		$sql_params = $params['sql_params'];
		$sql_params['language']= $this->site_language;
		$sql = "
				SELECT f.*, lvt.text AS title
				FROM festivals f
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'festivals' AND lvt.table_id = f.id AND lvt.keyword = 'title' AND lvt.language = :language
				WHERE 1 " . $params['where'] . "
				GROUP BY f.id
				ORDER BY f.time_starts DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all', $sql_params);
	}
	function get_festivals_list_cnt($params=[])
	{
		$params = $this->get_festivals_list_params($params);
		
		$sql_params = $params['sql_params'];
		$sql = "
				SELECT COUNT(f.id) AS cnt
				FROM festivals f
				WHERE 1 " . $params['where'] . "
			";
		
		return execute_sql_query($sql, 'get one', $sql_params);
	}
	function get_festivals_list_params($params): array
	{
		$where = [];
		$sql_params = [];
		
		if (isset($params['active'])) {
			if ($params['active']) {
				$where[] = "f.is_active = 1";
			} else {
				$where[] = "f.is_active IS NULL";
			}
		}
		
		if ($params['search']) {
			$where[] = "lvt.text LIKE :search";
			$sql_params['search'] = '%' . trim($params['search']) . '%';
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'sql_params' => $sql_params,
		);
	}



	function get_festivals_item($id) {
		$slq_params = [
			'festival_id' => $id,
			'language' => $this->site_language,
		];
		$sql = "
				SELECT F.*, LVT.text AS title, U.email AS user_email
				FROM festivals F
			    LEFT JOIN users U ON U.id = F.user_id
				LEFT JOIN languages_varchar LVT ON LVT.table_name = 'festivals' AND LVT.table_id = F.id AND LVT.keyword = 'title' AND LVT.language = :language
				WHERE F.id = :festival_id
			";
		
		return execute_sql_query($sql, 'get row', $slq_params);
	}
	function get_festivals_item_latest() {
		$list = $this->get_festivals_list();
		if ($list) {
			$data = reset($list);
			if ($data) {
				return $data;
			}
		}
	}


	function update_festivals_item($id, $form) {
		global $TextLanguages;

		$title = $form['title'];
		unset($form['title']);

		$sql = make_update_query('festivals', $form, array('id' => $id));
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('festivals', $id, 'title', $title);
		}

		return $return;
	}


	function add_festivals_item($form) {
		global $TextLanguages;

		$title = $form['title'];
		unset($form['title']);

		$sql = make_insert_query('festivals', $form);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('festivals', $id, 'title', $title);
		}

		return $id;
	}
	
	
	function get_companies_list($from, $items, $params=[])
	{
		$params = $this->get_companies_list_params($params);
		
		$sql_params = $params['sql_params'];
		$sql_params['language']= $this->site_language;
		$sql = "
				SELECT fc.*, COUNT(DISTINCT(fu.user_id)) AS users_total,
				       0-SUM(IF(CKT.price < 0, CKT.price, 0)) AS incomes_total
				FROM festivals_users_companies fc
				LEFT JOIN festivals_users_companies_users fu ON fu.company_id = fc.id
				LEFT JOIN festivals_checkout CKT ON CKT.festival_id = fc.festival_id AND CKT.user_id = fu.user_id
				WHERE " . $params['where'] . "
				GROUP BY fc.id
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all', $sql_params);
	}
	function get_companies_list_cnt($params=[])
	{
		$params = $this->get_companies_list_params($params);
		
		$sql_params = $params['sql_params'];
		$sql = "
				SELECT COUNT(fc.id) AS cnt
				FROM festivals_users_companies fc
				WHERE 1 " . $params['where'] . "
			";
		
		return execute_sql_query($sql, 'get one', $sql_params);
	}
	function get_companies_list_params($params): array
	{
		$where = [];
		$sql_params = [];
		$order = [];
		
		if (isset($params['active'])) {
			if ($params['active']) {
				$where[] = "fc.is_active = 1";
			} else {
				$where[] = "fc.is_active IS NULL";
			}
		}
		
		if ((int)$params['festival']) {
			$where[] = "fc.festival_id = :festival_id";
			$sql_params['festival_id'] = $params['festival'];
			$order[] = "fc.title ASC";
		}
		
		if (!$order) {
			$order[] = "fc.id DESC";
		}
		
		return array(
			'where' => (count($where)) ? (implode(' AND ', $where)) : '1',
			'sql_params' => $sql_params,
			'order' => (count($order)) ? (' ' . implode(', ', $order)) : ' s.sent_time DESC ',
		);
	}
	
	
	function get_companies_item($id) {
		$slq_params = [
			'company_id' => $id,
			'language' => $this->site_language,
		];
		$sql = "
				SELECT FC.*, LVT.text AS festival_title
				FROM festivals_users_companies FC
				LEFT JOIN festivals F ON F.id = FC.festival_id
				LEFT JOIN languages_varchar LVT ON LVT.table_name = 'festivals' AND LVT.table_id = F.id AND LVT.keyword = 'title' AND LVT.language = :language
				WHERE FC.id = :company_id
			";
		
		return execute_sql_query($sql, 'get row', $slq_params);
	}
	
	
	function update_companies_item($id, $form) {
		$sql = make_update_query('festivals_users_companies', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}
	
	
	function add_companies_item($form) {
		$sql = make_insert_query('festivals_users_companies', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	function get_users_list_by_company($company_id)
	{
		$sql_params = [
			'company_id' => $company_id,
		];
		$sql = "
				SELECT FU.*, U.email AS user_email, U.name AS user_name,
				       FC.title AS company_title, FC.festival_id
				FROM festivals_users_companies_users FU
				LEFT JOIN festivals_users_companies FC ON FU.company_id = FC.id AND FC.id = :company_id
				LEFT JOIN users U ON FU.user_id = U.id
				WHERE FC.id = :company_id
				ORDER BY U.email
			";
		
		return execute_sql_query($sql, 'get all', $sql_params);
	}
	
	
	function get_users_item($user_id, $festival_id=false)
	{
		$festival_id = ((int)$festival_id) ?: $_SESSION['app']['festival'];
		if (!$festival_id) {
			return false;
		}
		$slq_params = [
			'user_id' => $user_id,
			'festival_id' => $festival_id,
		];
		$sql = "
			SELECT FU.*, FC.app_type AS user_app_type,
			       U.email AS user_email, U.name AS user_name
			FROM festivals_users_companies_users FU
			INNER JOIN festivals_users_companies FC ON FU.company_id = FC.id AND FC.festival_id = :festival_id
			LEFT JOIN users U ON U.id = FU.user_id
			WHERE FU.user_id = :user_id
		";
		return execute_sql_query($sql, 'get row', $slq_params);
	}
	
	
	function update_users_item($user_id, $company_id, $form) {
		$sql = make_update_query('festivals_users_companies_users', $form, ['user_id' => $user_id, 'company_id' => $company_id]);
		return execute_sql_query($sql, "update");
	}
	
	
	function add_users_item($form) {
		$sql = make_insert_query('festivals_users_companies_users', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	function get_prices_list_by_company($company_id)
	{
		$sql_params = [
			'company_id' => $company_id,
		];
		$sql = "
				SELECT FP.*
				FROM festivals_users_companies_prices FP
				WHERE FP.company_id = :company_id
				ORDER BY FP.price ASC
			";
		
		return execute_sql_query($sql, 'get all', $sql_params);
	}
	
	
	function get_prices_item($id, $festival_id=false)
	{
		$festival_id = ((int)$festival_id) ?: $_SESSION['app']['festival'];
		if (!$festival_id) {
			return false;
		}
		$slq_params = [
			'price_id' => $id,
		];
		$sql = "
			SELECT FP.*, FC.title AS company_title
			FROM festivals_users_companies_prices FP
			LEFT JOIN festivals_users_companies FC ON FP.company_id = FC.id
			WHERE FP.id = :price_id
		";
		return execute_sql_query($sql, 'get row', $slq_params);
	}
	
	
	function update_prices_item($id, $form) {
		$sql = make_update_query('festivals_users_companies_prices', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}
	
	
	function add_prices_item($form) {
		$sql = make_insert_query('festivals_users_companies_prices', $form);
		return execute_sql_query($sql, "insert");
	}



	// Relations Add
	function add_relations_item($festival_id, $rec_table, $rec_id) {
		if (!(int)$festival_id || !addslashes($rec_table) || !(int)$rec_id) {
			return false;
		}

		$form = [
			'festival_id'   => $festival_id,
			'rec_table'     => $rec_table,
			'rec_id'        => $rec_id,
		];
		$sql = make_insert_query('festivals_relations', $form);
		return execute_sql_query($sql, "insert");
	}



	// Relations Delete
	function delete_relations_item($id) {
		if (!(int)$id) {
			return false;
		}

		$sql = "DELETE FROM festivals_relations WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}



	// Festivals with or without relations



	// Festivals List
	function get_festivals_list_by_relations($rec_table, $rec_id, $relation=false) {
		$where = ($relation) ? "fr.id IS NOT NULL" : "fr.id IS NULL";
		$sql = "
				SELECT f.*, lvt.text AS title, fr.id AS relation_id
				FROM festivals f
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'festivals' AND lvt.table_id = f.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				LEFT JOIN festivals_relations fr ON fr.festival_id = f.id AND fr.rec_table = '" . addslashes($rec_table) . "' AND fr.rec_id = " . (int)$rec_id . "
				WHERE {$where}
				ORDER BY f.time_starts DESC
			";

		return execute_sql_query($sql, 'get all');
	}



	// Festivals List
	function get_relations_list_by_relations($rec_table, $rec_id) {
		$sql = "
				SELECT f.*, lvt.text AS title, fr.id AS relation_id
				FROM festivals_relations fr
				LEFT JOIN festivals f ON fr.festival_id = f.id
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'festivals' AND lvt.table_id = f.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE fr.rec_table = '" . addslashes($rec_table) . "' AND fr.rec_id = " . (int)$rec_id . "
				ORDER BY f.time_starts DESC
			";

		return execute_sql_query($sql, 'get all');
	}



	// Relations Item
	function get_relations_item($id) {
		$sql = "
				SELECT fr.*
				FROM festivals_relations fr
				WHERE fr.id =  " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}

	
	function check_festival_user_selected(): bool
	{
		if ($_SESSION['app']['festival']) {
			$data = $this->get_festivals_item_by_company_user($_SESSION['app']['festival'], $_SESSION['user']['id']);
			if ($data) {
				return true;
			}
		}
		return false;
	}
	
	
	function get_festivals_list_by_company_user($user_id, $active=true)
	{
		$where = ($active) ? "AND f.time_starts <= " . time() . " AND f.time_ends >= " . time() : "";
		$sql = "
				SELECT F.*,
				       F.id AS festival_id,
				       LVT.text AS title,
				       FC.app_type AS user_app_type
				FROM festivals_users_companies_users FU
				LEFT JOIN festivals_users_companies FC ON FU.company_id = FC.id
				LEFT JOIN festivals F ON FC.festival_id = F.id
				LEFT JOIN languages_varchar LVT ON LVT.table_name = 'festivals' AND LVT.table_id = F.id AND LVT.keyword = 'title' AND LVT.language = '" . $this->site_language . "'
				WHERE FU.user_id = " . (int)$user_id . " {$where}
				ORDER BY F.time_starts DESC
			";
		return execute_sql_query($sql, 'get all');
	}
	
	
	function get_festivals_item_by_company_user($festival_id, $user_id, $active=true)
	{
		$where = ($active) ? "AND f.time_starts <= " . time() . " AND f.time_ends >= " . time() : "";
		$sql = "
				SELECT F.*,
				       F.id AS festival_id,
				       LVT.text AS title,
				       FC.app_type AS user_app_type
				FROM festivals_users_companies_users FU
				LEFT JOIN festivals_users_companies FC ON FU.company_id = FC.id
				LEFT JOIN festivals F ON FC.festival_id = F.id
				LEFT JOIN languages_varchar LVT ON LVT.table_name = 'festivals' AND LVT.table_id = F.id AND LVT.keyword = 'title' AND LVT.language = '" . $this->site_language . "'
				WHERE FU.user_id = " . (int)$user_id . " AND F.id = " . (int)$festival_id . " {$where}
			";
		return execute_sql_query($sql, 'get row');
	}
	
	
	function get_festivals_users_prices_list($festival_id, $user_id)
	{
		$sql = "
				SELECT FP.*
				FROM festivals_users_companies_prices FP
				LEFT JOIN festivals_users_companies FC ON FP.company_id = FC.id AND FC.festival_id = " . (int)$festival_id . "
				LEFT JOIN festivals_users_companies_users FU ON FU.company_id = FC.id AND FU.user_id = " . (int)$user_id . "
				WHERE FC.festival_id = " . (int)$festival_id . " AND FU.user_id = " . (int)$user_id . "
				ORDER BY FP.price ASC
			";
		return execute_sql_query($sql, 'get all');
	}

	
	function check_checkout_by_request_id($request_id): bool
	{
		$time_to_check = 10; // seconds
		
		$sql = "
			SELECT CKT.id
			FROM festivals_checkout CKT
			WHERE CKT.request_id = '" . (int)$request_id . "' AND CKT.user_id = " . (int)$_SESSION['user']['id'] . "
				AND CKT.rec_time > " . (time() - $time_to_check) . "
		";
		$return = execute_sql_query($sql, 'get one');
		return (bool)$return;
	}

	
	function add_checkout_item($form)
	{
		$sql = make_insert_query('festivals_checkout', $form);
		return execute_sql_query($sql, 'insert');
	}

	
	function get_wallet_item($nfc_id)
	{
		$sql = "
			SELECT SUM(price) AS total_money
			FROM festivals_checkout CKT
			WHERE CKT.nfc_id = '" . addslashes($nfc_id) . "' AND CKT.festival_id = " . (int)$_SESSION['app']['festival'] . "
		";
		return execute_sql_query($sql, 'get one');
	}
	
	
	function get_checkout_list($from=0, $items=20, $params=[]) {
		$params = $this->get_checkout_list_params($params);
		
		$sql = "
				SELECT CKT.*, PRI.title AS price_title
				FROM festivals_checkout CKT
				LEFT JOIN festivals_users_companies_prices PRI ON PRI.id = CKT.price_id
				WHERE 1 " . $params['where'] . "
				ORDER BY {$params['order']}
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_checkout_list_cnt($params=array()) {
		$params = $this->get_checkout_list_params($params);
		
		$sql = "
				SELECT COUNT(CKT.id) AS cnt
				FROM festivals_checkout CKT
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_checkout_list_params($params) {
		$where = [];
		$order = [];
		
		if ($params['festival']) {
			$where[] = "CKT.festival_id = " . (int)$params['festival'];
		}
		
		if ($params['user']) {
			$where[] = "CKT.user_id = " . (int)$params['user'];
		}
		
		if (!$order) {
			$order[] = "CKT.id DESC ";
		}
		
		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		);
	}
	
	
	function get_checkout_sum_by_user($user_id, $festival_id)
	{
		$sql = "
			SELECT 0-SUM(CKT.price) AS total_money
			FROM festivals_checkout CKT
			WHERE CKT.festival_id = " . (int)$festival_id . " AND CKT.user_id = " . (int)$user_id . "
		";
		return execute_sql_query($sql, 'get one');
	}
	
	
	function get_checkout_sum_by_company($company_id, $festival_id)
	{
		$sql = "
			SELECT 0-SUM(CKT.price) AS total_money
			FROM festivals_checkout CKT
			LEFT JOIN festivals_users_companies_users FU ON FU.user_id = CKT.user_id
			LEFT JOIN festivals_users_companies FC ON FU.company_id = FC.id AND FC.festival_id = " . (int)$festival_id . "
			WHERE CKT.festival_id = " . (int)$festival_id . " AND FC.id = " . (int)$company_id . "
		";
		return execute_sql_query($sql, 'get one');
	}
	
	
}

