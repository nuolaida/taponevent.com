<?php
require_once(LIBS_MAIN_PATH . 'Catalogue.class.php');
$Catalogue = new Catalogue;
require_once(LIBS_MAIN_PATH . 'countries.class.php');
$Countries = new Countries();

switch ($url['_module_']) {
	case 'catalogue_festivals':
		$module_name = 'catalogue_festivals';
		$template_pre = 'catalogue';
		$type = 'festivals';
		$title_keyword = 'festivals';
		break;
	case 'catalogue_competitions':
		$module_name = 'catalogue_competitions';
		$template_pre = 'catalogue';
		$type = 'competitions';
		$title_keyword = 'competitions';
		break;
}

check_permissions($module_name, 'read');

$title = [
		$Translate->get_item('catalogue'),
		$Translate->get_item($title_keyword),
	];
$submenu = array(
		$Translate->get_item('list')	=>	'/admin.php?module=' . $module_name . '&action=browse',
	);
//$adodb->debug = true;
	

switch ($url['_action_']) {
	case 'info':
		if ($url['id']) {
			$smarty->assign('data', $Catalogue->get_item($url['id']));
		}
		
		$smarty->assign('list_countries', $Countries->get_countries_list(0, 1000));
		$smarty->assign('list_drink_types', $Catalogue->module_config['drink_types']);
		$smarty->assign('config_module', $module_name);
		$smarty->assign('config_type', $type);
		
		$smarty->assign('list_dates', $Catalogue->get_dates_list($url['id'], 0, 1000));
		
		if ($url['dates_id']) {
			$smarty->assign('data_dates', $Catalogue->get_dates_item($url['dates_id']));
		}

		$page = $template_pre . ".info";
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
				$Catalogue->update_item($rec_id, $form);

				$ActionsLogs->add('catalogue_items', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

		// add
		} else {
			if ($oki) {
				$form['type'] = $form2['type'];
				$rec_id = $Catalogue->add_item($form);

				$ActionsLogs->add('catalogue_items', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}
		
		Location(admin_make_url("/admin.php?module=" . $module_name . "&action=info&id=" . $rec_id));
		die();
		
		break;



	case 'datesInfoAct':
		check_permissions($module_name, 'write');
		
		$form = $url['form'];
		$form2 = $url['form2'];
		$oki = true;
		
		if (!$form['date_start']) {
			$oki = false;
			$_SESSION['main_messages'][] = $Translate->get_item('error date empty');
		}
		
		if ($oki) {
			if ($form2['id']) {
				$data = $Catalogue->get_dates_item($form2['id']);
				$Catalogue->update_dates_item($data['id'], $form);
				$ActionsLogs->add('catalogue_items', $data['item_id'], 'update', 'update dates #' . $data['id']);
				$_SESSION['main_messages'][] = $Translate->get_item('success data updated');
				
			} else {
				$id = $Catalogue->add_dates_item($form);
				$ActionsLogs->add('catalogue_items', $form['item_id'], 'update', 'add dates #' . $id);
				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}
		}

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	case 'datesDeleteAct':
		check_permissions($module_name, 'write');

		$data = $Catalogue->get_dates_item($url['id']);

		$Catalogue->delete_dates_item($data['id']);

		$ActionsLogs->add('catalogue_items', $data['id'], 'update', 'delete dates #' . $data['id'], $data);

		$_SESSION['main_messages'][] = $Translate->get_item('success data deleted');

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	// Ajax get cities by country id
	case 'citiesAjax':
		$country_id = (int)$url['country_id'];
		$adodb->debug = false;

		if ($country_id) {
			die(json_encode($Countries->get_cities_list(0, 1000, ['country' => $country_id])));
		}

		die(json_encode([]));
		break;


		
	default:
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 50;
		$Paging = new paging;
		
		$smarty->assign('search', $search);

		$smarty->assign('list', $Catalogue->get_list($type, $pg, $pg_items, ['search' => $url['search']]));
		$smarty->assign('list_drink_types', $Catalogue->module_config['drink_types']);
		$smarty->assign('config_module', $module_name);
		
		$pg_records = $Catalogue->get_list_cnt($type, ['search' => $url['search']]);
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=" . $module_name . "&action=browse&search=" . urlencode($search) . "&pg="));

		$page = $template_pre;
		break;
}