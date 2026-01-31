<?php
class Conference {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/conference.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/conference.config.php');
			$this->module_config = $conference_config;
		}
	}



	// List
	function get_list($from=0, $items=20, $params=[]) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT cl.*, cltitle.text AS conference_title, cllanguage.text AS conference_language,
				       cs.name AS speaker_name, cs.gallery_id AS speaker_gallery_id, cstitle.text AS speaker_company,
				       ftitle.text AS festival_title
				FROM conference_list cl
				LEFT JOIN languages_varchar cltitle ON cltitle.table_name = 'conference_list' AND cltitle.table_id = cl.id AND cltitle.keyword = 'title' AND cltitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_varchar cllanguage ON cllanguage.table_name = 'conference_list' AND cllanguage.table_id = cl.id AND cllanguage.keyword = 'language' AND cllanguage.language = '" . $this->site_language . "'
				LEFT JOIN conference_speakers cs ON cs.id = cl.speaker_id
				LEFT JOIN languages_varchar cstitle ON cstitle.table_name = 'conference_speakers' AND cstitle.table_id = cs.id AND cstitle.keyword = 'company' AND cstitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals f ON f.id = cl.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(cl.id) AS cnt
				FROM conference_list cl
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_list_params($params) {
		$where = [];
		
		if ($params['festival']) {
			if ($params['festival'] == 'active') {
				require_once(LIBS_MAIN_PATH . 'festivals.class.php');
				$Festivals = new Festivals();
				$data_festival = $Festivals->get_festivals_list(['is_active' => true]);
				if ($data_festival) {
					$data_festival = reset($data_festival)['id'];
					$where[] = " cl.festival_id = " . (int)$data_festival . " ";
				}
			} else {
				$where[] = " cl.festival_id = " . (int)$params['festival'] . " ";
			}
		}
		
		
		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'title':
					$order[] = " list_title ASC";
					break;
			}
		}
		if (!$order) {
			$order[] = " cl.rec_time ASC ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		];
	}
	

	
	// Update
	function update_item($id, $form) {
		global $TextLanguages;
		
		$form['rec_time'] = (trim($form['rec_time'])) ? strtotime(trim($form['rec_time'])) : null;
		$form['speaker_id'] = ((int)$form['speaker_id']) ?: null;
		$form['festival_id'] = ((int)$form['festival_id']) ?: null;
		$form2['title'] = $form['title'];
		unset($form['title']);
		$form2['language'] = $form['language'];
		unset($form['language']);

		$sql = make_update_query('conference_list', $form, ['id' => $id]);
		$return = execute_sql_query($sql, "update");
		
		if ($return) {
			$TextLanguages->update_varchar('conference_list', $id, 'title', $form2['title']);
			$TextLanguages->update_varchar('conference_list', $id, 'language', $form2['language']);
		}

		return $return;
	}



	// Add
	function add_item($form) {
		global $TextLanguages;

		$form['rec_time'] = (trim($form['rec_time'])) ? strtotime(trim($form['rec_time'])) : null;
		$form['speaker_id'] = ((int)$form['speaker_id']) ?: null;
		$form['festival_id'] = ((int)$form['festival_id']) ?: null;
		$form2['title'] = $form['title'];
		unset($form['title']);
		$form2['language'] = $form['language'];
		unset($form['language']);
		
		$sql = make_insert_query('conference_list', $form);
		$id = execute_sql_query($sql, "insert");
		
		if ($id) {
			$TextLanguages->update_varchar('conference_list', $id, 'title', $form2['title']);
			$TextLanguages->update_varchar('conference_list', $id, 'language', $form2['language']);
		}

		return $id;
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT cl.*, cltitle.text AS conference_title, cllanguage.text AS conference_language,
				       cs.name AS speaker_name, cstitle.text AS speaker_company,
				       ftitle.text AS festival_title
				FROM conference_list cl
				LEFT JOIN languages_varchar cltitle ON cltitle.table_name = 'conference_list' AND cltitle.table_id = cl.id AND cltitle.keyword = 'title' AND cltitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_varchar cllanguage ON cllanguage.table_name = 'conference_list' AND cllanguage.table_id = cl.id AND cllanguage.keyword = 'language' AND cllanguage.language = '" . $this->site_language . "'
				LEFT JOIN conference_speakers cs ON cs.id = cl.speaker_id
				LEFT JOIN languages_varchar cstitle ON cstitle.table_name = 'conference_speakers' AND cstitle.table_id = cs.id AND cstitle.keyword = 'company' AND cstitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals f ON f.id = cl.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE cl.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}
	
	
	
	// Delete
	function delete_item($id) {
		if (!(int)$id) {
			return false;
		}
		
		$sql = "DELETE FROM conference_list WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}
	
	
	
	
	// List
	function get_speakers_list($from=0, $items=20, $params=[]) {
		$params = $this->get_speakers_list_params($params);
		
		$sql = "
				SELECT cs.*, cstitle.text AS speaker_company
				FROM conference_speakers cs
				LEFT JOIN languages_varchar cstitle ON cstitle.table_name = 'conference_speakers' AND cstitle.table_id = cs.id AND cstitle.keyword = 'company' AND cstitle.language = '" . $this->site_language . "'
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_speakers_list_cnt($params=array()) {
		$params = $this->get_speakers_list_params($params);
		
		$sql = "
				SELECT COUNT(cs.id) AS cnt
				FROM conference_speakers cs
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_speakers_list_params($params) {
		$where = [];
		
		if ($params['name']) {
			$where[] = " cs.name LIKE '%" . addslashes(trim($params['name'])) . "%'";
		}
		
		
		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'id':
					$order[] = " cs.id ASC";
					break;
			}
		}
		if (!$order) {
			$order[] = " cs.name ASC ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		];
	}
	
	
	
	// Update
	function update_speakers_item($id, $form) {
		global $TextLanguages;
		
		$form['name'] = (trim($form['name'])) ?: null;
		$form['gallery_id'] = ((int)$form['gallery_id']) ?: null;
		$form['email'] = (trim($form['email'])) ?: null;
		$form2['company'] = $form['company'];
		unset($form['company']);
		
		$sql = make_update_query('conference_speakers', $form, ['id' => $id]);
		$return = execute_sql_query($sql, "update");
		
		if ($return) {
			$TextLanguages->update_varchar('conference_speakers', $id, 'company', $form2['company']);
		}
		
		return $return;
	}
	
	
	
	// Add
	function add_speakers_item($form) {
		global $TextLanguages;
		
		$form['name'] = (trim($form['name'])) ?: null;
		$form['gallery_id'] = ((int)$form['gallery_id']) ?: null;
		$form['email'] = (trim($form['email'])) ?: null;
		$form2['company'] = $form['company'];
		unset($form['company']);
		
		$sql = make_insert_query('conference_speakers', $form);
		$id = execute_sql_query($sql, "insert");
		
		if ($id) {
			$TextLanguages->update_varchar('conference_speakers', $id, 'company', $form2['company']);
		}
		
		return $id;
	}
	
	
	
	// Item
	function get_speakers_item($id) {
		$sql = "
				SELECT cs.*, cstitle.text AS speaker_company
				FROM conference_speakers cs
				LEFT JOIN languages_varchar cstitle ON cstitle.table_name = 'conference_speakers' AND cstitle.table_id = cs.id AND cstitle.keyword = 'company' AND cstitle.language = '" . $this->site_language . "'
				WHERE cs.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	
	
	
	// Delete
	function delete_speakers_item($id) {
		if (!(int)$id) {
			return false;
		}
		
		$sql = "DELETE FROM conference_speakers WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}
	
	
	
	function update_newsletter($speaker_id) {
		require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
		$Newsletters = new Newsletters2;
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		$key = 'conference-speakers-id';
		
		// latest fest
		$data_festival = $Festivals->get_festivals_item_latest();
		if (!$data_festival) {
			return false;
		}
		$key .= $data_festival['id'];
		
		// speaker
		$data_speaker = $this->get_speakers_item($speaker_id);
		if (!$data_speaker || !trim($data_speaker['email'])) {
			return false;
		}
		
		// is this year speaker
		$sql = "
			SELECT cl.id
			FROM conference_list cl
			LEFT JOIN conference_speakers cs ON cs.id = cl.speaker_id
			WHERE cl.festival_id = " . (int)$data_festival['id'] . " AND cs.id = " . (int)$data_speaker['id'] . "
		";
		$is_this_festival_speaker = execute_sql_query($sql, 'get row');
		if (!$is_this_festival_speaker) {
			return false;
		}
		
		$data_newsletter_category = $Newsletters->check_add_categories_item_by_key($key);
		$Newsletters->check_add_relations_users_categories_with_email($data_newsletter_category['id'], $data_speaker['email']);
		
		return true;
	}
	
}
