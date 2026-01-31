<?php

require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
$Newsletters = new Newsletters2;
$module_name = 'newsletters2';

check_permissions($module_name, 'read');

$title = [$Translate->get_item('newsletters')];
$submenu = array(
		$Translate->get_item('list') => 'browse',
		$Translate->get_item('categories') => 'categories',
		$Translate->get_item('users') => 'users',
	);
$smarty->assign('config_module', $module_name);

//$adodb->debug = true;


switch ($url['_action_']) {
	case 'info':
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 20;
		$Paging = new paging;

		if ($url['id']) {
			$data = $Newsletters->get_emails_item($url['id']);
			$smarty->assign('data', $data);

			// categories
			$smarty->assign('list_categories', $Newsletters->get_categories_list_assoc(['active' => 1]));
			
			// emails status
			$smarty->assign('data_sent', $Newsletters->get_emails_sent_grouped_by_email($data['id']));

			// emails list
			$smarty->assign('list_sent', $Newsletters->get_emails_sent_list($pg, $pg_items, ['email' => $data['id']]));
			$pg_records = $Newsletters->get_emails_sent_list_cnt(['email' => $data['id']]);
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=info&id=" . urlencode($data['id']) . "&pg="));

		}
		
		$page = $module_name . ".info";
		break;



	case 'infoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// check for errors
		if (!trim($form['text_subject'])) {
			$oki = false;
			$_SESSION['main_messages'][] = $Translate->get_item('error title');
		}
		$form['rec_time'] = ($form2['rec_time']) ? strtotime($form2['rec_time']) : time();

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Newsletters->update_emails_item($rec_id, $form);

				$ActionsLogs->add('newsletters2_emails', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

		// add
		} else {
			if ($oki) {
				$rec_id = $Newsletters->add_emails_item($form);

				$ActionsLogs->add('newsletters2_emails', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=" . $module_name . "&action=info&id=" . $rec_id));
		die();
		
		break;
	
	
	
	case 'sendTestAct':
		check_permissions($module_name, 'write');
		
		$form = $url['form'];
		$form2 = $url['form2'];
		
		$Newsletters->send_email($form2['id'], null, $form['email']);
		$_SESSION['main_messages'][] = $Translate->get_item('success email sent');
		
		$ActionsLogs->add('newsletters2_emails', $form2['id'], 'update', 'test email sent ' . $form['email']);
		
		Location($_SERVER['HTTP_REFERER']);
		die();
		break;
	
		
	
	case 'sendAddCategoryAct':
		check_permissions($module_name, 'write');
		
		$form = $url['form'];
		
		$data_category = $Newsletters->get_categories_item($form['category_id']);
		if (!$data_category) {
			$_SESSION['main_messages'][] = $Translate->get_item('error category not found');
		} else {
			$Newsletters->add_sent_list($form['email_id'], $form['category_id']);
			$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			
			$ActionsLogs->add('newsletters2_emails', $form['email_id'], 'update', 'add users to send from category: ' . $data_category['title'] . ' (#' . $form['category_id'] . ')');
		}
		
		Location($_SERVER['HTTP_REFERER']);
		die();
		break;

		
	
	case 'sentDeleteAct':
		check_permissions($module_name, 'write');
		
		$Newsletters->delete_sent_item($url['email_id'], $url['user_id']);
		$_SESSION['main_messages'][] = $Translate->get_item('success data deleted');
		
		$ActionsLogs->add('newsletters2_emails', $url['email_id'], 'update', 'deleted user #' . $url['user_id'] . ' from sent');
		
		Location($_SERVER['HTTP_REFERER']);
		die();
		
		break;
	
	
	
	case 'categories':
		$smarty->assign('list', $Newsletters->get_categories_list());
		
		$title[] = $Translate->get_item('categories');
		$page = $module_name . ".categories";
		break;
	
	
	
	case 'categoriesInfo':
		if ($url['id']) {
			$data = $Newsletters->get_categories_item($url['id']);
			$smarty->assign('data', $data);
		}
		
		$title[] = $Translate->get_item('categories');
		$page = $module_name . ".categories.info";
		break;
	
	
	
	case 'categoriesInfoAct':
		check_permissions($module_name, 'write');
		
		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];
		$form['is_active'] = ($form['is_active']) ? 1 : 0;
		
		// check for errors
		if (!trim($form['title'])) {
			$oki = false;
			$_SESSION['main_messages'][] = $Translate->get_item('error title');
		}
		if (!$oki) {
			Location($_SERVER['HTTP_REFERER']);
			die();
		}
		
		// edit
		if ((int)$form2['id']) {
			$rec_id = $form2['id'];
			$Newsletters->update_categories_item($rec_id, $form);
				
			$ActionsLogs->add('newsletters2_categories', $rec_id, 'update');
			
		// add
		} else {
			$rec_id = $Newsletters->add_categories_item($form);
				
			$ActionsLogs->add('newsletters2_categories', $rec_id, 'insert');
		}
		
		$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
		
		Location(admin_make_url("/admin.php?module=" . $module_name . "&action=categoriesInfo&id=" . $rec_id));
		die();
		break;
	
	
	
	case 'users':
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $Newsletters->get_users_list($pg, $pg_items, ['search' => $search]));

		$pg_records = $Newsletters->get_users_list_cnt(['search' => $url['search']]);
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=users&search=" . urlencode($search) . "&pg="));
		
		$title[] = $Translate->get_item('users');
		$page = $module_name . ".users";
		break;



	case 'usersAdd':
		$smarty->assign('list_categories', $Newsletters->get_categories_list_assoc(['active' => 1]));
		
		$title[] = $Translate->get_item('users');
		$page = $module_name . ".users.add";
		break;



	case 'usersAddAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];

		if (!(int)$form['category_id']) {
			$oki = false;
			$_SESSION['main_messages'][] = $Translate->get_item('error empty category');
		} else {
			$list_categories = $Newsletters->get_categories_list_assoc();
			if (!$list_categories[$form['category_id']]) {
				$oki = false;
				$_SESSION['main_messages'][] = $Translate->get_item('error empty category');
			}
		}
		
		if ($oki) {
			$text = preg_replace("/\s/", " ", $form['email']);
			$text = preg_replace("/(\s)+/", " ", $text);
			$list = explode(' ', $text);
			if ($list) {
				foreach ($list as $item_email) {
					if (trim($item_email)) {
						$item = check_email(strtolower(trim($item_email)));
						if ($item) {
							// user
							$data_user = $Newsletters->get_users_item_email($item);
							if (!$data_user) {
								$user_id = $Newsletters->add_users_item(['email' => $item]);
								$ActionsLogs->add('newsletters2_users', $user_id, 'insert');
								$_SESSION['main_messages'][] = $Translate->get_item('success user added') . ' - ' . $item;
							} else {
								$user_id = $data_user['id'];
							}
							
							// subscription
							$data_rel = $Newsletters->get_relations_users_categories_list(0, 1, ['category' => $form['category_id'], 'user' => $user_id]);
							if (!$data_rel) {
								$Newsletters->add_relations_users_categories($form['category_id'], $user_id);
							} else {
								$data_rel = $data_rel[0];
								$_SESSION['main_messages'][] = $Translate->get_item('error subscription exists') . ' - ' . $item . ' - ' . (($data_rel['is_unsubscribed']) ? $Translate->get_item('unsubscribed') : $Translate->get_item('subscribed'));
							}
						} else {
							$_SESSION['main_messages'][] = $Translate->get_item('error email') . ' - ' . $item_email;
						}
					}
				}
			}

			$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
		}

		Location(admin_make_url("/admin.php?module=" . $module_name . "&action=users"));
		die();

		break;



	case 'usersEdit':
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 20;
		$Paging = new paging;

		if ($url['id']) {
			$data = $Newsletters->get_users_item($url['id']);
			$smarty->assign('data', $data);
			
			$smarty->assign('list_categories', $Newsletters->get_relations_users_categories_list(0, 100, ['user' => $data['id']]));
			
			// emails list
			$smarty->assign('list_sent', $Newsletters->get_emails_sent_list($pg, $pg_items, ['user' => $data['id']]));
			$pg_records = $Newsletters->get_emails_sent_list_cnt(['user' => $data['id']]);
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=usersEdit&id=" . urlencode($data['id']) . "&pg="));

		}
		
		$title[] = $Translate->get_item('users');
		$page = $module_name . ".users.edit";
		break;
	
	
	
	case 'usersEditAct':
		check_permissions($module_name, 'write');
		
		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];
		
		$email = preg_replace("/\s/", "", $form['email']);
		$email = check_email(trim(strtolower($email)));
		if (!$email) {
			$oki = false;
			$_SESSION['main_messages'][] = $Translate->get_item('error email');
		} else {
			$data_new_email = $Newsletters->get_users_item_email($email);
			if ($data_new_email && $data_new_email['id'] != $form2['id']) {
				$oki = false;
				$_SESSION['main_messages'][] = $Translate->get_item('error dublicate entry');
			}
		}
		
		$data = $Newsletters->get_users_item($form2['id']);
		if (!$data) {
			$oki = false;
		}
		
		if ($oki) {
			if ($email != $data['email']) {
				$Newsletters->update_users_item($form2['id'], ['email' => $email]);
				$ActionsLogs->add('newsletters_users', $form2['id'], 'update', $data['email'] . ' -> ' . $email);
			}
			
		}

		Location(admin_make_url("/admin.php?module=" . $module_name . "&action=usersEdit&id=" . $form2['id']));
		die();
		break;
	
	
	
	case 'usersEditSubscriptionAct':
		check_permissions($module_name, 'write');
		
		$oki = true;

		if (!(int)$url['id'] && !(int)$url['category']) {
			$oki = false;
		}
		
		if ($oki) {
			$data_user = $Newsletters->get_users_item($url['id']);
			if (!$data_user) {
				$oki = false;
			}
		}

		if ($oki) {
			$data_category = $Newsletters->get_categories_item($url['category']);
			if (!$data_category) {
				$oki = false;
			}
		}
		
		if ($oki) {
			$data_rel = $Newsletters->get_relations_users_categories_list(0, 1, ['category' => $url['category'], 'user' => $url['id']]);
			if ($data_rel) {
				$data_rel = $data_rel[0];
				$subscribe = (bool)$data_rel['is_unsubscribed'];
			} else {
				$oki = false;
			}
		}
		
		if ($oki) {
			$Newsletters->update_relations_users_categories_subscription($data_user['id'], $data_category['id'], $subscribe);
			$ActionsLogs->add('newsletters2_users', $data_user['id'], 'update', 'Subscription to: ' . $data_category['title'] . ' (#' . $data_category['id'] . ') -> ' . (int)$subscribe);
		}
		
		Location(admin_make_url($_SERVER['HTTP_REFERER']));
		die();
		break;




	default:
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $Newsletters->get_emails_list($pg, $pg_items, ['search' => $search]));

		$pg_records = $Newsletters->get_emails_list_cnt(['search' => $url['search']]);
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=browse&search=" . urlencode($search) . "&pg="));
		break;
}