<?php
require_once(LIBS_MAIN_PATH . 'countries.class.php');
$Countries = new Countries();
require_once(LIBS_MAIN_PATH . 'festivals.class.php');
$Festivals = new Festivals();
require_once(LIBS_MAIN_PATH . 'index.class.php');
$Index = new Index();
$module_name = 'settings';

check_permissions($module_name, 'read');

$title = [$Translate->get_item('settings')];
$submenu = array(
	$Translate->get_item('index')	    =>	'index',
	$Translate->get_item('festivals')	=>	'festivals',
	$Translate->get_item('cities')	    =>	'cities',
	$Translate->get_item('countries')	=>	'countries',
	);
//$adodb->debug = true;


switch ($url['_action_']) {
	case 'countriesInfo':
		$title[] = $Translate->get_item('countries');

		if ($url['id']) {
			$data = $Countries->get_countries_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
			$title[] = $data['title'];
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.countries.info";
		break;



	case 'countriesInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// check for errors
		if (!trim($form['title'])) {
			$_SESSION['main_messages'][] = $Translate->get_item('error title');
			$oki = false;
		}

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Countries->update_countries_item($rec_id, $form);

				$ActionsLogs->add('countries', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Countries->add_countries_item($form);

				$ActionsLogs->add('countries', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=countriesInfo&id=" . $rec_id));
		die();
		
		break;


		
	case 'countries':
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $Countries->get_countries_list($pg, $pg_items, array('search' => $search)));

		$pg_records = $Countries->get_countries_list_cnt(array('search' => $url['search']));
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=settings&action=countries&search=" . urlencode($search) . "&pg="));

		$title[] = $Translate->get_item('countries');
		$page = "settings.countries";
		break;



	case 'citiesInfo':
		$title[] = $Translate->get_item('cities');

		$list_countries = $Countries->get_countries_list(0, 500);
		$smarty->assign('list_countries', $list_countries);

		if ($url['id']) {
			$data = $Countries->get_cities_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
			$title[] = $data['title'];
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.cities.info";
		break;



	case 'citiesInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// check for errors
		if (!trim($form['title'])) {
			$_SESSION['main_messages'][] = $Translate->get_item('error title');
			$oki = false;
		}
		if (!(int)$form['country_id']) {
			$_SESSION['main_messages'][] = $Translate->get_item('error form not filled');
			$oki = false;
		}

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Countries->update_cities_item($rec_id, $form);

				$ActionsLogs->add('cities', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Countries->add_cities_item($form);

				$ActionsLogs->add('cities', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=citiesInfo&id=" . $rec_id));
		die();

		break;



	case 'cities':
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $Countries->get_cities_list($pg, $pg_items, array('search' => $search)));

		$pg_records = $Countries->get_cities_list_cnt(array('search' => $url['search']));
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=settings&action=cities&search=" . urlencode($search) . "&pg="));

		$title[] = $Translate->get_item('cities');
		$page = "settings.cities";
		break;



	case 'festivalsInfo':
		$title[] = $Translate->get_item('festivals');

		if ($url['id']) {
			$data = $Festivals->get_festivals_item($url['id']);
			$smarty->assign('data', $data);

			$data_active = $Festivals->get_festivals_list(['active' => true]);
			$smarty->assign('disable_activation', ($data_active && !$data['is_active']) ? true : false);

			$title[] = $Translate->get_item('edit');
			$title[] = $data['title'];
		} else {
			$smarty->assign('disable_activation', true);

			$title[] = $Translate->get_item('add');
		}

		$page = "settings.festivals.info";
		break;



	case 'festivalsInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];
		$form['time_starts'] = strtotime($form2['time_starts']);
		$form['time_ends'] = strtotime($form2['time_ends']);
		$form['is_active'] = ($form2['is_active']) ? 1 : 0;

		// check for errors
		if (!trim($form['title'])) {
			$_SESSION['main_messages'][] = $Translate->get_item('error title');
			$oki = false;
		}
		if (!(int)$form['time_starts'] || !(int)$form['time_ends']) {
			$_SESSION['main_messages'][] = $Translate->get_item('error date empty');
			$oki = false;
		}

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Festivals->update_festivals_item($rec_id, $form);

				$ActionsLogs->add('festivals', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Festivals->add_festivals_item($form);

				$ActionsLogs->add('festivals', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=festivalsInfo&id=" . $rec_id));
		die();

		break;



	case 'festivals':

		$smarty->assign('list', $Festivals->get_festivals_list());

		$title[] = $Translate->get_item('festivals');
		$page = "settings.festivals";
		break;



	case 'index':
		$smarty->assign('list_index', $Index->get_list_grouped());

		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$page = "settings.index";
		break;



	case 'indexDeleteAct':
		check_permissions($module_name, 'write');

		$Index->delete_item($url['id']);

		$ActionsLogs->add('index_pages', $url['id'], 'delete', true);

		$_SESSION['main_messages'][] = $Translate->get_item('success data deleted');

		Location(admin_make_url("/admin.php?module=settings&action=index"));
		die();
		break;



	case 'indexTopImagesInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('index top images');

		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.index.topimages.info";
		break;



	case 'indexTopImagesInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		if (!trim($form['gallery_id'])) {
			$_SESSION['main_messages'][] = $Translate->get_item('error image');
			$oki = false;
		}

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);

				$ActionsLogs->add('index_pages', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

		// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'topimages');

				$ActionsLogs->add('index_pages', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=indexTopImagesInfo&id=" . $rec_id));
		die();

		break;



	case 'indexMeetUpInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('meet up in vaf');

		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.index.meetup.info";
		break;



	case 'indexMeetUpInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);

				$ActionsLogs->add('index_pages', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'meetup');

				$ActionsLogs->add('index_pages', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=indexMeetUpInfo&id=" . $rec_id));
		die();

		break;



	case 'indexAboutInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('about');

		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.index.about.info";
		break;



	case 'indexAboutInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);

				$ActionsLogs->add('index_pages', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'about');

				$ActionsLogs->add('index_pages', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=indexAboutInfo&id=" . $rec_id));
		die();

		break;



	case 'indexTicketsInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('tickets');

		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.index.tickets.info";
		break;



	case 'indexTicketsInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);

				$ActionsLogs->add('index_pages', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'tickets');

				$ActionsLogs->add('index_pages', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=indexTicketsInfo&id=" . $rec_id));
		die();

		break;



	case 'indexSponsorsInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('sponsors');

		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.index.sponsors.info";
		break;



	case 'indexSponsorsInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);

				$ActionsLogs->add('index_pages', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'sponsors');

				$ActionsLogs->add('index_pages', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=indexSponsorsInfo&id=" . $rec_id));
		die();

		break;



	case 'indexPlaceInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('place');

		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$page = "settings.index.place.info";
		break;



	case 'indexPlaceInfoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);

				$ActionsLogs->add('index_pages', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'place');

				$ActionsLogs->add('index_pages', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=settings&action=indexPlaceInfo&id=" . $rec_id));
		die();

		break;
	
	
	
	case 'indexMapInfo':
		$title[] = $Translate->get_item('settings');
		$title[] = $Translate->get_item('index');
		$title[] = $Translate->get_item('map');
		
		if ($url['id']) {
			$data = $Index->get_item($url['id']);
			$smarty->assign('data', $data);
			
			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}
		
		$page = "settings.index.map.info";
		break;
	
	
	
	case 'indexMapInfoAct':
		check_permissions($module_name, 'write');
		
		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];
		
		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Index->update_item($rec_id, $form);
				
				$ActionsLogs->add('index_pages', $rec_id, 'update');
				
				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}
			
			// add
		} else {
			if ($oki) {
				$rec_id = $Index->add_item($form, 'map');
				
				$ActionsLogs->add('index_pages', $rec_id, 'insert');
				
				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
		}
		
		Location(admin_make_url("/admin.php?module=settings&action=indexMapInfo&id=" . $rec_id));
		die();
		
		break;



	default:
		break;
}

?>
