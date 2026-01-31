<?php
require_once(LIBS_MAIN_PATH . 'participants.class.php');
$Participants = new Participants;
require_once(LIBS_MAIN_PATH . 'festivals.class.php');
$Festivals = new Festivals();

$module_name = 'participants';

check_permissions($module_name, 'read');

$title = $Translate->get_item('participants');
$submenu = array(
		$Translate->get_item('list')	=>	'browse',
	);
//$adodb->debug = true;


switch ($url['_action_']) {
	case 'info':
		if ($url['id']) {
			$smarty->assign('data', $Participants->get_item($url['id']));

			$smarty->assign('list_festivals_unrelated', $Festivals->get_festivals_list_by_relations('participants', $url['id'], false));
			$smarty->assign('list_festivals_related', $Festivals->get_festivals_list_by_relations('participants', $url['id'], true));
		}

		$page = "participants.info";
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
				$Participants->update_item($rec_id, $form);

				$ActionsLogs->add('participants', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

		// add
		} else {
			if ($oki) {
				$rec_id = $Participants->add_item($form);

				$ActionsLogs->add('participants', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}
		
		// newsletter
		$Participants->update_newsletter($rec_id);

		Location(admin_make_url("/admin.php?module=participants&action=info&id=" . $rec_id));
		die();
		
		break;



	case 'festivalsAddAct':
		check_permissions($module_name, 'write');

		$form = $url['form'];

		$Festivals->add_relations_item($form['festival_id'], 'participants', $form['rec_id']);

		$ActionsLogs->add('participants', $form['rec_id'], 'update', 'added participants #' . $form['festival_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');
		
		// newsletter
		$Participants->update_newsletter($form['rec_id']);

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	case 'festivalsDeleteAct':
		check_permissions($module_name, 'write');

		$data_relation = $Festivals->get_relations_item($url['id']);

		$Festivals->delete_relations_item($url['id']);

		$ActionsLogs->add('participants', $data_relation['rec_id'], 'update', 'deleted participants #' . $data_relation['festival_id']);

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


		
	default:
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $Participants->get_list($pg, $pg_items, array('search' => $search)));

		$pg_records = $Participants->get_list_cnt(array('search' => $url['search']));
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=participants&action=browse&search=" . urlencode($search) . "&pg="));
		break;
}