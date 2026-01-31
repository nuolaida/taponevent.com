<?php
	require_once(LIBS_MAIN_PATH . 'index.class.php');
	$Index = new Index();


	$list_blocks = $Index->get_list_grouped();

	$smarty->assign('list_topimages', $list_blocks['topimages']);
	$smarty->assign('list_meetup', $list_blocks['meetup']);
	$smarty->assign('list_about', $list_blocks['about']);
	$smarty->assign('list_tickets', $list_blocks['tickets']);
	$smarty->assign('list_sponsors', $list_blocks['sponsors']);
	$smarty->assign('list_place', $list_blocks['place']);
	$smarty->assign('list_map', $list_blocks['map']);
	
	require_once(LIBS_MAIN_PATH . 'breweries.class.php');
	$Breweries = new Breweries();
	$smarty->assign('list_breweries', $Breweries->get_list(0, 100, ['festival' => 'active', 'order' => 'rand']));
	
	require_once(LIBS_MAIN_PATH . 'Conference.class.php');
	$Conference = new Conference();
	$smarty->assign('list_conference', $Conference->get_list(0, 100, ['festival' => 'active']));
	
	require_once(LIBS_MAIN_PATH . 'participants.class.php');
	$Participants = new Participants();
	$smarty->assign('list_participants', $Participants->get_list(0, 100, ['festival' => 'active', 'order' => 'rand']));
	
	require_once(LIBS_MAIN_PATH . 'pictures.class.php');
	$Pictures = new Pictures();
	$smarty->assign('list_gallery', $Pictures->get_list_by_festival());
	
	$smarty->assign('page_google_api_key', $page_special_config['google_api_key']);

	$mod = $smarty->fetch('title.tpl');
	