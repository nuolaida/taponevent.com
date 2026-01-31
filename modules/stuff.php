<?php

require_once(LIBS_MAIN_PATH . 'texts.class.php');
$Texts = new Texts;

switch ($url['_action_']) {
	case 'sitemaps':
		$smarty->assign('http_protocol', $page_special_config['protocol']);
		$smarty->assign('http_domain', $page_special_config['domain']);

		header('Content-Type: application/xml; charset=utf-8');
		echo $smarty->fetch('stuff.sitemaps.index.tpl');
		die();
		break;

	case 'sitemaps_lt':
	case 'sitemaps_en':
		switch ($url['_action_']) {
			case 'sitemaps_lt':
				$dispatch->changeLanguage('lt');
				break;
			case 'sitemaps_en':
				$dispatch->changeLanguage('en');
				break;
		}
		$smarty->assign('http_protocol', $page_special_config['protocol']);
		$smarty->assign('http_domain', $page_special_config['domain']);

		// festivals
		require_once(LIBS_MAIN_PATH . 'festivals.class.php');
		$Festivals = new Festivals();
		$smarty->assign('festivals_list', $Festivals->get_festivals_list(['active' => false]));

		// breweries show
		require_once(LIBS_MAIN_PATH . 'breweries.class.php');
		$Breweries = new Breweries();
		$smarty->assign('breweries_list', $Breweries->get_list(0, 10000));

		// participants
		require_once(LIBS_MAIN_PATH . 'participants.class.php');
		$Participants = new Participants();
		$smarty->assign('participants_list', $Participants->get_list(0, 10000));

		// texts
		require_once(LIBS_MAIN_PATH . 'texts.class.php');
		$Texts = new Texts();
		$smarty->assign('texts_list', $Texts->get_list(0, 10000, ['active' => true]));

		header('Content-Type: application/xml; charset=utf-8');
		echo $smarty->fetch('stuff.sitemaps.tpl');
		die();
		break;
}