<?php
class Participants {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/participants.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/participants.config.php');
			$this->module_config = $participants_config;
		}
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT b.*, txtitle.text AS title, txdshort.text AS description_short, txdescr.text AS description
				FROM participants b
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'participants' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_varchar txdshort ON txdshort.table_name = 'participants' AND txdshort.table_id = b.id AND txdshort.keyword = 'description_short' AND txdshort.language = '" . $this->site_language . "'
				LEFT JOIN languages_text txdescr ON txdescr.table_name = 'participants' AND txdescr.table_id = b.id AND txdescr.keyword = 'description' AND txdescr.language = '" . $this->site_language . "'
				WHERE b.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Beer list
	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT b.*, txtitle.text AS title, txdshort.text AS description_short
				FROM participants b
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'participants' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_varchar txdshort ON txdshort.table_name = 'participants' AND txdshort.table_id = b.id AND txdshort.keyword = 'description_short' AND txdshort.language = '" . $this->site_language . "'
				LEFT JOIN festivals_relations festrel ON festrel.rec_table = 'participants' AND festrel.rec_id = b.id {$params['on_join']}
				WHERE 1 " . $params['where'] . "
				GROUP BY b.id
				ORDER BY {$params['order']}
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(b.id) AS cnt
				FROM participants b
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'participants' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_list_params($params) {
		$where = [];
		$on_join = [];
		$order = [];

		if ($params['search']) {
			$where[] = " txtitle.text LIKE '%" . addslashes($params['search']) . "%' ";
		}

		if (isset($params['festival'])) {
			if ($params['festival'] == 'active') {
				require_once(LIBS_MAIN_PATH . 'festivals.class.php');
				$Festivals = new Festivals();
				$data_festival = $Festivals->get_festivals_list(['is_active' => true]);
				if ($data_festival) {
					$data_festival = reset($data_festival);
					$festival_id = $data_festival['id'];
				}
			} else {
				$festival_id = (int)$params['festival'];
			}
			if ($festival_id) {
				$where[] = " festrel.id IS NOT NULL ";
				$on_join[] = " festrel.festival_id = " . (int)$festival_id . " ";
			}
		}

		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'rand':
					$order[] = " RAND() ";
					break;
			}
		}
		if (!$order) {
			$order[] = " b.id DESC ";
		}


		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'on_join' => (count($on_join)) ? (' AND ' . implode(' AND ', $on_join)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		);
	}



	// Beer list
	function get_list_relations_gallery($gallery_id) {

		$sql = "
				SELECT b.*, txtitle.text AS title, rel.id AS relation_id
				FROM gallery_relations rel
				LEFT JOIN participants b ON rel.rec_id = b.id
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'participants' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				WHERE rel.rec_table = 'participants' AND rel.gallery_id = " . (int)$gallery_id . "
				ORDER BY txtitle.text ASC
				LIMIT 0, 500
			";

		return execute_sql_query($sql, 'get all');
	}



	// Beer list
	function get_list_relations_gallery_free($gallery_id) {

		$sql = "
				SELECT b.*, txtitle.text AS title
				FROM participants b
				LEFT JOIN gallery_relations rel ON rel.rec_id = b.id AND rel.rec_table = 'participants' AND rel.gallery_id = " . (int)$gallery_id . "
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'participants' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				WHERE rel.id IS NULL
				ORDER BY txtitle.text ASC
				LIMIT 0, 500
			";

		return execute_sql_query($sql, 'get all');
	}



	// Update
	function update_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'email'    => (trim($form['email'])) ?: null,
			'gallery_id'    => ((int)$form['gallery_id']) ?: null,
		];
		$sql = make_update_query('participants', $form_main, ['id' => $id]);
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('participants', $id, 'title', $form['title']);
			$TextLanguages->update_varchar('participants', $id, 'description_short', $form['description_short']);
			$TextLanguages->update_text('participants', $id, 'description', $form['description']);
		}

		return $return;
	}



	// Add
	function add_item($form) {
		global $TextLanguages;

		$form_main = [
			'email'    => (trim($form['email'])) ?: null,
			'gallery_id'    => ((int)$form['gallery_id']) ?: null,
		];
		$sql = make_insert_query('participants', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('participants', $id, 'title', $form['title']);
			$TextLanguages->update_varchar('participants', $id, 'description_short', $form['description_short']);
			$TextLanguages->update_text('participants', $id, 'description', $form['description']);
		}

		return $id;
	}
	
	
	
	function update_newsletter($participant_id) {
		require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
		$Newsletters = new Newsletters2;
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		$key = 'participants-id';
		
		// latest fest
		$data_festival = $Festivals->get_festivals_item_latest();
		if (!$data_festival) {
			return false;
		}
		$key .= $data_festival['id'];
		
		// participant
		$data_participant = $this->get_item($participant_id);
		if (!$data_participant || !trim($data_participant['email'])) {
			return false;
		}
		
		// is this year attending
		$sql = "
			SELECT fr.id
			FROM festivals_relations fr
			WHERE fr.festival_id = " . (int)$data_festival['id'] . " AND fr.rec_table = 'participants' AND fr.rec_id = " . (int)$data_participant['id'] . "
		";
		$is_this_festival_attender = execute_sql_query($sql, 'get row');
		if (!$is_this_festival_attender) {
			return false;
		}
		
		$data_newsletter_category = $Newsletters->check_add_categories_item_by_key($key);
		$Newsletters->check_add_relations_users_categories_with_email($data_newsletter_category['id'], $data_participant['email']);
		
		return true;
	}




}
?>