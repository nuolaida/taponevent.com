<?php
require_once(LIBS_MAIN_PATH . 'texts.class.php');
$Texts = new Texts;
$module_name = 'texts';
//$adodb->debug = true;

check_permissions($module_name, 'read');

$title = $Translate->get_item('texts');
$submenu = array(
		$Translate->get_item('list')	=>	'browse',
	);
//$adodb->debug = true;


switch ($url['_action_']) {
	case 'info':
		if ($url['id']) {
			$data = $Texts->get_item($url['id']);
			$smarty->assign('data', $data);
		}
		
		$page = "texts.info";
		break;



	case 'infoAct':
		check_permissions($module_name, 'write');

		$oki = true;
		$form = $url['form'];
		$form2 = $url['form2'];

		// check for errors
		if (!trim($form['title'])) {
			$_SESSION['main_messages'][] = $Translate->get_item('error title');
		}

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$Texts->update_item($rec_id, $form);

				$ActionsLogs->add('texts', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $Texts->add_item($form);

				$ActionsLogs->add('texts', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=texts&action=info&id=" . $rec_id));
		die();
		
		break;


		
	default:
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $Texts->get_list($pg, $pg_items, array('search' => $search)));

		$pg_records = $Texts->get_list_cnt(array('search' => $url['search']));
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=texts&action=browse&search=" . urlencode($search) . "&pg="));
		break;
}

?>
