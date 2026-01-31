<?php
class Index {
	var $module_config, $site_language, $domain_keyword;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->domain_keyword = $page_special_config['keyword'];
		$this->site_language = $Translate->language;

		if (file_exists($page_special_config['site_config_path'] . 'configs/index.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/index.config.php');
			$this->module_config = $index_config;
		}
	}



	// List
	function get_list($keyword=null) {
		$where = '';
		if ($keyword) {
			$where = " AND keyword = '" . addslashes($keyword) . "'";
		}
		$sql = "
				SELECT i.*
				FROM index_page i
				WHERE i.domain_keyword = '" . $this->domain_keyword . "' {$where}
				ORDER BY i.rec_keyword ASC, i.rec_position ASC, RAND()
			";

		return execute_sql_query($sql, 'get all');
	}



	// List
	function get_languages_list($table_id = null) {
		$texts = [];

		$where = ($table_id) ? " AND l.table_id = " . (int)$table_id : '';

		$sql = "
				SELECT l.*
				FROM languages_text l
				LEFT JOIN index_page i ON i.id = l.table_id
				WHERE l.table_name = 'index_page' {$where} AND l.language = '" . $this->site_language . "' AND i.domain_keyword = '" . $this->domain_keyword . "'
			";
		$list = execute_sql_query($sql, 'get all');
		if ($list) {
			foreach ($list as $item) {
				$texts[$item['table_id']][$item['keyword']] = $item['text'];
			}
		}

		$sql = "
				SELECT l.*
				FROM languages_varchar l
				LEFT JOIN index_page i ON i.id = l.table_id
				WHERE l.table_name = 'index_page' {$where} AND l.language = '" . $this->site_language . "' AND i.domain_keyword = '" . $this->domain_keyword . "'
			";
		$list = execute_sql_query($sql, 'get all');
		if ($list) {
			foreach ($list as $item) {
				$texts[$item['table_id']][$item['keyword']] = $item['text'];
			}
		}

		return $texts;
	}



	// List
	function get_list_grouped($keyword=null) {
		$list = $this->get_list($keyword);
		$list_languages = $this->get_languages_list();

		$return = [];
		if ($list) {
			foreach ($list as $item) {
				$return[$item['rec_keyword']][$item['id']] = [];
				$data_json = json_decode($item['data_json'], true);
				if ($data_json) {
					foreach ($data_json as $key_json => $value_json) {
						if (substr($key_json, 0, strlen('text_')) == 'text_') {
							$key = substr($key_json, strlen('text_'));
							$value = $list_languages[$item['id']][$key];
						} else if (substr($key_json, 0, strlen('varchar_')) == 'varchar_') {
							$key = substr($key_json, strlen('varchar_'));
							$value = $list_languages[$item['id']][$key];
						} else {
							$key = $key_json;
							$value = $value_json;
						}
						$return[$item['rec_keyword']][$item['id']][$key_json] = $value;
					}
				}
			}
		}

		return $return;
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT i.*
				FROM index_page i
				WHERE i.id =  " . (int)$id . " AND i.domain_keyword = '" . $this->domain_keyword . "'
			";
		$data = execute_sql_query($sql, 'get row');

		if ($data) {
			$list_languages = $this->get_languages_list($data['id']);
			$data_json = json_decode($data['data_json'], true);
			if ($data_json) {
				foreach ($data_json as $key_json => $value_json) {
					if (substr($key_json, 0, strlen('text_')) == 'text_') {
						$key = substr($key_json, strlen('text_'));
						$value = $list_languages[$data['id']][$key];
					} else if (substr($key_json, 0, strlen('varchar_')) == 'varchar_') {
						$key = substr($key_json, strlen('varchar_'));
						$value = $list_languages[$data['id']][$key];
					} else {
						//$key = $key_json;
						$value = $value_json;
					}
					$data[$key_json] = $value;
				}
			}
		}

		return $data;
	}



	// Update
	function update_item($id, $form) {
		global $TextLanguages;

		$varchar = [];
		$text = [];
		$form_json = [];

		if ($form['rec_position']) {
			$position = $form['rec_position'];
			unset($form['rec_position']);
		}

		foreach ($form as $key => $value) {
			if (substr($key, 0, strlen('text_')) == 'text_') {
				$this_key = substr($key, strlen('text_'));
				$text[$this_key] = $value;
				$form_json[$key] = '';
			} else if (substr($key, 0, strlen('varchar_')) == 'varchar_') {
				$this_key = substr($key, strlen('varchar_'));
				$varchar[$this_key] = $value;
				$form_json[$key] = '';
			} else {
				$form_json[$key] = $value;
			}
		}

		$form5 = [
			'rec_position' => $position,
			'data_json'   => json_encode($form_json),
		];
		$sql = make_update_query('index_page', $form5, ['id' => $id], ['data_json']);
		execute_sql_query($sql, "update");

		foreach ($varchar as $key => $value) {
			$TextLanguages->update_varchar('index_page', $id, $key, $value);
		}

		foreach ($text as $key => $value) {
			$TextLanguages->update_text('index_page', $id, $key, $value);
		}

		return true;
	}



	// Add
	function add_item($form, $keyword=null) {
		global $TextLanguages;

		$varchar = [];
		$text = [];
		$form_json = [];

		if ($form['rec_position']) {
			$position = $form['rec_position'];
			unset($form['rec_position']);
		}

		foreach ($form as $key => $value) {
			if (substr($key, 0, strlen('text_')) == 'text_') {
				$this_key = substr($key, strlen('text_'));
				$text[$this_key] = $value;
				$form_json[$key] = '';
			} else if (substr($key, 0, strlen('varchar_')) == 'varchar_') {
				$this_key = substr($key, strlen('varchar_'));
				$varchar[$this_key] = $value;
				$form_json[$key] = '';
			} else {
				$form_json[$key] = $value;
			}
		}

		$form5 = [
				'rec_keyword' => $keyword,
				'rec_position' => $position,
				'data_json'   => json_encode($form_json),
				'domain_keyword' => $this->domain_keyword,
			];
		$sql = make_insert_query('index_page', $form5, ['data_json']);
		$id = execute_sql_query($sql, 'insert');

		foreach ($varchar as $key => $value) {
			$TextLanguages->update_varchar('index_page', $id, $key, $value);
		}

		foreach ($text as $key => $value) {
			$TextLanguages->update_text('index_page', $id, $key, $value);
		}

		return $id;
	}



	// Delete
	function delete_item($id) {
		$sql = " DELETE FROM index_page WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");

	}


}
?>