<?php
require_once(LIBS_MAIN_PATH . 'pictures.class.php');
$Pictures = new Pictures;
require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
$Festivals = new Festivals();
require_once(LIBS_MAIN_PATH . 'breweries.class.php');
$Breweries = new Breweries();
require_once(LIBS_MAIN_PATH . 'participants.class.php');
$Participants = new Participants();

$module_name = 'pictures';

check_permissions($module_name, 'read');


$title = array($Translate->get_item('pictures'));
$submenu = array(
	$Translate->get_item('list')		=>	'browse',
);
//$adodb->debug = true;




switch ($url['_action_']) {

	// Info
	case 'info':
		$page = 'pictures.info';
		if ($url['popup']) {
			$main_page['popup'] = true;
		}


		if ($url['id']) {
			$data = $Pictures->get_item($url['id']);
			$smarty->assign('data', $data);

			$smarty->assign('is_last_record', $Pictures->is_last_record($data['id']));

			$smarty->assign('list_festivals_unrelated', $Festivals->get_festivals_list_by_relations('gallery', $url['id'], false));
			$smarty->assign('list_festivals_related', $Festivals->get_festivals_list_by_relations('gallery', $url['id'], true));

			$smarty->assign('list_breweries_unrelated', $Breweries->get_list_relations_gallery_free($url['id']));
			$smarty->assign('list_breweries_related', $Breweries->get_list_relations_gallery($url['id']));

			$smarty->assign('list_participants_unrelated', $Participants->get_list_relations_gallery_free($url['id']));
			$smarty->assign('list_participants_related', $Participants->get_list_relations_gallery($url['id']));

			$title[] = $Translate->get_item('edit');
		} else {
			$title[] = $Translate->get_item('add');
		}

		$smarty->assign('form_referer', $_SERVER['HTTP_REFERER']);

		break;



	// Info Act
	case 'infoAct':
		check_permissions($module_name, 'write');

		$form = $url['form'];
		$form2 = $url['form2'];

		// convert variables
		$oki = true;

		// check for errors
		if (!trim($form['keywords'])) {
			$_SESSION['main_messages'][] = $Translate->get_item('error keywords');
			$oki = false;
		}


		// Delete
		if ($form2['delete']) {
			$id = (int)$form2['id'];

			$data = $Pictures->get_item($id);
			if ($data && $Pictures->is_last_record($id)) {
				$Pictures->delete_item($id);

				$ActionsLogs->add('gallery', $id, 'delete', false, $data);
			}

			Location("/admin.php?module=pictures&action=browse");
			die();

			// Edit
		} else if ($form2['id']) {
			$id = $form2['id'];

			$data = $Pictures->get_item($id);
			if ($data && $oki) {
				$form5 = [
					'keywords'  =>  $form['keywords'],
				];
				$Pictures->update_item($id, $form5);

				$ActionsLogs->add('gallery', $id, 'update');
			}

		// Add
		} else {
			if (!$_FILES['image']['tmp_name']) {
				$_SESSION['main_messages'][] = $Translate->get_item('error file too big');
				$oki = false;
			}

			if ($oki) {
				$form['type'] = $_FILES['image']['type'];
				$form['tmp_name'] = $_FILES['image']['tmp_name'];
				$size = getimagesize($_FILES['image']['tmp_name']);
				$form['width'] = $size[0];
				$form['height'] = $size[1];
				$form['type'] = $size[2];
				$form['mime'] = $size['mime'];
				$form['content'] = file_get_contents($_FILES['image']['tmp_name']);

				$id = $Pictures->add_item($form);

				$ActionsLogs->add('gallery', $id, 'insert');
			}
		}

		if ($url['referer']) {
			Location($url['referer']);
		} else if ($id) {
			Location("/admin.php?module=pictures&action=info&id={$id}");
		} else {
			Location("/admin.php?module=pictures&action=browse");
		}
		die();
		break;



	case 'festivalsAddAct':
		check_permissions($module_name, 'write');

		$form = $url['form'];

		$Festivals->add_relations_item($form['festival_id'], 'gallery', $form['rec_id']);

		$ActionsLogs->add('gallery', $form['rec_id'], 'update', 'added festival #' . $form['festival_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	case 'festivalsDeleteAct':
		check_permissions($module_name, 'write');

		$data_relation = $Festivals->get_relations_item($url['id']);

		$Festivals->delete_relations_item($url['id']);

		$ActionsLogs->add('gallery', $data_relation['rec_id'], 'update', 'deleted festival #' . $data_relation['festival_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	case 'relationsAddAct':
		check_permissions($module_name, 'write');

		$form = $url['form'];

		$Pictures->add_relations_item($form['gallery_id'], $form['rec_table'], $form['rec_id']);

		$ActionsLogs->add('gallery', $form['gallery_id'], 'update', 'added ' . $form['rec_table'] . ' #' . $form['rec_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	case 'relationsDeleteAct':
		check_permissions($module_name, 'write');

		$data_relation = $Festivals->get_relations_item($url['id']);

		$Pictures->delete_relations_item($url['id']);

		$ActionsLogs->add('gallery', $url['id'], 'update', 'deleted ' . $data_relation['rec_table'] . ' #' . $data_relation['rec_id']);

		$_SESSION['main_messages'][] = $Translate->get_item('success data updated');

		Location($_SERVER['HTTP_REFERER']);
		die();

		break;



	// Browse
	case "browse":
	case "browsePopup":
	default:
		if ($url['popup']) {
			$page = 'pictures.browse.popup';
			$main_page['popup'] = true;

			$srch = array(
				'keyword'	=>	urldecode($url['kw']),
				'type'		=>	urldecode($url['type']),
				'rel'		=>	urldecode($url['rel']),
				'place'		=>	urldecode($url['place']),
				'myown'		=>	urldecode($url['myown']),
			);
			$smarty->assign('srch', $srch);
		}

		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 20;
		$Paging = new paging;

		$smarty->assign('search', $search);
		$smarty->assign('popup', $url['popup']);

		$smarty->assign('list', $Pictures->get_list($pg, $pg_items, array('search' => $search)));

		$pg_records = $Pictures->get_list_cnt(array('search' => $url['search']));
		if ($url['popup']) {
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=pictures&action=browse&search=" . urlencode($search) . "&popup=1&type=" . urlencode($srch['type']) . "&rel=" . urlencode($srch['rel']) . "&pg="));
		} else {
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=pictures&action=browse&search=" . urlencode($search) . "&pg="));
		}
		break;
}



?>