<?php
	require_once(LIBS_MAIN_PATH . 'Competition.class.php');
	$Competition = new Competition;
	require_once(LIBS_MAIN_PATH . 'festivals.class.php');
	$Festivals = new Festivals;
	$module_name = 'competition';
	
	check_permissions($module_name, 'read');
	
	$title = [
		$Translate->get_item('competition'),
	];
	$submenu = [
			$Translate->get_item('list')	=>	'browse',
			$Translate->get_item('producer')	=>	'owners',
			$Translate->get_item('judges')	=>	'judges',
			$Translate->get_item('settings')	=>	'settings',
			$Translate->get_item('print')	=>	'print',
		];
	//$adodb->debug = true;
	
	
	$smarty->assign('config_module', $module_name);

	switch ($url['_action_']) {
		case 'info':
			if ($url['id']) {
				$data = $Competition->get_item($url['id']);
				$smarty->assign('data', $data);
				
				$data_owner = $Competition->get_owners_item($data['owner_id']);
				$smarty->assign('data_owner', $data_owner);
			}
			$list_festivals = $Festivals->get_festivals_list();
			$smarty->assign('list_festivals', $list_festivals);
			$list_owners = $Competition->get_owners_list(0, 500);
			$smarty->assign('list_owners', $list_owners);
			$list_medal_categories = $Competition->get_medal_categories_list(0, 500);
			$smarty->assign('list_medal_categories', $list_medal_categories);
			
			$page = $module_name . ".info";
			break;
		
		
		case 'infoAct':
			check_permissions($module_name, 'write');
			
			$oki = true;
			$form = $url['form'];
			$form2 = $url['form2'];
			
			// check for errors
			if (!trim($form['title'])) {
				$oki = false;
				$_SESSION['main_messages'][] = $Translate->get_item('error title');
			}
			
			// edit
			if ($form2['id']) {
				if (!trim($form['title']) && !trim($form['owner_id'])) {
					$Competition->delete_item($form2['id']);
					$ActionsLogs->add('competition_items', $form2['id'], 'delete');
				} else {
					$rec_id = $form2['id'];
					if ($oki) {
						$Competition->update_item($rec_id, $form);
						$ActionsLogs->add('competition_items', $rec_id, 'update');
						$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
					}
				}
				
			// add
			} else {
				if ($oki) {
					$rec_id = $Competition->add_item($form);
					$ActionsLogs->add('competition_items', $rec_id, 'insert');
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				} else {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
			}
			
			// newsletter
			$Competition->update_newsletter_owners($form['owner_id']);
			
			Location(admin_make_url("/admin.php?module=" . $module_name . "&action=info&id=" . $rec_id));
			die();
			
			break;
		
		
		
		case 'owners':
			$search = urldecode($url['search']);
			$pg = ((int)$url['pg']) ? $url['pg'] : 0;
			$pg_items = 30;
			$Paging = new paging;
			
			$smarty->assign('search', $search);
			
			$smarty->assign('list', $Competition->get_owners_list($pg, $pg_items, array('name' => $search)));
			
			$pg_records = $Competition->get_owners_list_cnt(['name' => $url['search']]);
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=owners&search=" . urlencode($search) . "&pg="));
			
			$title[] = $Translate->get_item('producers');
			$page = $module_name . ".owners";
			break;
		
		
		
		case 'ownersInfo':
			if ($url['id']) {
				$data = $Competition->get_owners_item($url['id']);
				$smarty->assign('data', $data);
			}
			
			$title[] = $Translate->get_item('producers');
			$page = $module_name . ".owners.info";
			break;
		
		
		case 'ownersInfoAct':
			check_permissions($module_name, 'write');
			
			$oki = true;
			$form = $url['form'];
			$form2 = $url['form2'];
			
			// check for errors
			if (!trim($form['title'])) {
				$oki = false;
				$_SESSION['main_messages'][] = $Translate->get_item('error name');
			}
			
			// edit
			if ($form2['id']) {
				if (!trim($form['title']) && !trim($form['real_name'])) {
					$is_deleted = $Competition->delete_owners_item($form2['id']);
					if ($is_deleted) {
						$ActionsLogs->add('competition_owners', $form2['id'], 'delete');
						$_SESSION['main_messages'][] = $Translate->get_item('success data deleted');
					} else {
						$_SESSION['main_messages'][] = $Translate->get_item('error cant delete');
					}
				} else {
					$rec_id = $form2['id'];
					if ($oki) {
						$Competition->update_owners_item($rec_id, $form);
						
						$ActionsLogs->add('competition_owners', $rec_id, 'update');
						
						$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
					}
				}
				
			// add
			} else {
				if ($oki) {
					$rec_id = $Competition->add_owners_item($form);
					
					$ActionsLogs->add('competition_owners', $rec_id, 'insert');
					
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				} else {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				
			}
			
			// newsletter
			if ($rec_id) {
				$Competition->update_newsletter_owners($rec_id);
				Location(admin_make_url("/admin.php?module=" . $module_name . "&action=ownersInfo&id=" . $rec_id));
			} else {
				Location(admin_make_url("/admin.php?module=" . $module_name . "&action=owners"));
			}
			die();
			break;
		
		
		
		case 'judges':
			$search = urldecode($url['search']);
			$pg = ((int)$url['pg']) ? $url['pg'] : 0;
			$pg_items = 30;
			$Paging = new paging;
			
			$smarty->assign('search', $search);
			
			$smarty->assign('list', $Competition->get_judges_list($pg, $pg_items, array('name' => $search)));
			
			$pg_records = $Competition->get_judges_list_cnt(['name' => $url['search']]);
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=judges&search=" . urlencode($search) . "&pg="));
			
			$title[] = $Translate->get_item('judges');
			$page = $module_name . ".judges";
			break;
		
		
		
		case 'judgesInfo':
			if ($url['id']) {
				$data = $Competition->get_judges_item($url['id']);
				$smarty->assign('data', $data);
				
				$smarty->assign('list_festivals_unrelated', $Festivals->get_festivals_list_by_relations('competition_judges', $url['id'], false));
				$smarty->assign('list_festivals_related', $Festivals->get_festivals_list_by_relations('competition_judges', $url['id'], true));
			}
			
			$title[] = $Translate->get_item('judges');
			$page = $module_name . ".judges.info";
			break;
		
		
		case 'judgesInfoAct':
			check_permissions($module_name, 'write');
			
			$oki = true;
			$form = $url['form'];
			$form2 = $url['form2'];
			
			// check for errors
			if (!trim($form['name'])) {
				$oki = false;
				$_SESSION['main_messages'][] = $Translate->get_item('error name');
			}
			
			// edit
			if ($form2['id']) {
				if (!trim($form['name'])) {
					$is_deleted = $Competition->delete_judges_item($form2['id']);
					if ($is_deleted) {
						$ActionsLogs->add('competition_judges', $form2['id'], 'delete');
						$_SESSION['main_messages'][] = $Translate->get_item('success data deleted');
					} else {
						$_SESSION['main_messages'][] = $Translate->get_item('error cant delete');
					}
				} else {
					$rec_id = $form2['id'];
					if ($oki) {
						$Competition->update_judges_item($rec_id, $form);
						
						$ActionsLogs->add('competition_judges', $rec_id, 'update');
						
						$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
					}
				}
				
				// add
			} else {
				if ($oki) {
					$rec_id = $Competition->add_judges_item($form);
					
					$ActionsLogs->add('competition_judges', $rec_id, 'insert');
					
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				} else {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				
			}
			
			// newsletter
			if ($rec_id) {
				$Competition->update_newsletter_judges($rec_id);
				Location(admin_make_url("/admin.php?module=" . $module_name . "&action=judgesInfo&id=" . $rec_id));
			} else {
				Location(admin_make_url("/admin.php?module=" . $module_name . "&action=judges"));
			}
			die();
			break;
		
		
		
		case 'judgesFestivalsAddAct':
			check_permissions($module_name, 'write');
			
			$form = $url['form'];
			
			$Festivals->add_relations_item($form['festival_id'], 'competition_judges', $form['rec_id']);
			
			$ActionsLogs->add('competition_judges', $form['rec_id'], 'update', 'added festival #' . $form['festival_id']);
			
			$_SESSION['main_messages'][] = $Translate->get_item('success data updated');
			
			// newsletter
			$Competition->update_newsletter_judges($form['rec_id']);
			
			Location($_SERVER['HTTP_REFERER']);
			die();
			
			break;
		
		
		
		case 'judgesFestivalsDeleteAct':
			check_permissions($module_name, 'write');
			
			$data_relation = $Festivals->get_relations_item($url['id']);
			
			$Festivals->delete_relations_item($url['id']);
			
			$ActionsLogs->add('competition_judges', $data_relation['rec_id'], 'update', 'deleted festival #' . $data_relation['festival_id']);
			
			$_SESSION['main_messages'][] = $Translate->get_item('success data updated');
			
			Location($_SERVER['HTTP_REFERER']);
			die();
			
			break;
		
		
		
		case 'sendLabelAct':
			check_permissions($module_name, 'write');
			
			$form = $url['form'];
			$form2 = $url['form2'];
			
			$data_item = $Competition->get_item($form2['id']);
			$data_owner = $Competition->get_owners_item($data_item['owner_id']);
			
			$email_address = split_email($form['email']);
			
			if ($data_item && $email_address) {
				$email_params['subject'] = $Translate->get_item('competition email label subject') . ' / ' . $data_item['title'];
				$email_params['to'] = $email_address;
				
				$domain = (is_development_version()) ? "https://" . $page_special_config['keyword'] : $page_special_config['protocol'] . '://' . $page_special_config['domain'];
				$key = md5_encrypt($data_item['id'] . ':' . time());
				
				$smarty->assign('config_domain', $domain);
				$smarty->assign('data_item', $data_item);
				$smarty->assign('data_user', $data_owner);
				$smarty->assign('key', $key);
				
				$body_html = my_fetch('competition.email.label.tpl');
				
				$email_params['body_html'] = mail_body_customize($body_html);
				
				debug($email_params);
				mail_customize_smtp($email_params);
				$_SESSION['main_messages'][] = $Translate->get_item('success email sent');
				$ActionsLogs->add('competition_items', $form2['id'], 'update', 'email label sent ' . $form['email']);
			}
			
			Location($_SERVER['HTTP_REFERER']);
			die();
			break;

			
		case 'print':
			$title[] = $Translate->get_item('print');
			$page = $module_name . ".print";
			
			$data_festival = $Festivals->get_festivals_item_latest();

			// all drinks
			$list_items = $Competition->get_list(0, 10000, ['festival' => $data_festival['id'], 'order' => 'category']);
			$smarty->assign('list_items', $list_items);
			
			// categories
			$tbl1 = [];
			foreach ($list_items as $item) {
				$tbl1[trim(strtolower($item['category']))][($item['sweetness'] + 0)]++;
			}
			$smarty->assign('tbl1', $tbl1);
			
			// medals
			$list_medals = $Competition->get_list(0, 10000, ['festival' => $data_festival['id'], 'medal' => true]);
			$list_medals_sorted = [];
			if ($list_medals) {
				foreach ($list_medals as $item) {
					$list_medals_sorted[$item['medal_categories_id']]['medals'][$item['medal']] = $item;
					$list_medals_sorted[$item['medal_categories_id']]['title'] = $item['medal_categories_title'];
				}
			}
			$smarty->assign('list_medals', $list_medals_sorted);
			break;
		
		
		
		case 'settings':
			$title[] = $Translate->get_item('settings');
			$page = $module_name . ".settings";
			
			$list_festivals = $Festivals->get_festivals_list();
			$smarty->assign('list_festivals', $list_festivals);
			break;
		
		
		
		case 'settingsInfo':
			$title[] = $Translate->get_item('settings');
			$page = $module_name . ".settings.info";
			
			$data_festival = $Festivals->get_festivals_item($url['id']);
			$smarty->assign('data_festival', $data_festival);
			
			$data_settings = $Competition->get_settings_item($data_festival['id']);
			$smarty->assign('data', $data_settings);
			
			if (!$data_settings) {
				$Competition->add_settings_item($data_festival['id']);
				header("Refresh:0");
				die();
			}
			
			break;
		
		
		case 'settingsInfoAct':
			check_permissions($module_name, 'write');
			
			$form = $url['form'];
			$form2 = $url['form2'];
			
			if ($form2['festival_id']) {
				$Competition->update_settings_item($form2['festival_id'], $form);
				
				$ActionsLogs->add('competition_settings', $form2['festival_id'], 'update');
				
				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}
	
			Location($_SERVER['HTTP_REFERER']);
			die();
			break;
		
		
		
		case 'medalCat':
			$search_festival = urldecode($url['search_festival']);
			$smarty->assign('search_festival', $search_festival);
			$list_festivals = $Festivals->get_festivals_list();
			$smarty->assign('list_festivals', $list_festivals);
			
			if (!$search_festival) {
				$search_festival = reset($list_festivals)['id'];
			}
			
			$smarty->assign('list', $Competition->get_medal_categories_list(0, 10000, ['festival' => $search_festival]));

			$title[] = $Translate->get_item('medal categories');
			$page = $module_name . ".medal.categories";
			break;
		
		
			
		case 'medalCatInfo':
			$data_festival = $Festivals->get_festivals_item_latest();
			$smarty->assign('data_festival', $data_festival);
			
			if ($url['id']) {
				$data = $Competition->get_medal_categories_item($url['id']);
				$smarty->assign('data', $data);
			}
			
			$title[] = $Translate->get_item('medal categories');
			$page = $module_name . ".medal.categories.info";
			
			break;
		
		
			
		case 'medalCatInfoAct':
			check_permissions($module_name, 'write');
			
			$form = $url['form'];
			$form2 = $url['form2'];
			
			if ($form2['id'] && trim($form['title'])) {
				$id = $form2['id'];
				$Competition->update_medal_categories_item($form2['id'], $form);
				$ActionsLogs->add('competition_medal_categories', $form2['id'], 'update');
				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} elseif ($form2['id']) {
				$data = $Competition->get_item($form2['id']);
				$Competition->delete_medal_categories_item($form2['id']);
				$ActionsLogs->add('competition_medal_categories', $form2['id'], 'delete', false, $data);
				$_SESSION['main_messages'][] = $Translate->get_item('success data deleted');
			} else {
				$id = $Competition->add_medal_categories_item($form);
				$ActionsLogs->add('competition_medal_categories', $id, 'insert');
				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}
			
			Location(admin_make_url("/admin.php?module=" . $module_name . "&action=medalCat"));
			die();
			break;
		
		
		
		default:
			$search_festival = urldecode($url['search_festival']);
			$smarty->assign('search_festival', $search_festival);
			$list_festivals = $Festivals->get_festivals_list();
			$smarty->assign('list_festivals', $list_festivals);
			
			if (!$search_festival) {
				$search_festival = reset($list_festivals)['id'];
			}
			
			$smarty->assign('list', $Competition->get_list(0, 10000, ['festival' => $search_festival]));
			
			
			break;
		
	}