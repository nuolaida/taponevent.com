<?php
	require_once(LIBS_MAIN_PATH . 'Beer.class.php');
	$Beer = new Beer;
	require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
	$Festivals = new Festivals;
	require_once(LIBS_MAIN_PATH . 'breweries.class.php');
	$Breweries = new Breweries;
	$module_name = 'beer';
	
	check_permissions($module_name, 'read');
	
	$title = $Translate->get_item('beer');
	$submenu = array(
		$Translate->get_item('list')	=>	'browse',
	);
//$adodb->debug = true;
	
	
	switch ($url['_action_']) {
		case 'info':
			if ($url['id']) {
				$data = $Beer->get_item($url['id']);
				$smarty->assign('data', $data);
			} else {
				$data_festival = $Festivals->get_festivals_item($url['festival_id']);
				$data_brewery = $Breweries->get_item($url['brewery_id']);
				if (!$data_brewery || !$data_festival) {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				$smarty->assign('data_festival', $data_festival);
				$smarty->assign('data_brewery', $data_brewery);
			}
			
			$page = "beer.info";
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
				$rec_id = $form2['id'];
				if ($oki) {
					$Beer->update_item($rec_id, $form);
					
					$ActionsLogs->add('beer', $rec_id, 'update');
					
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				}
				
			// add
			} else {
				if ($oki) {
					$rec_id = $Beer->add_item($form);
					
					$ActionsLogs->add('beer', $rec_id, 'insert');
					
					$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
				} else {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				
			}
			
			Location(admin_make_url("/admin.php?module=breweries&action=info&id=" . $form['brewery_id']));
			die();
			
			break;
		
		
		
		case 'deleteAct':
			check_permissions($module_name, 'write');
			
			$data = $Beer->get_item($url['id']);
			$Beer->delete_item($url['id']);
			
			$ActionsLogs->add('breweries', $data['brewery_id'], 'update', 'deleted beer #' . $url['id']);
			$ActionsLogs->add('beer', $url['id'], 'delete', false, $data);
			
			$_SESSION['main_messages'][] = $Translate->get_item('success data updated');
			
			Location($_SERVER['HTTP_REFERER']);
			die();
			
			break;
		
		
		
		default:
			$search_festival = urldecode($url['search_festival']);
			
			if ($search_festival) {
				$smarty->assign('list_breweries', $Breweries->get_list(0, 10000, ['festival' => $search_festival, 'order' => 'title']));
				$smarty->assign('list_beer', $Beer->get_list_grouped_by_brewery(0, 10000, ['festival_id' => $search_festival]));
			}
			
			$smarty->assign('search_festival', $search_festival);
			$smarty->assign('list_festivals', $Festivals->get_festivals_list());
			
			break;
		
	}