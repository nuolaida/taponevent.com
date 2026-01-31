<?php
	require_once(LIBS_MAIN_PATH . 'Competition.class.php');
	$Competition = new Competition();
	require_once(LIBS_MAIN_PATH . 'festivals.class.php');
	$Festivals = new Festivals();
	$module_name = 'competition';


switch ($url['_action_']) {
	case 'label':
		list($item_id, $time) = explode(':', md5_decrypt($url['item_url']));
		if ((int)$item_id) {
			$data_item = $Competition->get_item($item_id);
		}
		if (!$data_item) {
			die();
		}
		
		$smarty->assign('data_item', $data_item);
		echo my_fetch($module_name . '.label.tpl');
		die();
		break;
		
		
		
	case 'results':
		$data_festival = $Festivals->get_festivals_item($url['id']);
		$smarty->assign('data_festival', $data_festival);
		if (!$data_festival) {
			my_header_code(404);
			Location('/');
			die();
		}
		
		$data_settings = $Competition->get_settings_item($data_festival['id']);
		$smarty->assign('data_settings', $data_settings);
		
		if ($data_settings['show_judges']) {
			$list_judges = $Competition->get_judges_list_of_the_festival($data_festival['id']);
			$smarty->assign('list_judges', $list_judges);
		}
		$smarty->assign('default_judge_photo_id', $page_special_config['default_images']['default']);
		
		if ($data_settings['show_results']) {
			$list_medals = $Competition->get_list(0, 10000, ['medal' => true]);
			$list_medals_sorted = [];
			foreach ($list_medals as $item) {
				$list_medals_sorted[$item['medal_categories_id']]['medals'][] = $item;
			}
			$smarty->assign('list_medals', $list_medals_sorted);
			
			$list_medal_categories = $Competition->get_medal_categories_list(0, 500, ['festival' => $data_festival['id']]);
			$list_medal_categories_assoc = [];
			if ($list_medal_categories) {
				foreach ($list_medal_categories as $item) {
					$list_medal_categories_assoc[$item['id']] = $item['medal_title'];
				}
			}
			$smarty->assign('list_medal_categories_assoc', $list_medal_categories_assoc);
		}
		
		$syspage['page_title'] = [$data_settings['title']];
		$syspage['page_description'] = clear_from_html($data_settings['description']);
		$syspage['page_og_title'] = $syspage['page_title'];
		$syspage['page_og_description'] = $syspage['page_description'];
		
		$mod = $smarty->fetch($module_name . '.results.tpl');
		break;

}


