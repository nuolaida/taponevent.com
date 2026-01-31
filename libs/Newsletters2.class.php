<?php
class Newsletters2 {
	var $module_config, $site_language;

	function  __construct() {
		global $page_special_config, $Translate;

		$this->site_language = $Translate->language;
		
		if (file_exists($page_special_config['site_config_path'] . 'configs/newsletters.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/newsletters.config.php');
			$this->module_config = $newsletters_config;
		}
	}



	// List
	function get_emails_list($from=0, $items=20, $params=[]) {
		$params = $this->get_emails_list_params($params);

		$sql = "
				SELECT n.*, SUM(IF(s.sent_time IS NOT NULL, 1, 0)) AS send_done,
					COUNT(s.user_id) AS send_total
				FROM newsletters2_emails n
				LEFT JOIN newsletters2_emails_sent s ON n.id = s.email_id
				WHERE 1 " . $params['where'] . "
				GROUP BY n.id
				ORDER BY n.id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_emails_list_cnt($params=[]) {
		$params = $this->get_emails_list_params($params);

		$sql = "
				SELECT COUNT(n.id) AS cnt
				FROM newsletters2_emails n
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_emails_list_params($params) {
		$where = array();

		if ($params['search']) {
			$where[] = " n.text_subject LIKE '%" . addslashes($params['search']) . "%' ";
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
		);
	}



	// Update
	function update_emails_item($id, $form) {
		$form_main = [
			'rec_time'      => $form['rec_time'],
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
			'text_subject'  => $form['text_subject'],
			'text_body'     => $form['text_body'],
			'admin_description' => (trim($form['admin_description'])) ?: null,
		];
		$sql = make_update_query('newsletters2_emails', $form_main, ['id' => $id], ['text_body']);
		$return = execute_sql_query($sql, "update");

		return $return;
	}



	// Add
	function add_emails_item($form) {
		$form_main = [
			'rec_time'      => $form['rec_time'],
			'gallery_id'    => ((int)$form['gallery_id']) ? $form['gallery_id'] : null,
			'text_subject'  => $form['text_subject'],
			'text_body'     => $form['text_body'],
			'admin_description' => (trim($form['admin_description'])) ?: null,
		];
		$sql = make_insert_query('newsletters2_emails', $form_main, ['text_body']);
		$id = execute_sql_query($sql, "insert");

		return $id;
	}



	// Item
	function get_emails_item($id) {
		$sql = "
				SELECT n.*
				FROM newsletters2_emails n
				WHERE n.id = " . (int)$id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Sent grouped
	function get_emails_sent_grouped_by_email($email_id) {
		$sql = "
				SELECT SUM(IF(s.sent_time IS NOT NULL, 1, 0)) AS send_done, SUM(IF(s.sent_time IS NULL, 1, 0)) AS send_waiting, COUNT(s.opened_times) AS opened_times
				FROM newsletters2_emails_sent s
				WHERE s.email_id = " . (int)$email_id . "
			";
		$data = execute_sql_query($sql, 'get row');

		$return = [
			'done'      =>  ($data['send_done']) ? (int)$data['send_done'] : 0,
			'waiting'   =>  ($data['send_waiting']) ? (int)$data['send_waiting'] : 0,
			'opened'   =>  ($data['opened_times']) ? (int)$data['opened_times'] : 0,
		];
		$return['total'] = $return['done'] + $return['waiting'];

		return $return;
	}



	// Sent list
	function get_emails_sent_list($from=0, $items=20, $params=[]) {
		$params = $this->get_emails_sent_list_params($params);

		$sql = "
				SELECT s.*, u.email AS user_email, e.text_subject AS email_subject
				FROM newsletters2_emails_sent s
				LEFT JOIN newsletters2_users u ON u.id = s.user_id
				LEFT JOIN newsletters2_emails e ON e.id = s.email_id
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_emails_sent_list_cnt($params=[]) {
		$params = $this->get_emails_sent_list_params($params);

		$sql = "
				SELECT COUNT(s.email_id) AS cnt
				FROM newsletters2_emails_sent s
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_emails_sent_list_params($params) {
		$where = [];
		$order = [];

		if ($params['email']) {
			$where[] = " s.email_id = " . (int)$params['email'] . " ";
		}

		if ($params['user']) {
			$where[] = " s.user_id = " . (int)$params['user'] . " ";
			$order[] = " e.id DESC ";
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (' ' . implode(', ', $order)) : ' s.sent_time DESC ',
		);
	}



	// Users list
	function get_users_list($from=0, $items=20, $params=[]) {
		$params = $this->get_users_list_params($params);

		$sql = "
				SELECT u.*
				FROM newsletters2_users u
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";

		return execute_sql_query($sql, 'get all');
	}
	function get_users_list_cnt($params=[]) {
		$params = $this->get_users_list_params($params);

		$sql = "
				SELECT COUNT(u.id) AS cnt
				FROM newsletters2_users u
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_users_list_params($params) {
		$where = [];
		$order = [];

		if ($params['search']) {
			$where[] = " u.email LIKE '%" . addslashes($params['search']) . "%' ";
			$order[] = " u.email ASC ";
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (' ' . implode(', ', $order)) : ' u.id DESC ',
		);
	}



	// Users add
	function add_users_item($form) {
		$form_main = [
			'email'      => $form['email'],
		];
		$sql = make_insert_query('newsletters2_users', $form_main);
		return execute_sql_query($sql, "insert");
	}



	// Users item
	function get_users_item($id) {
		$sql = "
				SELECT u.*
				FROM newsletters2_users u
				WHERE u.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	function get_users_item_email($email) {
		$sql = "
				SELECT u.*
				FROM newsletters2_users u
				WHERE u.email = '" . addslashes(trim($email)) . "'
			";
		
		return execute_sql_query($sql, 'get row');
	}



	// Users update
	function update_users_item($id, $form) {
		unset($form['id']);
		
		$sql = make_update_query('newsletters2_users', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}



	// Send email
	function send_email($email_id, $user_id=null, $email_address=null) {
		global $conf, $page_special_config, $smarty;

		$data_email = $this->get_emails_item($email_id);

		if ($email_address) {
			$email_address = split_email($email_address);
		} else {
			$data_user = $this->get_users_item($user_id);
			$email_address = $data_user['email'];
		}

		if ($data_email && $email_address) {
			$email_params['subject'] = $data_email['text_subject'];
			$email_params['to'] = $email_address;
			
			$domain = (is_development_version()) ? "https://" . $page_special_config['keyword'] : $page_special_config['protocol'] . '://' . $page_special_config['domain'];
			$unsubscribe_id = md5_encrypt($user_id . ':' . time());

			$smarty->assign('config_domain', $domain);
			$smarty->assign('config_domain_plain', $page_special_config['domain']);
			$smarty->assign('data_email', $data_email);
			$smarty->assign('data_user', $data_user);
			$smarty->assign('config_social', $page_special_config['social']);
			$smarty->assign('unsubscribe_id', $unsubscribe_id);
			
			$body_html = my_fetch('newsletters2.email.body.tpl', null, null, null, 'frontend');

			$email_params['body_html'] = $this->send_email_body_html($body_html);

			mail_customize_smtp($email_params);
		}

		return false;
	}
	function send_email_body_html($html) {
		$new_html = $html;
		$new_html = str_replace('<p>', '<p style="font-size: 16px;">', $new_html);

		return $new_html;
	}



	// Send email
	function send_email_scheduled($items=null) {
		if (!(int)$items) {
			$items = 50;
		}
		
		$sql = "
				SELECT s.*, u.email AS user_email
				FROM newsletters2_emails_sent s
				LEFT JOIN newsletters2_users u ON u.id = s.user_id
				WHERE s.sent_time IS NULL
				ORDER BY s.email_id ASC, s.user_id ASC
				LIMIT 0, " . (int)$items . "
			";
		$list = execute_sql_query($sql, 'get all');
		if ($list) {
			$return = count($list);
			foreach ($list as $item) {
				$this->send_email($item['email_id'], $item['user_id']);
				$this->update_sent_item($item['email_id'], $item['user_id'], ['sent_time' => time()]);
			}
		}

		return $return;
	}



	// Sent add list
	function add_sent_list($email_id, $category_id) {
		$sql = "
			INSERT IGNORE INTO newsletters2_emails_sent (email_id, user_id)
			(SELECT " . (int)$email_id . ", user_id FROM newsletters2_relations_users_categories WHERE category_id = " . (int)$category_id . " AND is_unsubscribed IS NULL)
		";
		execute_sql_query($sql, "insert");

		return true;
	}
	
	

	// Users item
	function get_sent_item($email_id, $user_id) {
		$sql = "
				SELECT s.*
				FROM newsletters2_emails_sent s
				WHERE s.email_id = " . (int)$email_id . " AND s.user_id = " . (int)$user_id . "
			";

		return execute_sql_query($sql, 'get row');
	}



	// Users item
	function update_sent_item($email_id, $user_id, $form) {
		$sql = make_update_query('newsletters2_emails_sent', $form, ['email_id' => (int)$email_id, 'user_id' => (int)$user_id]);
		return execute_sql_query($sql, 'update');
	}



	// Sent delete item
	function delete_sent_item($email_id, $user_id) {
		$sql = "DELETE FROM newsletters2_emails_sent WHERE email_id = " . (int)$email_id . " AND user_id = " . (int)$user_id . "";
		return execute_sql_query($sql, "delete");
	}
	
	
	
	function get_categories_list($params=[]) {
		$params = $this->get_categories_list_params($params);
		$sql = "
				SELECT c.*, SUM(IF(rel1.is_unsubscribed IS NULL, 1, 0)) AS subscribed, SUM(IF(rel1.is_unsubscribed IS NULL, 0, 1)) AS unsubscribed
				FROM newsletters2_categories c
				LEFT JOIN newsletters2_relations_users_categories rel1 ON c.id = rel1.category_id
				WHERE 1 " . $params['where'] . "
				GROUP BY c.id
				ORDER BY " . $params['order'] . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_categories_list_assoc($params=[]) {
		$list = [];
		$l = $this->get_categories_list($params);
		if ($l) {
			foreach ($l as $item) {
				$list[$item['id']] = $item['title'];
			}
		}
		return $list;
	}
	function get_categories_list_params($params) {
		$where = [];
		$order = [];
		
		if (isset($params['active'])) {
			$where[] = " c.is_active = " . (($params['active']) ? 1 : 0) . " ";
		}
		
		if (!$params['order']) {
			$order[] = 'c.is_active DESC';
			$order[] = 'c.gen_key ASC';
			$order[] = 'c.title ASC';
		}
		
		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(', ', $order)) : $order,
		);
	}
	
	
	
	function add_categories_item($form) {
		if (isset($form['rec_key'])) {
			$form['rec_key'] = trim($form['rec_key']);
			if (!$form['rec_key']) {
				unset($form['rec_key']);
			} else {
				$data_key = $this->get_categories_item_by_key($form['rec_key']);
				if ($data_key) {
					unset($form['rec_key']);
				}
			}
		}
		$sql = make_insert_query('newsletters2_categories', $form);
		return execute_sql_query($sql, "insert");
	}
	
	
	
	function update_categories_item($id, $form) {
		if (isset($form['rec_key'])) {
			unset($form['rec_key']);
		}
		if (isset($form['id'])) {
			unset($form['id']);
		}
		
		$sql = make_update_query('newsletters2_categories', $form, ['id' => $id]);
		return execute_sql_query($sql, "update");
	}
	
	
	
	function get_categories_item($id) {
		$sql = "
				SELECT c.*
				FROM newsletters2_categories c
				WHERE c.id = " . (int)$id . "
			";
		
		return execute_sql_query($sql, 'get row');
	}
	function get_categories_item_by_key($key) {
		$sql = "
				SELECT c.*
				FROM newsletters2_categories c
				WHERE c.gen_key = '" . addslashes($key) . "'
			";
		
		return execute_sql_query($sql, 'get row');
	}
	
	
	
	function check_add_categories_item_by_key($key) {
		if (!trim($key)) {
			return false;
		}
		
		$data = $this->get_categories_item_by_key($key);
		if ($data) {
			return $data;
		} else {
			$form = [
				'title' => $key,
				'gen_key' => $key,
				'is_active' => 1,
			];
			$id = $this->add_categories_item($form);
			return $this->get_categories_item($id);
		}
	}
	
	
	
	function get_relations_users_categories_list($from=0, $items=20, $params=[]) {
		$params = $this->get_relations_users_categories_list_params($params);
		
		$sql = "
				SELECT rel.*, c.title AS category_title, c.gen_key AS category_key
				FROM newsletters2_relations_users_categories rel
				LEFT JOIN newsletters2_categories c ON rel.category_id = c.id
				WHERE 1 " . $params['where'] . "
				ORDER BY " . $params['order'] . "
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		
		return execute_sql_query($sql, 'get all');
	}
	function get_relations_users_categories_list_cnt($params=[]) {
		$params = $this->get_relations_users_categories_list_params($params);
		
		$sql = "
				SELECT COUNT(rel.user_id) AS cnt
				FROM newsletters2_relations_users_categories rel
				WHERE 1 " . $params['where'] . "
			";
		return execute_sql_query($sql, 'get one');
	}
	function get_relations_users_categories_list_params($params) {
		$where = [];
		$order = [];
		
		if ($params['category']) {
			$where[] = "rel.category_id = " . (int)$params['category'];
		}
		if ($params['user']) {
			$where[] = "rel.user_id = " . (int)$params['user'];
		}
		
		if ($params['order']) {
		
		} else {
			$order[] = "rel.user_id ASC";
		}
		
		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
			'order' => (count($order)) ? (implode(', ', $order)) : '',
		);
	}



	function add_relations_users_categories($categories, $users, $resubscribe = false) {
		$cnt = 0;
		if (!is_array($categories)) {
			$categories = [$categories];
		}
		if (!is_array($users)) {
			$users = [$users];
		}
		
		foreach ($categories as $item_categories) {
			foreach ($users as $item_users) {
				$form = [
					'user_id' => $item_users,
					'category_id' => $item_categories,
				];
				if ($resubscribe) {
					$form['is_unsubscribed'] = null;
				}
				$sql = make_insert_query('newsletters2_relations_users_categories', $form, [], true);
				$added = execute_sql_query($sql, "insert");
				if ($added) {
					$cnt++;
				}
			}
		}
		
		return $cnt;
	}
	
	
	
	function update_relations_users_categories_subscription($user_id, $category_id, $subscribe) {
		if (!(int)$user_id || !(int)$category_id || !isset($subscribe)) {
			return false;
		}
		
		$sql = make_update_query('newsletters2_relations_users_categories', ['is_unsubscribed' => (($subscribe) ? null : 1)], ['user_id' => $user_id, 'category_id' => $category_id]);
		return execute_sql_query($sql, "update");
	}
	function update_relations_users_categories_unsubscribe($user_id) {
		if (!(int)$user_id) {
			return false;
		}
		
		$sql = make_update_query('newsletters2_relations_users_categories', ['is_unsubscribed' => 1], ['user_id' => $user_id]);
		return execute_sql_query($sql, "update");
	}
	
	
	
	function check_add_relations_users_categories_with_email($category_id, $email) {
		$data_category = $this->get_categories_item($category_id);
		if (!$data_category) {
			return false;
		}
		
		$list_email = split_email($email);
		foreach ($list_email as $item_email) {
			$data_user = $this->get_users_item_email($item_email);
			if (!$data_user) {
				$form = [
					'email' => $item_email,
				];
				$user_id = $this->add_users_item($form);
			} else {
				$user_id = $data_user['id'];
			}
			
			$this->add_relations_users_categories($category_id, $user_id);
		}
		
		return true;
	}
}
