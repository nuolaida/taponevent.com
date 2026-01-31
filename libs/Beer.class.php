<?php
class Beer {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/beer.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/beer.config.php');
			$this->module_config = $beer_config;
		}
	}



	// List
	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT b.*, brtitle.text AS brewery_title, ftitle.text AS festival_title
				FROM beer b
				LEFT JOIN breweries br ON br.id = b.brewery_id
				LEFT JOIN languages_varchar brtitle ON brtitle.table_name = 'breweries' AND brtitle.table_id = br.id AND brtitle.keyword = 'title' AND brtitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals f ON f.id = b.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE 1 " . $params['where'] . "
				ORDER BY b.festival_id DESC, b.festival_session_number ASC, b.title ASC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(b.id) AS cnt
				FROM beer b
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_list_params($params) {
		$where = [];
		
		if ($params['brewery_id']) {
			$where[] = " b.brewery_id = " . (int)$params['brewery_id'] . " ";
		}
		
		if ($params['festival_id']) {
			$where[] = " b.festival_id = " . (int)$params['festival_id'] . " ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
		];
	}
	function get_list_grouped_by_festival($from=0, $items=20, $params=[]) {
		$return = [];
		
		$list = $this->get_list($from, $items, $params);
		if ($list) {
			foreach ($list as $item) {
				$return[$item['festival_id']][] = $item;
			}
		}
		
		return $return;
	}
	function get_list_grouped_by_brewery($from=0, $items=20, $params=[]) {
		$return = [];
		
		$list = $this->get_list($from, $items, $params);
		if ($list) {
			foreach ($list as $item) {
				$return[$item['brewery_id']][] = $item;
			}
		}
		
		return $return;
	}



	// Update
	function update_item($id, $form) {
		$form['title'] = (trim($form['title'])) ? trim($form['title']) : null;
		$form['style'] = (trim($form['style'])) ? trim($form['style']) : null;
		$form['description'] = (trim($form['description'])) ? trim($form['description']) : null;
		$form['festival_session_number'] = (trim($form['festival_session_number'])) ? trim($form['festival_session_number']) : null;
		$form['abv'] = (trim($form['abv'])) ? update_decimal_numbers($form['abv']) : null;

		$sql = make_update_query('beer', $form, ['id' => $id]);
		$return = execute_sql_query($sql, "update");

		return $return;
	}



	// Add
	function add_item($form) {
		$form['title'] = (trim($form['title'])) ? trim($form['title']) : null;
		$form['style'] = (trim($form['style'])) ? trim($form['style']) : null;
		$form['description'] = (trim($form['description'])) ? trim($form['description']) : null;
		$form['festival_session_number'] = (trim($form['festival_session_number'])) ? trim($form['festival_session_number']) : null;
		$form['abv'] = (trim($form['abv'])) ? update_decimal_numbers($form['abv']) : null;

		$sql = make_insert_query('beer', $form);
		$id = execute_sql_query($sql, "insert");

		return $id;
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT b.*, brtitle.text AS brewery_title, ftitle.text AS festival_title
				FROM beer b
				LEFT JOIN breweries br ON br.id = b.brewery_id
				LEFT JOIN languages_varchar brtitle ON brtitle.table_name = 'breweries' AND brtitle.table_id = br.id AND brtitle.keyword = 'title' AND brtitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals f ON f.id = b.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE b.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}
	
	
	
	// Delete
	function delete_item($id) {
		if (!(int)$id) {
			return false;
		}
		
		$sql = "DELETE FROM beer WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}

}
?>