<?php
require_once(LIBS_MAIN_PATH . 'breweries.class.php');
$Breweries = new Breweries;
require_once(LIBS_MAIN_PATH . 'countries.class.php');
$Countries = new Countries();
require_once(LIBS_MAIN_PATH . 'festivals.class.php');
$Festivals = new Festivals();
require_once(LIBS_MAIN_PATH . 'Beer.class.php');
$Beer = new Beer;

$module_name = 'breweries';

check_permissions($module_name, 'read');

$title = $Translate->get_item('breweries');
$submenu = array(
		$Translate->get_item('list')	=>	'browse',
		$Translate->get_item('beer')	=>	'beer',
	);
//$adodb->debug = true;


switch ($url['_action_']) {
	case 'info':
		if ($url['id']) {
			$smarty->assign('data', $Breweries->get_item($url['id']));

			$smarty->assign('list_festivals_unrelated', $Festivals->get_festivals_list_by_relations('breweries', $url['id'], false));
			$smarty->assign('list_festivals_related', $Festivals->get_festivals_list_by_relations('breweries', $url['id'], true));

			$smarty->assign('list_beer', $Beer->get_list_grouped_by_festival(0, 1000, ['brewery_id' => $url['id']]));
		}

		$smarty->assign('list_countries', $Countries->get_countries_list(0, 1000));

		$page = "breweries.info";
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
				$Breweries->update_item($rec_id, $form);

				$ActionsLogs->add('breweries', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

		// add
		} else {
			if ($oki) {
				$rec_id = $Breweries->add_item($form);

				$ActionsLogs->add('breweries', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		// newsletter
		$Breweries->update_newsletter($rec_id);
		
		Location(admin_make_url("/admin.php?module=breweries&action=info&id=" . $rec_id));
		die();
		
		break;



	case 'festivalsAddAct':
		check_permissions($module_name, 'write');

		$form = $url['form'];

		$Festivals->add_relations_item($form['festival_id'], 'breweries', $form['rec_id']);

		$ActionsLogs->add('breweries', $form['rec_id'], 'update', 'added festival #' . $form['festival_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');
		
		// newsletter
		$Breweries->update_newsletter($form['rec_id']);

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	case 'festivalsDeleteAct':
		check_permissions($module_name, 'write');

		$data_relation = $Festivals->get_relations_item($url['id']);

		$Festivals->delete_relations_item($url['id']);

		$ActionsLogs->add('breweries', $data_relation['rec_id'], 'update', 'deleted festival #' . $data_relation['festival_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');

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
		
		
		
	case 'beer':
		if ($url['id']) {
			$data_festival = $Festivals->get_festivals_item($url['id']);
			$smarty->assign('data_festival', $data_festival);
			
			$smarty->assign('list_breweries', $Breweries->get_list(0, 10000, ['festival' => $data_festival['id'], 'order' => 'title']));
			$smarty->assign('list_beer', $Beer->get_list_grouped_by_brewery(0, 10000, ['festival_id' => $data_festival['id']]));
		}
		
		// festivals
		$smarty->assign('list_festivals', $Festivals->get_festivals_list());
		
		$page = "breweries.beer";
		break;


		
	default:
		$search = urldecode($url['search']);
		$search_festival = urldecode($url['search_festival']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 50;
		$Paging = new paging;
		
		$smarty->assign('search', $search);

		$smarty->assign('list', $Breweries->get_list($pg, $pg_items, ['search' => $url['search'], 'festival' => $search_festival]));
		
		$smarty->assign('search_festival', $search_festival);
		$smarty->assign('list_festivals', $Festivals->get_festivals_list());
		
		$pg_records = $Breweries->get_list_cnt(['search' => $url['search'], 'festival' => $search_festival]);
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=breweries&action=browse&search=" . urlencode($search) . "&pg="));
		break;
}