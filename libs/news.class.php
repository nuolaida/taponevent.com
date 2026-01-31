<?php
class News {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/news.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/news.config.php');
			$this->module_config = $news_config;
		}
	}



	// Beer list
	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT n.*, lvt.text AS title
				FROM news n
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'news' AND lvt.table_id = n.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE n.language = '" . $this->site_language . "' " . $params['where'] . "
				ORDER BY n.id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(n.id) AS cnt
				FROM news n
				LEFT JOIN languages_varchar lvt ON lvt.table_name = 'news' AND lvt.table_id = n.id AND lvt.keyword = 'title' AND lvt.language = '" . $this->site_language . "'
				WHERE n.language = '" . $this->site_language . "' " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_list_params($params) {
		$where = array();

		if ($params['search']) {
			$where[] = " lvt.text LIKE '%" . addslashes($params['search']) . "%' ";
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
		);
	}

	// Update
	function update_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'rec_time'      => ((int)$form['rec_time']) ? $form['rec_time'] : null,
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
		];
		$sql = make_update_query('news', $form_main, array('id' => $id));
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('news', $id, 'title', $form['title']);
			$TextLanguages->update_text('news', $id, 'short_description', $form['short_description']);
			$TextLanguages->update_text('news', $id, 'description', $form['description']);
		}

		return $return;
	}



	// Add
	function add_item($form) {
		global $TextLanguages;

		$form_main = [
			'language'      => $this->site_language,
			'rec_time'      => ((int)$form['rec_time']) ? $form['rec_time'] : null,
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
		];
		$sql = make_insert_query('news', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('news', $id, 'title', $form['title']);
			$TextLanguages->update_text('news', $id, 'short_description', $form['short_description']);
			$TextLanguages->update_text('news', $id, 'description', $form['description']);
		}

		return $id;
	}




}
?>