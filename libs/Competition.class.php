<?php
class Competition {
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
				SELECT ci.*,
				       co.title AS owner_title,
				       ftitle.text AS festival_title,
				       medaltitle.text AS medal_categories_title
				FROM competition_items ci
				LEFT JOIN competition_owners co ON co.id = ci.owner_id
				LEFT JOIN festivals f ON f.id = ci.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				LEFT JOIN competition_medal_categories medalc ON medalc.id = ci.medal_categories_id
				LEFT JOIN languages_varchar medaltitle ON medaltitle.table_name = 'competition_medal_categories' AND medaltitle.table_id = medalc.id AND medaltitle.keyword = 'title' AND medaltitle.language = '" . $this->site_language . "'
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=[]) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(ci.id) AS cnt
				FROM competition_items ci
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
					$where[] = " ci.festival_id = " . (int)$data_festival . " ";
				}
			} else {
				$where[] = " ci.festival_id = " . (int)$params['festival'] . " ";
			}
		}
		
		if ((int)$params['owner']) {
			$where[] = " ci.owner_id = " . (int)$params['owner'] . " ";
		}
		
		if ($params['medal']) {
			if ($params['medal'] === true) {
				$where[] = " ci.medal IS NOT NULL ";
			} else {
				$where[] = " ci.medal = " . (int)$params['medal'] . " ";
			}
			$order[] = " ci.medal_categories_id ASC";
			$order[] = " ci.medal ASC";
		}
		
		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'title':
					$order[] = " ci.title ASC";
					break;
				case 'category':
					$order[] = " ci.category ASC";
					$order[] = " ci.sweetness ASC";
					$order[] = " ci.abv ASC";
					break;
			}
		}
		if (!$order) {
			$order[] = " ci.id DESC ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(', ', $order)) : '',
		];
	}
	

	
	// Update
	function update_item($id, $form) {
		global $TextLanguages;
		
		$form['owner_id'] = ((int)$form['owner_id']) ?: null;
		$form['title'] = (trim($form['title'])) ?: null;
		$form['ingredients'] = (trim($form['ingredients'])) ?: null;
		$form['category'] = (trim($form['category'])) ?: null;
		$form['abv'] = (trim($form['abv'])) ? update_decimal_numbers($form['abv']) : null;
		$form['sweetness'] = ((int)$form['sweetness']) ?: null;
		$form['carbonation'] = ((int)$form['carbonation']) ?: null;
		$form['gen_id'] = $this->gen_item_id($id);
		$form['medal'] = ((int)$form['medal']) ?: null;
		$form['medal_categories_id'] = ((int)$form['medal_categories_id']) ?: null;

		$sql = make_update_query('competition_items', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}



	// Add
	function add_item($form) {
		global $TextLanguages;
		
		$form['festival_id'] = ((int)$form['festival_id']) ?: null;
		$form['owner_id'] = ((int)$form['owner_id']) ?: null;
		$form['title'] = (trim($form['title'])) ?: null;
		$form['ingredients'] = (trim($form['ingredients'])) ?: null;
		$form['category'] = (trim($form['category'])) ?: null;
		$form['abv'] = (trim($form['abv'])) ? update_decimal_numbers($form['abv']) : null;
		$form['sweetness'] = ((int)$form['sweetness']) ?: null;
		$form['carbonation'] = ((int)$form['carbonation']) ?: null;
		$form['gen_id'] = $this->gen_item_id();
		$form['medal'] = ((int)$form['medal']) ?: null;
		$form['medal_categories_id'] = ((int)$form['medal_categories_id']) ?: null;
		
		$sql = make_insert_query('competition_items', $form);
		return execute_sql_query($sql, "insert");
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT ci.*,
				       co.title AS owner_title,
				       ftitle.text AS festival_title
				FROM competition_items ci
				LEFT JOIN competition_owners co ON co.id = ci.owner_id
				LEFT JOIN festivals f ON f.id = ci.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE ci.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}
	
	
	
	function gen_item_id($id=false) {
		if ($id) {
			$data = $this->get_item($id);
			if ($data['gen_id']) {
				return $data['gen_id'];
			}
		}
		
		while (!$gen_id) {
			$gen_id = mt_rand(10000, 99999);
			$sql = "SELECT * FROM competition_items WHERE gen_id = " . (int)$gen_id;
			$row = execute_sql_query($sql, 'get row');
			if ($row) {
				unset($gen_id);
			}
		}
		return $gen_id;
	}
	
	
	
	// Delete
	function delete_item($id) {
		if (!(int)$id) {
			return false;
		}
		
		$sql = "DELETE FROM competition_items WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}
	
	
	
	
	// List
	function get_owners_list($from=0, $items=20, $params=[]) {
		$params = $this->get_owners_list_params($params);
		
		$sql = "
				SELECT co.*
				FROM competition_owners co
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_owners_list_cnt($params=array()) {
		$params = $this->get_owners_list_params($params);
		
		$sql = "
				SELECT COUNT(co.id) AS cnt
				FROM competition_owners co
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_owners_list_params($params) {
		$where = [];
		
		if ($params['name']) {
			$where[] = " CONCAT(co.title, ' ', co.real_name) LIKE '%" . addslashes(trim($params['name'])) . "%'";
		}
		
		
		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'id':
					$order[] = " co.id ASC";
					break;
			}
		}
		if (!$order) {
			$order[] = " co.title ASC ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		];
	}
	
	
	
	// Update
	function update_owners_item($id, $form) {
		global $TextLanguages;
		
		$form['title'] = (trim($form['title'])) ?: null;
		$form['real_name'] = (trim($form['real_name'])) ?: null;
		$form['email'] = (trim($form['email'])) ?: null;
		$form['phone'] = (strlen(trim($form['phone'])) < 11) ? null : trim($form['phone']);
		$form['is_professional'] = (bool)$form['is_professional'];
		
		$sql = make_update_query('competition_owners', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}
	
	
	
	// Add
	function add_owners_item($form) {
		global $TextLanguages;
		
		$form['title'] = (trim($form['title'])) ?: null;
		$form['real_name'] = (trim($form['real_name'])) ?: null;
		$form['email'] = (trim($form['email'])) ?: null;
		$form['phone'] = (strlen(trim($form['phone'])) < 11) ? null : trim($form['phone']);
		$form['is_professional'] = (bool)$form['is_professional'];
		
		$sql = make_insert_query('competition_owners', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	
	// Item
	function get_owners_item($id) {
		$sql = "
				SELECT co.*
				FROM competition_owners co
				WHERE co.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	
	
	
	// Delete
	function delete_owners_item($id) {
		if (!(int)$id) {
			return false;
		}
		
		$list_items = $this->get_list(0, 1, ['owner' => $id]);
		
		if (!$list_items) {
			$sql = "DELETE FROM competition_owners WHERE id = " . (int)$id;
			return execute_sql_query($sql, "delete");
		}
		
		return false;
	}
	
	
	
	function update_newsletter_owners($owner_id) {
		require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
		$Newsletters = new Newsletters2;
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		$key = 'competition-owners-id';
		
		// latest fest
		$data_festival = $Festivals->get_festivals_item_latest();
		if (!$data_festival) {
			return false;
		}
		$key .= $data_festival['id'];
		
		// owner
		$data_owner = $this->get_owners_item($owner_id);
		if (!$data_owner || !trim($data_owner['email'])) {
			return false;
		}
		
		// is this year speaker
		$sql = "
			SELECT ci.id
			FROM competition_items ci
			LEFT JOIN competition_owners co ON co.id = ci.owner_id
			WHERE ci.festival_id = " . (int)$data_festival['id'] . " AND ci.owner_id = " . (int)$data_owner['id'] . "
		";
		$is_this_competition_owner = execute_sql_query($sql, 'get row');
		if (!$is_this_competition_owner) {
			return false;
		}
		
		$data_newsletter_category = $Newsletters->check_add_categories_item_by_key($key);
		$Newsletters->check_add_relations_users_categories_with_email($data_newsletter_category['id'], $data_owner['email']);
		
		return true;
	}
	
	
	
	
	// List
	function get_judges_list($from=0, $items=20, $params=[]) {
		$params = $this->get_judges_list_params($params);
		
		$sql = "
				SELECT co.*
				FROM competition_judges co
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_judges_list_cnt($params=[]) {
		$params = $this->get_owners_list_params($params);
		
		$sql = "
				SELECT COUNT(co.id) AS cnt
				FROM competition_judges co
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_judges_list_params($params) {
		$where = [];
		
		if ($params['name']) {
			$where[] = " co.name LIKE '%" . addslashes(trim($params['name'])) . "%'";
		}
		
		
		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'id':
					$order[] = " co.id ASC";
					break;
			}
		}
		if (!$order) {
			$order[] = " co.name ASC ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		];
	}
	
	
	function get_judges_list_of_the_festival($festival_id) {
		$sql = "
				SELECT cj.*
				FROM festivals_relations festrel
				LEFT JOIN competition_judges cj ON cj.id = festrel.rec_id
				WHERE festrel.rec_table = 'competition_judges' AND festrel.festival_id = " . (int)$festival_id . "
				ORDER BY RAND()
			";
		return execute_sql_query($sql, 'get all');
	}
	
	// Update
	function update_judges_item($id, $form) {
		global $TextLanguages;
		
		$form['name'] = (trim($form['name'])) ?: null;
		$form['email'] = (trim($form['email'])) ?: null;
		$form['description'] = (trim($form['description'])) ?: null;
		$form['gallery_id'] = (int)$form['gallery_id'] ?: null;
		$form['social_web'] = (trim($form['social_web'])) ?: null;
		$form['social_linkedin'] = (trim($form['social_linkedin'])) ?: null;
		$form['social_facebook'] = (trim($form['social_facebook'])) ?: null;
		$form['social_instagram'] = (trim($form['social_instagram'])) ?: null;
		$form['social_youtube'] = (trim($form['social_youtube'])) ?: null;
		
		$sql = make_update_query('competition_judges', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}
	
	
	
	// Add
	function add_judges_item($form) {
		global $TextLanguages;
		
		$form['name'] = (trim($form['name'])) ?: null;
		$form['email'] = (trim($form['email'])) ?: null;
		$form['description'] = (trim($form['description'])) ?: null;
		$form['gallery_id'] = (int)$form['gallery_id'] ?: null;
		$form['social_web'] = (trim($form['social_web'])) ?: null;
		$form['social_linkedin'] = (trim($form['social_linkedin'])) ?: null;
		$form['social_facebook'] = (trim($form['social_facebook'])) ?: null;
		$form['social_instagram'] = (trim($form['social_instagram'])) ?: null;
		$form['social_youtube'] = (trim($form['social_youtube'])) ?: null;
		
		$sql = make_insert_query('competition_judges', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	
	// Item
	function get_judges_item($id) {
		$sql = "
				SELECT co.*
				FROM competition_judges co
				WHERE co.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	
	
	
	// Delete
	function delete_judges_item($id) {
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		if (!(int)$id) {
			return false;
		}
		
		$list_items = $Festivals->get_festivals_list_by_relations('competition_judges', $id);
		
		if (!$list_items) {
			$sql = "DELETE FROM competition_judges WHERE id = " . (int)$id;
			return execute_sql_query($sql, "delete");
		}
		
		return false;
	}
	
	
	
	
	
	function update_newsletter_judges($judges_id) {
		require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
		$Newsletters = new Newsletters2;
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		$key = 'competition-judges-id';
		
		// latest fest
		$data_festival = $Festivals->get_festivals_item_latest();
		if (!$data_festival) {
			return false;
		}
		$key .= $data_festival['id'];
		
		$data_judges = $this->get_judges_item($judges_id);
		if (!$data_judges || !trim($data_judges['email'])) {
			return false;
		}
		
		// is this year attending
		$sql = "
			SELECT fr.id
			FROM festivals_relations fr
			WHERE fr.festival_id = " . (int)$data_festival['id'] . " AND fr.rec_table = 'competition_judges' AND fr.rec_id = " . (int)$data_judges['id'] . "
		";
		$is_this_festival_attender = execute_sql_query($sql, 'get row');
		if (!$is_this_festival_attender) {
			return false;
		}
		
		$data_newsletter_category = $Newsletters->check_add_categories_item_by_key($key);
		$Newsletters->check_add_relations_users_categories_with_email($data_newsletter_category['id'], $data_judges['email']);
		
		return true;
	}
	
	
	
	function get_settings_item($festival_id) {
		$sql = "SELECT * FROM competition_settings WHERE festival_id = " . (int)$festival_id;
		return execute_sql_query($sql, 'get row');
	}
	
	
	
	function update_settings_item($id, $form) {
		$form['title'] = (trim($form['title'])) ?: null;
		$form['description'] = (trim($form['description'])) ?: null;
		$form['gallery_id'] = (int)$form['gallery_id'] ?: null;
		$form['show_judges'] = (bool)$form['show_judges'];
		$form['show_results'] = (bool)$form['show_results'];
		
		$sql = make_update_query('competition_settings', $form, ['festival_id' => $id], ['description']);
		return execute_sql_query($sql, "update");
	}
	
	
	
	function add_settings_item($festival_id) {
		$form = [
			'festival_id' => (int)$festival_id,
		];
		$sql = make_insert_query('competition_settings', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	
	function get_medal_categories_list($from=0, $items=20, $params=[]) {
		$params = $this->get_medal_categories_list_params($params);
		
		$sql = "
				SELECT medalc.*,
				       ctitle.text AS medal_title,
				       ftitle.text AS festival_title
				FROM competition_medal_categories medalc
				LEFT JOIN languages_varchar ctitle ON ctitle.table_name = 'competition_medal_categories' AND ctitle.table_id = medalc.id AND ctitle.keyword = 'title' AND ctitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals f ON f.id = medalc.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_medal_categories_list_cnt($params=array()) {
		$params = $this->get_medal_categories_list_params($params);
		
		$sql = "
				SELECT COUNT(medalc.id) AS cnt
				FROM competition_medal_categories medalc
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_medal_categories_list_params($params) {
		$where = [];
		
		if ($params['festival']) {
			if ($params['festival'] == 'active') {
				require_once(LIBS_MAIN_PATH . 'festivals.class.php');
				$Festivals = new Festivals();
				$data_festival = $Festivals->get_festivals_list(['is_active' => true]);
				if ($data_festival) {
					$data_festival = reset($data_festival)['id'];
					$where[] = " medalc.festival_id = " . (int)$data_festival . " ";
				}
			} else {
				$where[] = " medalc.festival_id = " . (int)$params['festival'] . " ";
			}
		}
		
		if (!$order) {
			$order[] = " ctitle.text ASC ";
		}
		
		return [
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(', ', $order)) : '',
		];
	}
	
	
	function get_medal_categories_item($id) {
		$sql = "
				SELECT medalc.*,
				       ctitle.text AS medal_title,
				       ftitle.text AS festival_title
				FROM competition_medal_categories medalc
				LEFT JOIN languages_varchar ctitle ON ctitle.table_name = 'competition_medal_categories' AND ctitle.table_id = medalc.id AND ctitle.keyword = 'title' AND ctitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals f ON f.id = medalc.festival_id
				LEFT JOIN languages_varchar ftitle ON ftitle.table_name = 'festivals' AND ftitle.table_id = f.id AND ftitle.keyword = 'title' AND ftitle.language = '" . $this->site_language . "'
				WHERE medalc.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	
	
	function add_medal_categories_item($form) {
		global $TextLanguages;
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		
		$data_festival = $Festivals->get_festivals_item_latest();
		$form['festival_id'] = $data_festival['id'];
		$title = (trim($form['title'])) ?: null;
		unset($form['title']);
		
		$sql = make_insert_query('competition_medal_categories', $form);
		$id = execute_sql_query($sql, "insert");
		
		if ($id) {
			$TextLanguages->update_varchar('competition_medal_categories', $id, 'title', $title);
		}
		
		return $id;
	}
	
	
	function update_medal_categories_item($id, $form) {
		global $TextLanguages;
		
		$title = (trim($form['title'])) ?: null;
		
		return $TextLanguages->update_varchar('competition_medal_categories', $id, 'title', $title);
	}
	
	
	function delete_medal_categories_item($id) {
		global $TextLanguages;

		$sql = "SELECT id FROM competition_items WHERE medal_categories_id = " . (int)$id;
		$list = execute_sql_query($sql, "get col");
		if (!$list) {
			$TextLanguages->update_varchar('competition_medal_categories', $id, 'title', '');
			$sql = "DELETE FROM competition_medal_categories WHERE id = " . (int)$id;
			return execute_sql_query($sql, "delete");
		}
		return false;
	}
}
