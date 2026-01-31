<?php
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users;
	
	$module_name = 'users';
	/** @var object $smarty */
	$smarty->assign('module_name', $module_name);
	check_permissions($module_name);
	
	/** @var object $Translate */
	$title = [$Translate->get_item('users')];
	$submenu = [
		$Translate->get_item('list') => 'browse',
	];
	//$adodb->debug = true;
	
	
	/** @var array $url */
	switch ($url['_action_']) {
	
		// info
		case 'info':
			$page = $module_name . ".info";
	
			if ($url['id']) {
				$data = $Users->get_item_id($url['id']);
				$smarty->assign('data', $data);
	
				$smarty->assign('list_rights', $Users->get_item_rights($data['id'], $data['admin']));
			}
	
			$smarty->assign('list_menu', $Users->get_admin_menu_all());
	
			break;
	
	
		case 'infoAct':
			check_permissions($module_name, 'write');
			$form = $url['form'];
			$form2 = $url['form2'];
			$rights = $url['rights'];
			$form['admin'] = ($form2['admin']) ? 1 : 0;
			$form['is_active'] = ($form2['is_active']) ? 1 : 0;
			if (trim($form2['password'])) {
				$form['rec_password'] = md5($form2['password']);
			}
			$user_id = 0;
			$oki = true;
			
			if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$oki = false;
			}
			
			if ($form2['id']) {
				$data = $Users->get_item_id($form2['id']);
				
				if ($data['admin'] == 2 && $_SESSION['user']['admin'] != 2) {
					$_SESSION['main_messages'][] = $Translate->get_item('error super admin');
					$oki = false;
				}
				
				if ($oki) {
					$data_email = $Users->get_item_email($form['email']);
					if ($data_email && $data_email['id'] != $data['id']) {
						$_SESSION['main_messages'][] = $Translate->get_item('error email duplicate');
						$oki = false;
					}
				}
				
				if ($oki) {
					$user_id = $data['id'];
					$Users->update_item($user_id, $form);
					$Users->delete_rights_user($data['id']);
				}

			} else {
				$data_user = $Users->get_item_email($form['email']);
				if ($data_user) {
					$_SESSION['main_messages'][] = $Translate->get_item('error email duplicate');
					$oki = false;
				}
				
				if ($oki) {
					$user_id = $Users->add_item($form);
				}
			}
			
			if ($user_id && $rights && $form['is_active']) {
				foreach ($rights as $key_rights => $value_rights) {
					$form_rights = [
						'user_id' => $user_id,
						'menu_id' => $key_rights,
						'rights_read' => ($value_rights['read'] || $value_rights['write']) ? 1 : 0,
						'rights_write' => ($value_rights['write']) ? 1 : 0,
					];
					$Users->add_rights_item($form_rights);
				}
			}
	
			Location('?module=' . $module_name);
			die();
		
		default:
			$page = $module_name;
			$search = urldecode($url['search']);
			$pg = ((int)$url['pg']) ? $url['pg'] : 0;
			$pg_items = 30;
	
			$smarty->assign('search', $search);
	
			$smarty->assign('list', $Users->get_list($pg, $pg_items, ['search' => $search]));
	
			$pg_records = $Users->get_list_cnt(['search' => $url['search']]);
			/** @var object $Paging */
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "?module=" . $module_name . "&action=browse&search=" . urlencode($search) . "&pg="));
			
			break;
	}
