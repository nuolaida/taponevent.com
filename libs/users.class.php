<?php
class Users {
	var $module_config, $domain_keyword, $login_id;

	function  __construct() {
		global $page_special_config;

		$this->domain_keyword = $page_special_config['keyword'];
		$this->login_id = 0;

		if (file_exists($page_special_config['site_config_path'] . 'configs/users.config.php')) {
			include($page_special_config['site_config_path'] . 'configs/users.config.php');
			$this->module_config = $users_config;
		}
	}



	function get_list($from=0, $items=20, $params=array()) {
		$params = $this->get_list_params($params);

		if ($_SESSION['user']['admin'] == 2) {
			$sql = "
				SELECT u.*
				FROM users u
				WHERE 1 " . $params['where'] . "
				ORDER BY id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		} else {
			$sql = "
				SELECT u.*
				FROM users u
				LEFT JOIN users_rights ur ON u.id = ur.user_id AND ur.domain_keyword = '" . addslashes($this->domain_keyword) . "'
				WHERE 1 " . $params['where'] . " AND ur.id IS NOT NULL
				GROUP BY u.id
				ORDER BY u.id DESC
				LIMIT " . (int)$from . ", " . (int)$items . "
			";
		}

		return execute_sql_query($sql, "get all");
	}
	function get_list_cnt($params=array()) {
		$params = $this->get_list_params($params);

		if ($_SESSION['user']['admin'] == 2) {
			$sql = "
				SELECT COUNT(id) AS cnt
				FROM users u
				WHERE 1 " . $params['where'] . "
			";
		} else {
			$sql = "
				SELECT COUNT(id) AS cnt
				FROM users u
				LEFT JOIN users_rights ur ON u.id = ur.user_id AND ur.domain_keyword = '" . addslashes($this->domain_keyword) . "'
				WHERE 1 " . $params['where'] . " AND ur.id IS NOT NULL
				GROUP BY u.id
				ORDER BY u.id DESC
			";
		}

		return execute_sql_query($sql, "get one");
	}
	function get_list_params($params) {
		$where = array();

		if ($params['search']) {
			$where[] = "u.email LIKE '%" . addslashes($params['search']) . "%'";
		}

		return array(
			'where' => (count($where)) ? (' AND ' . implode(' AND ', $where)) : '',
		);
	}



	private function get_item($where) {
		$sql = "
				SELECT u.*
				FROM users u
 				WHERE {$where}
			";
		$data = execute_sql_query($sql, "get row");
		if (!$data) {
			return false;
		}

		$data['rights'] = $this->get_item_rights($data['id'], $data['admin']);

		return $data;
	}
	// email & pass
	function get_item_login($email, $password) {
		$where = "u.email = '" . addslashes($email) . "' AND u.rec_password = md5('" . addslashes($password) . "')";

		return $this->get_item($where);
	}
	// by id
	function get_item_id($id) {
		$where = "u.id = " . (int)$id . "";

		return $this->get_item($where);
	}
	// by email
	function get_item_email($email) {
		$where = "u.email = '" . addslashes(trim($email)) . "'";

		return $this->get_item($where);
	}



	// login data to session
	function item_to_session($data=false) {
		// hack to logout users
		if ($data && is_array($data)) {
			$_SESSION['user'] = array(
				'id'					=>	$data['id'],
				'password'				=>	md5_encrypt($data['rec_password']),
				'email'					=>	$data['email'],
				'name'					=>	$data['name'],
				'visit_time'			=>	$data['visit_time'],
				'admin'					=>	$data['admin'],
				'login_id'				=>	$this->login_id,
				'rights'				=>	$data['rights'],
			);
			if ($data['admin']) {
				$_SESSION['user']['admin_rights'] = $this->get_item_rights($data['id'], $data['admin']);
			}
			setcookie('logedin', '1', time()+30*60, '/');

		} else {
			unset($_SESSION['user']);
			unset($_SESSION['app']);
			setcookie('logedin', '0', time()-30*60, '/');
		}
	}
	// login data to cookie
	function item_to_cookie($data=false) {
		$time = time() + 3*60*24*60*60;

		if ($data && is_array($data)) {
			setcookie('user_email', $data['email'], $time, '/');

			return true;
		} else {
			setcookie('user_email', '', time() - 3600, '/');

			return false;
		}
	}



	// check login
	function check_login() {
		// hack to logout
		if ($_SESSION['user']['id'] && ($_SESSION['user']['login_id'] != $this->login_id)) {
			$this->item_to_session();
			$this->item_to_cookie();
		}

		if ($_SESSION['user']['id']) {
			$data = $this->get_item_id($_SESSION['user']['id']);
			if ($data['rec_password'] != md5_decrypt($_SESSION['user']['password']) || !$data['is_active']) {
				$this->item_to_session();
				$this->item_to_cookie();
			} else {
				$this->item_to_session($data);
			}
		}
	}



	// update item
	function update_item($id, $form) {
		$data = $this->get_item_id($id);
		if (!$data) {
			return false;
		}

		$sql = make_update_query('users', $form, array('id' => $id));
		return execute_sql_query($sql, 'update');
	}
	// update login info
	function update_item_login($id) {
		$form = [
			'visit_time'  =>  time(),
			'visit_ip'    =>  get_ip(),
		];
		return $this->update_item($id, $form);
	}



	// add item
	function add_item($form) {
		$data = $this->get_item_email($form['email']);
		if ($data) {
			return false;
		}

		$sql = make_insert_query('users', $form);
		return execute_sql_query($sql, 'add');
	}



	// Rights
	function get_item_rights($id, $admin_type) {
		$permissions = array();

		if ($admin_type == 1) {
			$sql = "
					SELECT am.*, ur.rights_read, ur.rights_write
					FROM users_rights ur
					LEFT JOIN admin_menu am ON am.id = ur.menu_id
					WHERE ur.user_id = " . (int)$id . " AND ur.domain_keyword = '" . $this->domain_keyword . "'
					ORDER BY am.pos
				";
			$list = execute_sql_query($sql, 'get all');
			foreach ($list as $item) {
				if ($item['module']) {
					$module = $item['module'];

					if (!isset($permissions[$module])) {
						$permissions[$module] = array();
					}

					if ($item['rights_write']) {
						$permissions[$module]['read'] = true;
						$permissions[$module]['write'] = true;
					} else if ($item['rights_read']) {
						$permissions[$module]['read'] = true;
						$permissions[$module]['write'] = false;
					}

					if ($permissions[$module]['read']) {
						$permissions[$module]['menu_id'] = $item['id'];
						$permissions[$module]['name'] = $item['name'];
					}
				}
			}

		} else if ($admin_type == 2) {
			$sql = "
					SELECT am.*
					FROM admin_menu am
					ORDER BY am.pos
				";
			$list = execute_sql_query($sql, 'get all');
			foreach ($list as $item) {
				if ($item['module']) {
					$module = $item['module'];

					$permissions[$module] = array(
						'menu_id' => $item['id'],
						'name' => $item['name'],
						'read' => true,
						'write' => true,
					);
				}
			}
		}

		return $permissions;
	}



	// Get admin user menu
	function get_admin_menu() {
		global $Translate;
		$menu = array();

		if ($_SESSION['user']['admin'] == 1) {
			$sql = "
					SELECT am.*
					FROM users_rights ur
					LEFT JOIN admin_menu am ON am.id = ur.menu_id
					WHERE ur.user_id = " . (int)$_SESSION['user']['id'] . " AND ur.domain_keyword = '" . addslashes($this->domain_keyword) . "'
					ORDER BY am.pos ASC
				";
		} else if ($_SESSION['user']['admin'] == 2) {
			$sql = "
					SELECT am.*
					FROM admin_menu am
					ORDER BY am.pos ASC
				";
		}

		$list = execute_sql_query($sql, 'get all');
		if ($list) {
			foreach ($list as $item) {
				if (!$item['parent_id']) {
					$menu[] = array(
						'name' => $Translate->get_item('menu ' . $item['name']),
						'link' => ($item['link']) ? $item['link'] : (($item['module']) ? admin_make_url('/admin.php?module=' . $item['module']) : ''),
					);
				}

			}
		}

		return $menu;
	}



	// Get admin user menu
	function get_admin_menu_all() {
		$sql = "
				SELECT am.*
				FROM admin_menu am
				ORDER BY am.pos ASC
			";
		return execute_sql_query($sql, 'get all');
	}




	function delete_rights_user($user_id) {
		$sql = "DELETE FROM users_rights WHERE user_id = " . (int)$user_id . " AND domain_keyword = '" . addslashes($this->domain_keyword) . "'";
		return execute_sql_query($sql, 'delete');
	}



	function add_rights_item($form) {
		if (!(int)$form['user_id']) {
			return false;
		}

		if (!(int)$form['menu_id']) {
			return false;
		}

		if (!$form['rights_read'] && !$form['rights_write']) {
			return false;
		}

		$form['domain_keyword'] = $this->domain_keyword;

		$sql = make_insert_query('users_rights', $form);
		return execute_sql_query($sql, 'add');

	}

}
