<?php
	require_once(LIBS_MAIN_PATH . 'Conference.class.php');
	$Conference = new Conference;
	require_once(LIBS_MAIN_PATH . 'festivals.class.php');
	$Festivals = new Festivals;
	$module_name = 'conference';
	
	check_permissions($module_name, 'read');
	
	$title = [
		$Translate->get_item('conference'),
	];
	$submenu = [
			$Translate->get_item('list')	=>	'browse',
			$Translate->get_item('speakers')	=>	'speakers',
		];
	//$adodb->debug = true;
	
	
	$smarty->assign('config_module', $module_name);

	switch ($url['_action_']) {
		case 'info':
			if ($url['id']) {
				$data = $Conference->get_item($url['id']);
				$smarty->assign('data', $data);
			}
			$list_festivals = $Festivals->get_festivals_list();
			$smarty->assign('list_festivals', $list_festivals);
			$list_speakers = $Conference->get_speakers_list();
			$smarty->assign('list_speakers', $list_speakers);
			
			$page = "conference.info";
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
				if (!trim($form['title']) && !trim($form['rec_time'])) {
					$Conference->delete_item($form2['id']);
					$ActionsLogs->add('conference_list', $form2['id'], 'delete');
				} else {
					$rec_id = $form2['id'];
					if ($oki) {
						$Conference->update_item($rec_id, $form);
						
						$ActionsLogs->add('conference_list', $rec_id, 'update');
						
						$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
					}
				}
				
			// add
			} else {
				if ($oki) {
					$rec_id = $Conference->add_item($form);
					
					$ActionsLogs->add('conference_list', $rec_id, 'insert');
					
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				} else {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				
			}
			
			// newsletter
			$Conference->update_newsletter($form['speaker_id']);
			
			Location(admin_make_url("/admin.php?module=conference&action=info&id=" . $rec_id));
			die();
			
			break;
		
		
		
		case 'speakers':
			$search = urldecode($url['search']);
			$pg = ((int)$url['pg']) ? $url['pg'] : 0;
			$pg_items = 30;
			$Paging = new paging;
			
			$smarty->assign('search', $search);
			
			$smarty->assign('list', $Conference->get_speakers_list($pg, $pg_items, array('search' => $search)));
			
			$pg_records = $Conference->get_speakers_list_cnt(['search' => $url['search']]);
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=conference&action=speakers&search=" . urlencode($search) . "&pg="));
			
			$title[] = $Translate->get_item('speakers');
			$page = "conference.speakers";
			break;
		
		
		
		case 'speakersInfo':
			if ($url['id']) {
				$data = $Conference->get_speakers_item($url['id']);
				$smarty->assign('data', $data);
			}
			
			$title[] = $Translate->get_item('speakers');
			$page = "conference.speakers.info";
			break;
		
		
		case 'speakersInfoAct':
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
				if (!trim($form['name']) && !trim($form['company'])) {
					$Conference->delete_speakers_item($form2['id']);
					$ActionsLogs->add('conference_speakers', $form2['id'], 'delete');
				} else {
					$rec_id = $form2['id'];
					if ($oki) {
						$Conference->update_speakers_item($rec_id, $form);
						
						$ActionsLogs->add('conference_speakers', $rec_id, 'update');
						
						$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
					}
				}
				
			// add
			} else {
				if ($oki) {
					$rec_id = $Conference->add_speakers_item($form);
					
					$ActionsLogs->add('conference_speakers', $rec_id, 'insert');
					
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				} else {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				
			}
			
			// newsletter
			$Conference->update_newsletter($rec_id);
			
			Location(admin_make_url("/admin.php?module=conference&action=speakersInfo&id=" . $rec_id));
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
			
			$smarty->assign('list', $Conference->get_list(0, 10000, ['festival' => $search_festival]));
			
			
			break;
		
	}