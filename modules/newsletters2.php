<?php

require_once(LIBS_MAIN_PATH . 'Newsletters2.class.php');
$Newsletters = new Newsletters2();
$module_name = 'newsletters2';

switch ($url['_action_']) {
	case 'send':
		$items = ((int)$url['id']) ?: null;
		echo $Newsletters->send_email_scheduled($items);
		die();
		break;


	case 'track':
		$email_id = $url['id'];
		$user_id = $url['idd'];

		$item = $Newsletters->get_sent_item($email_id, $user_id);
		if ($item) {
			$cnt = ((int)$item['opened_times']) ? $item['opened_times'] + 1 : 1;
			$Newsletters->update_sent_item($email_id, $user_id, ['opened_times' => $cnt]);
		}

		header('Pragma: public');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Cache-Control: private', false);
		header('Content-Disposition: attachment; filename="pixel.gif"');
		header('Content-Transfer-Encoding: binary');
		header('Content-Length: ' . filesize(IMAGES_PATH . 'pixel.gif'));
		readfile(IMAGES_PATH . 'pixel.gif');
		die();
		break;



	case 'unsubscribe':
		list($user_id, $time) = explode(':', md5_decrypt($url['item_url']));
		if ((int)$user_id) {
			$Newsletters->update_relations_users_categories_unsubscribe($user_id);
		}

		$_SESSION['messages_unsubscribe'] = $Translate->get_item('unsubscribe successfull');

		Location($dispatch->buildUrl('/index.php?module=' . $module_name . '&action=unsubscribeMessage'));
		break;



	case 'unsubscribeMessage':
		$smarty->assign('message', $_SESSION['messages_unsubscribe']);
		$mod = $smarty->fetch($module_name . '.unsubscribe.tpl');
		break;


}