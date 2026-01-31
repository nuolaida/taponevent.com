<?php
class Catalogue {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/catalogue.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/catalogue.config.php');
			$this->module_config = $catalogue_config;
		}
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT c.*, txtitle.text AS title, txdescr.text AS description,
					txcity.text AS city_title, cn.id AS country_id, txcountry.text AS country_title,
					catdt.date_start AS last_date_start, catdt.date_end AS last_date_end,
			       	IF(catdt.date_end < DATE(NOW()), 1, 0) AS event_finished
				FROM catalogue_items c
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'catalogue_items' AND txtitle.table_id = c.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_text txdescr ON txdescr.table_name = 'catalogue_items' AND txdescr.table_id = c.id AND txdescr.keyword = 'description' AND txdescr.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = c.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				LEFT JOIN catalogue_dates catdt ON catdt.item_id = c.id AND catdt.id = (SELECT MAX(id) FROM catalogue_dates WHERE item_id = c.id)
				WHERE c.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Beer list
	function get_list($type, $from=0, $items=20, $params=[]) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT c.*, txtitle.text AS title, txcity.text AS city_title, txdescr.text AS description,
				       cn.id AS country_id, txcountry.text AS country_title,
				       catdt.date_start AS last_date_start, catdt.date_end AS last_date_end,
				       IF (catdt.date_end IS NULL,
				       		IF (catdt.date_start IS NULL, 1, IF (catdt.date_start < DATE(NOW()), 1, 0)),
				       		IF (catdt.date_end < DATE(NOW()), 1, 0)
			        	) AS event_finished
				FROM catalogue_items c
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'catalogue_items' AND txtitle.table_id = c.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_text txdescr ON txdescr.table_name = 'catalogue_items' AND txdescr.table_id = c.id AND txdescr.keyword = 'description' AND txdescr.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = c.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				LEFT JOIN catalogue_dates catdt ON catdt.item_id = c.id AND catdt.id = (SELECT MAX(id) FROM catalogue_dates WHERE item_id = c.id)
				WHERE c.type = '" . addslashes($type) . "' " . $params['where'] . "
				GROUP BY c.id
				ORDER BY {$params['order']}
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($type, $params=[]) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(c.id) AS cnt
				FROM catalogue_items c
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'catalogue_items' AND txtitle.table_id = c.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				WHERE c.type = '" . addslashes($type) . "' " . $params['where'] . "
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

		if (isset($params['order'])) {
			switch ($params['order']) {
				case 'rand':
					$order[] = " RAND() ";
					break;
				case 'title':
					$order[] = " title ASC";
					break;
			}
		}
		if (!$order) {
			$order[] = " event_finished ASC, catdt.date_start ASC ";
		}


		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(' AND ', $order)) : '',
		);
	}



	// Update
	function update_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
			'city_id'       => ((int)$form['city_id']) ? $form['city_id'] : null,
			'drink_type'       => (trim($form['drink_type'])) ? trim($form['drink_type']) : null,
			'link_social_website'  => (trim($form['link_social_website'])) ? $form['link_social_website'] : null,
			'link_social_facebook'  => (trim($form['link_social_facebook'])) ? $form['link_social_facebook'] : null,
			'link_social_instagram'  => (trim($form['link_social_instagram'])) ? $form['link_social_instagram'] : null,
			'link_social_twitter'  => (trim($form['link_social_twitter'])) ? $form['link_social_twitter'] : null,
			'link_social_youtube'  => (trim($form['link_social_youtube'])) ? $form['link_social_youtube'] : null,
		];
		$sql = make_update_query('catalogue_items', $form_main, ['id' => $id]);
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('catalogue_items', $id, 'title', $form['title']);
			$TextLanguages->update_text('catalogue_items', $id, 'description', $form['description']);
		}

		return $return;
	}



	// Add
	function add_item($form) {
		global $TextLanguages;

		$form_main = [
			'type'    => $form['type'],
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
			'city_id'       => ((int)$form['city_id']) ? $form['city_id'] : null,
			'drink_type'       => (trim($form['drink_type'])) ? trim($form['drink_type']) : null,
			'link_social_website'  => (trim($form['link_social_website'])) ? $form['link_social_website'] : null,
			'link_social_facebook'  => (trim($form['link_social_facebook'])) ? $form['link_social_facebook'] : null,
			'link_social_instagram'  => (trim($form['link_social_instagram'])) ? $form['link_social_instagram'] : null,
			'link_social_twitter'  => (trim($form['link_social_twitter'])) ? $form['link_social_twitter'] : null,
			'link_social_youtube'  => (trim($form['link_social_youtube'])) ? $form['link_social_youtube'] : null,
		];
		$sql = make_insert_query('catalogue_items', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('catalogue_items', $id, 'title', $form['title']);
			$TextLanguages->update_text('catalogue_items', $id, 'description', $form['description']);
		}

		return $id;
	}
	
	
	
	function get_dates_list($catalogue_item_id, $from=0, $items=20) {
		$sql = "
				SELECT dt.*
				FROM catalogue_dates dt
				WHERE dt.item_id = " . (int)$catalogue_item_id . "
				ORDER BY dt.date_start DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	
	
	
	function get_dates_item($id) {
		$sql = "
				SELECT dt.*
				FROM catalogue_dates dt
				WHERE dt.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	
	
	
	function add_dates_item($form) {
		$form['date_end'] = ($form['date_end']) ? $form['date_end'] : null;
		
		$sql = make_insert_query('catalogue_dates', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	
	function update_dates_item($id, $form) {
		$form['date_end'] = ($form['date_end']) ? $form['date_end'] : null;
		
		$sql = make_update_query('catalogue_dates', $form, ['id' => $id]);
		$return = execute_sql_query($sql, "update");
	}

	
	
	function delete_dates_item($id) {
		if (!(int)$id) {
			return false;
		}
		
		$sql = "DELETE FROM catalogue_dates WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}
	
	
	
	
}
?>