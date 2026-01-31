<?php

use BestPub\News\Model\News;

require_once(LIBS_MAIN_PATH . 'news.class.php');
$News = new \News;
$module_name = 'news';

check_permissions($module_name, 'read');

$title = $Translate->get_item('news');
$submenu = array(
		$Translate->get_item('list')	=>	'browse',
	);
//$adodb->debug = true;


switch ($url['_action_']) {
	case 'info':
		if ($url['id']) {
			$smarty->assign('data', News::fetch($url['id']));
		}
		
		$page = "news.info";
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
		$form['rec_time'] = ($form2['rec_time']) ? strtotime($form2['rec_time']) : null;

		// edit
		if ($form2['id']) {
			$rec_id = $form2['id'];
			if ($oki) {
				$News->update_item($rec_id, $form);

				$ActionsLogs->add('news', $rec_id, 'update');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			}

			// add
		} else {
			if ($oki) {
				$rec_id = $News->add_item($form);

				$ActionsLogs->add('news', $rec_id, 'insert');

				$_SESSION['main_messages'][] = $Translate->get_item('success data saved');
			} else {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

		}

		Location(admin_make_url("/admin.php?module=news&action=info&id=" . $rec_id));
		die();
		
		break;


		
	default:
		$search = urldecode($url['search']);
		$pg = ((int)$url['pg']) ? $url['pg'] : 0;
		$pg_items = 30;
		$Paging = new paging;

		$smarty->assign('search', $search);

		$smarty->assign('list', $News->get_list($pg, $pg_items, array('search' => $search)));

		$pg_records = $News->get_list_cnt(array('search' => $url['search']));
		$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "/admin.php?module=news&action=browse&search=" . urlencode($search) . "&pg="));
		break;
}