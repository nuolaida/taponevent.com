<?php
class Countries {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;

		if (file_exists($page_special_config['site_config_path'] . 'configs/countries.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/countries.config.php');
			$this->module_config = $countries_config;
		}
	}



	// Countries List
	function get_countries_list($from=0, $items=20, $params=array()) {
		$params = $this->get_countries_list_params($params);

		$sql = "
				SELECT c.*, lvt.text AS title
				FROM countries c
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'countries' AND lvt.table_id = c.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE 1 {$params['where']}
				ORDER BY title ASC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_countries_list_cnt($params=array()) {
		$params = $this->get_countries_list_params($params);

		$sql = "
				SELECT COUNT(c.id) AS cnt
				FROM countries c
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'countries' AND lvt.table_id = c.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE 1 {$params['where']}
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_countries_list_params($params=array()) {
		$where = '';
		if ($params['search']) {
			$where .= " AND lvt.text LIKE '%" . addslashes($params['search']) . "%'";
		}

		return array(
				'where' =>  $where,
			);
	}



	// Item
	function get_countries_item($id) {
		$sql = "
				SELECT rec.*, lvt.text AS title
				FROM countries rec
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'countries' AND lvt.table_id = rec.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE rec.id =  " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Update
	function update_countries_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'keyword' => strtolower($form['keyword']),
		];
		$sql = make_update_query('countries', $form_main, array('id' => $id));
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('countries', $id, 'title', $form['title']);
		}

		return $return;
	}



	// Add
	function add_countries_item($form) {
		global $TextLanguages;

		$form_main = [
			'keyword' => strtolower($form['keyword']),
		];
		$sql = make_insert_query('countries', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('countries', $id, 'title', $form['title']);
		}

		return $id;
	}



	// Cities List
	function get_cities_list($from=0, $items=20, $params=array()) {
		$params = $this->get_cities_list_params($params);

		$sql = "
				SELECT c.*, lvt.text AS title, lvt2.text AS country_title
				FROM cities c
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'cities' AND lvt.table_id = c.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				LEFT JOIN countries ctr ON c.country_id = ctr.id
				LEFT JOIN languages_varchar lvt2 ON lvt2.table_name = 'countries' AND lvt2.table_id = ctr.id AND lvt2.keyword = 'title' AND lvt2.language = '" . $this->site_language . "'
				WHERE 1 {$params['where']}
				ORDER BY title ASC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_cities_list_cnt($params=array()) {
		$params = $this->get_cities_list_params($params);

		$sql = "
				SELECT COUNT(c.id) AS cnt
				FROM cities c
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'cities' AND lvt.table_id = c.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE 1 {$params['where']}
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_cities_list_params($params=array()) {
		$where = '';

		if ($params['search']) {
			$where .= " AND lvt.text LIKE '%" . addslashes($params['search']) . "%'";
		}

		if ($params['country']) {
			$where .= " AND c.country_id = " . (int)$params['country'] . "";
		}

		return array(
			'where' =>  $where,
		);
	}



	// Item
	function get_cities_item($id) {
		$sql = "
				SELECT rec.*, lvt.text AS title
				FROM cities rec
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'cities' AND lvt.table_id = rec.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE rec.id =  " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Update
	function update_cities_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'country_id' => $form['country_id'],
		];
		$sql = make_update_query('cities', $form_main, array('id' => $id));
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('cities', $id, 'title', $form['title']);
		}

		return $return;
	}



	// Add
	function add_cities_item($form) {
		global $TextLanguages;

		$form_main = [
			'country_id' => $form['country_id'],
		];
		$sql = make_insert_query('cities', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('cities', $id, 'title', $form['title']);
		}

		return $id;
	}

}
?>