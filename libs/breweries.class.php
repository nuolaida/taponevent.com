<?php
class Breweries {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/breweries.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/breweries.config.php');
			$this->module_config = $breweries_config;
		}
	}



	// Item
	function get_item($id) {
		$sql = "
				SELECT b.*, txtitle.text AS title, txdescr.text AS description,
					txcity.text AS city_title, cn.id AS country_id, txcountry.text AS country_title
				FROM breweries b
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'breweries' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN languages_text txdescr ON txdescr.table_name = 'breweries' AND txdescr.table_id = b.id AND txdescr.keyword = 'description' AND txdescr.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = b.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				WHERE b.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Beer list
	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT b.*, txtitle.text AS title, txcity.text AS city_title, cn.id AS country_id, txcountry.text AS country_title
				FROM breweries b
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'breweries' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = b.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				LEFT JOIN festivals_relations festrel ON festrel.rec_table = 'breweries' AND festrel.rec_id = b.id {$params['on_join']}
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
				FROM breweries b
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'breweries' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN festivals_relations festrel ON festrel.rec_table = 'breweries' AND festrel.rec_id = b.id {$params['on_join']}
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
				case 'title':
					$order[] = " title ASC";
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
				SELECT b.*, txtitle.text AS title, txcity.text AS city_title, cn.id AS country_id, txcountry.text AS country_title, rel.id AS relation_id
				FROM gallery_relations rel
				LEFT JOIN breweries b ON rel.rec_id = b.id
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'breweries' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = b.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				WHERE rel.rec_table = 'breweries' AND rel.gallery_id = " . (int)$gallery_id . "
				ORDER BY txtitle.text ASC
				LIMIT 0, 500
			";

		return execute_sql_query($sql, 'get all');
	}



	// Beer list
	function get_list_relations_gallery_free($gallery_id) {

		$sql = "
				SELECT b.*, txtitle.text AS title, txcity.text AS city_title, cn.id AS country_id, txcountry.text AS country_title
				FROM breweries b
				LEFT JOIN gallery_relations rel ON rel.rec_id = b.id AND rel.rec_table = 'breweries' AND rel.gallery_id = " . (int)$gallery_id . "
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'breweries' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = b.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				WHERE rel.id IS NULL
				ORDER BY txtitle.text ASC
				LIMIT 0, 500
			";

		return execute_sql_query($sql, 'get all');
	}



	// Beer list
	function get_list_attenders_grouped_by_country() {
		$return = [];
		$sql = "
				SELECT b.*, txtitle.text AS title, txcity.text AS city_title, cn.id AS country_id,
				       txcountry.text AS country_title, cn.keyword AS country_keyword,
				       COUNT(rel.id) AS festivals_cnt
				FROM breweries b
				LEFT JOIN festivals_relations rel ON rel.rec_id = b.id AND rel.rec_table = 'breweries'
				LEFT JOIN languages_varchar txtitle ON txtitle.table_name = 'breweries' AND txtitle.table_id = b.id AND txtitle.keyword = 'title' AND txtitle.language = '" . $this->site_language . "'
				LEFT JOIN cities ct ON ct.id = b.city_id
				LEFT JOIN languages_varchar txcity ON txcity.table_name = 'cities' AND txcity.table_id = ct.id AND txcity.keyword = 'title' AND txcity.language = '" . $this->site_language . "'
				LEFT JOIN countries cn ON cn.id = ct.country_id
				LEFT JOIN languages_varchar txcountry ON txcountry.table_name = 'countries' AND txcountry.table_id = cn.id AND txcountry.keyword = 'title' AND txcountry.language = '" . $this->site_language . "'
				WHERE rel.id IS NOT NULL
				GROUP BY b.id
				HAVING festivals_cnt > 0
				ORDER BY txcountry.text ASC, RAND()
			";
		$list = execute_sql_query($sql, 'get all');
		if ($list) {
			foreach ($list as $item) {
				if (!$return[$item['country_id']]) {
					$return[$item['country_id']] = [
						'country_title' => $item['country_title'],
						'country_keyword' => $item['country_keyword'],
						'list' => [],
					];
				}
				$return[$item['country_id']]['list'][] = $item;
			}
		}

		return $return;
	}



	// Update
	function update_item($id, $form) {
		global $TextLanguages;

		$form_main = [
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
			'city_id'       => ((int)$form['city_id']) ? $form['city_id'] : null,
			'email'       => (trim($form['email'])) ? trim($form['email']) : null,
			'link_social_website'  => (trim($form['link_social_website'])) ? $form['link_social_website'] : null,
			'link_social_facebook'  => (trim($form['link_social_facebook'])) ? $form['link_social_facebook'] : null,
			'link_social_instagram'  => (trim($form['link_social_instagram'])) ? $form['link_social_instagram'] : null,
			'link_social_twitter'  => (trim($form['link_social_twitter'])) ? $form['link_social_twitter'] : null,
			'link_social_youtube'  => (trim($form['link_social_youtube'])) ? $form['link_social_youtube'] : null,
			'link_social_untappd'  => (trim($form['link_social_untappd'])) ? $form['link_social_untappd'] : null,
		];
		$sql = make_update_query('breweries', $form_main, array('id' => $id));
		$return = execute_sql_query($sql, "update");

		if ($return) {
			$TextLanguages->update_varchar('breweries', $id, 'title', $form['title']);
			$TextLanguages->update_text('breweries', $id, 'description', $form['description']);
		}

		return $return;
	}



	// Add
	function add_item($form) {
		global $TextLanguages;

		$form_main = [
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
			'city_id'       => ((int)$form['city_id']) ? $form['city_id'] : null,
			'email'       => (trim($form['email'])) ? trim($form['email']) : null,
			'link_social_website'  => (trim($form['link_social_website'])) ? $form['link_social_website'] : null,
			'link_social_facebook'  => (trim($form['link_social_facebook'])) ? $form['link_social_facebook'] : null,
			'link_social_instagram'  => (trim($form['link_social_instagram'])) ? $form['link_social_instagram'] : null,
			'link_social_twitter'  => (trim($form['link_social_twitter'])) ? $form['link_social_twitter'] : null,
			'link_social_youtube'  => (trim($form['link_social_youtube'])) ? $form['link_social_youtube'] : null,
			'link_social_untappd'  => (trim($form['link_social_untappd'])) ? $form['link_social_untappd'] : null,
		];
		$sql = make_insert_query('breweries', $form_main);
		$id = execute_sql_query($sql, "insert");

		if ($id) {
			$TextLanguages->update_varchar('breweries', $id, 'title', $form['title']);
			$TextLanguages->update_text('breweries', $id, 'description', $form['description']);
		}

		return $id;
	}


	function update_newsletter($brewery_id) {
		require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
		$Newsletters = new Newsletters2;
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		$key = 'breweries-id';
		
		// latest fest
		$data_festival = $Festivals->get_festivals_item_latest();
		if (!$data_festival) {
			return false;
		}
		$key .= $data_festival['id'];
		
		// brewery
		$data_brewery = $this->get_item($brewery_id);
		if (!$data_brewery || !trim($data_brewery['email'])) {
			return false;
		}
		
		// is this year attending
		$sql = "
			SELECT fr.id
			FROM festivals_relations fr
			WHERE fr.festival_id = " . (int)$data_festival['id'] . " AND fr.rec_table = 'breweries' AND fr.rec_id = " . (int)$data_brewery['id'] . "
		";
		$is_this_festival_attender = execute_sql_query($sql, 'get row');
		if (!$is_this_festival_attender) {
			return false;
		}
		
		$data_newsletter_category = $Newsletters->check_add_categories_item_by_key($key);
		$Newsletters->check_add_relations_users_categories_with_email($data_newsletter_category['id'], $data_brewery['email']);
		
		return true;
	}


}
