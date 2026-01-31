<?php
require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
$Festivals = new Festivals();


switch ($url['_action_']) {
	case 'show':
		require_once(LIBS_MAIN_PATH . 'breweries.class.php');
		$Breweries = new Breweries();
		require_once(LIBS_MAIN_PATH . 'pictures.class.php');
		$Pictures = new Pictures();
		require_once(LIBS_MAIN_PATH . 'participants.class.php');
		$Participants = new Participants();

		// festival
		$data_festival = $Festivals->get_festivals_item($url['id']);
		$smarty->assign('data_festival', $data_festival);
		if (!$data_festival) {
			my_header_code(404);
			Location('/');
			die();
		}

		// url validation
		$this_url = $dispatch->buildUrl('/index.php?module=festivals&action=show&id=' . $data_festival['id'] . '&rec_url_name=' . $data_festival['title']);
		$syspage['page_og_url'] = $this_url;
		if ($this_url != $_SERVER['REQUEST_URI']) {
			my_header_code(301);
			Location($this_url);
			die();
		}

		// gallery
		$smarty->assign('list_gallery', $Pictures->get_list_by_festival($data_festival['id']));


		// breweries
		$smarty->assign('list_breweries', $Breweries->get_list(0, 100, ['festival' => $data_festival['id'], 'order' => 'rand']));

		// participants
		$smarty->assign('list_participants', $Participants->get_list(0, 100, ['festival' => $data_festival['id'], 'order' => 'rand']));

		$syspage['page_title'] = array($data_festival['title'], $Translate->get_item('festivals'));
		$syspage['page_description'] = $data_festival['title'];
		$syspage['page_og_title'] = $syspage['page_title'];
		$syspage['page_og_description'] = $syspage['page_description'];

		$mod = $smarty->fetch('festivals.show.tpl');
		break;
}


