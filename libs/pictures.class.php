<?php
class Pictures {
	var $module_config, $domain_keyword;

	function  __construct() {
		global $page_special_config;

		$this->domain_keyword = $page_special_config['keyword'];
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/pictures.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/pictures.config.php');
			$this->module_config = $pictures_config;
		}
	}




	// Item
	function get_item($id) {
		$sql = "
				SELECT gal.*
				FROM gallery gal
				WHERE gal.id = " . (int)$id . " AND gal.domain_keyword = '" . $this->domain_keyword . "'
			";

		return execute_sql_query($sql, 'get row');
	}



	// list
	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT gal.*
				FROM gallery gal
				WHERE gal.domain_keyword = '" . $this->domain_keyword . "' " . $params['where'] . "
				ORDER BY gal.id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		$sql = "
				SELECT COUNT(gal.id) AS cnt
				FROM gallery gal
				WHERE gal.domain_keyword = '" . $this->domain_keyword . "' " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_list_params($params) {
		$where = array();

		if ($params['search']) {
			$where[] = " gal.keywords LIKE '%" . addslashes($params['search']) . "%' ";
		}

		return array(
				'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			);
	}



	// list relations
	function get_list_by_festival($id=null) {
		$sql = "
				SELECT DISTINCT(rel.rec_id) AS gallery_id
				FROM festivals_relations rel
				WHERE rel.rec_table = 'gallery' " . (($id) ? " AND rel.festival_id = " . (int)$id . " " : "") . "
				ORDER BY RAND()
				LIMIT 0, 16
			";

		return execute_sql_query($sql, 'get all');
	}



	// Add
	function add_item($form) {
		require_once(LIBS_MAIN_PATH . 'gallery.class.php');
		$Gallery = new Gallery;

		return $Gallery->add_resized_item($form, $Gallery->config['max_upload_file_format']);
	}



	// Update
	function update_item($id, $form) {
		require_once(LIBS_MAIN_PATH . 'gallery.class.php');
		$Gallery = new Gallery;

		return $Gallery->db_update_item($id, $form);
	}



	// Delete
	function delete_item($id) {
		require_once(LIBS_MAIN_PATH . 'gallery.class.php');
		$Gallery = new Gallery;

		return $Gallery->db_delete_item($id);
	}



	// Checks is the last one
	function is_last_record($id) {
		$list = $this->get_list(0, 1);
		$item = reset($list);
		if ($item['id'] == $id) {
			return true;
		}

		return false;
	}



	// Relations Add
	function add_relations_item($gallery_id, $rec_table, $rec_id) {
		if (!(int)$gallery_id || !addslashes($rec_table) || !(int)$rec_id) {
			return false;
		}

		$form = [
			'gallery_id'   => $gallery_id,
			'rec_table'     => $rec_table,
			'rec_id'        => $rec_id,
		];
		$sql = make_insert_query('gallery_relations', $form);
		return execute_sql_query($sql, "insert");
	}



	// Relations Delete
	function delete_relations_item($id) {
		if (!(int)$id) {
			return false;
		}

		$sql = "DELETE FROM gallery_relations WHERE id = " . (int)$id;
		return execute_sql_query($sql, "delete");
	}



	// list relations
	function get_relations_list($rec_table, $rec_id, $from=0, $items=16) {
		$sql = "
				SELECT rel.gallery_id AS gallery_id
				FROM gallery_relations rel
				WHERE rel.rec_table = '" . addslashes($rec_table) . "' AND rel.rec_id = " . (int)$rec_id . "
				ORDER BY RAND()
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}

}
?>