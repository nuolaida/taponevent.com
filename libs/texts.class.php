<?php
class Texts {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/texts.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/texts.config.php');
			$this->module_config = $texts_config;
		}
	}



	// Beer list
	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT t.*, lvt.text AS title
				FROM texts t
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'texts' AND lvt.table_id = t.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE 1  " . $params['where'] . "
				ORDER BY t.is_active DESC, lvt.text ASC, t.id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(t.id) AS cnt
				FROM texts t
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'texts' AND lvt.table_id = t.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE 1  " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_list_params($params) {
		$where = array();

		if ($params['search']) {
			$where[] = " lvt.text LIKE '%" . addslashes($params['search']) . "%' ";
		}

		if ($params['active']) {
			$where[] = " t.is_active = 1 ";
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
		);
	}



	function get_list_associative() {
		$sql = "
				SELECT t.*, lvt.text AS title
				FROM texts t
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'texts' AND lvt.table_id = t.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE t.is_active = 1
				ORDER BY lvt.text ASC, t.id DESC
				LIMIT 0, 100
			";
		$list = execute_sql_query($sql, 'get all');
		$return = [];
		if ($list) {
			foreach ($list as $item) {
				$return[$item['id']] = $item;
			}
		}

		return $return;
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT t.*, lvt.text AS title, ltd.text AS description,
					COALESCE(lvt.language, ltd.language) AS lang
				FROM texts t
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'texts' AND lvt.table_id = t.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				LEFT JOIN languages_text ltd ON ltd.table_name = 'texts' AND ltd.table_id = t.id AND ltd.keyword = 'description' AND ltd.language = '" . $this->site_language . "'
				WHERE t.id =  " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Update
	function update_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'is_active'     => ($form['is_active']) ? 1 : 0,
			'gallery_id'    => ($form['gallery_id']) ?: null,
			'template'    => (trim($form['template'])) ?: null,
		];
		$sql = make_update_query('texts', $form_main, array('id' => $id));
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('texts', $id, 'title', $form['title']);
			$TextLanguages->update_text('texts', $id, 'description', $form['description']);
		}

		return $return;
	}



	// Add
	function add_item($form) {
		global $TextLanguages;

		$form_main = [
			'is_active'     => ($form['is_active']) ? 1 : 0,
			'gallery_id'    => ($form['gallery_id']) ?: null,
			'template'    => (trim($form['template'])) ?: null,
		];
		$sql = make_insert_query('texts', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('texts', $id, 'title', $form['title']);
			$TextLanguages->update_text('texts', $id, 'description', $form['description']);
		}

		return $id;
	}




}
?>