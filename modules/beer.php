<?php
require_once(LIBS_MAIN_PATH . 'Beer.class.php');
$Beer = new Beer();


switch ($url['_action_']) {
    case 'show':
	    require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
	    $Festivals = new Festivals();
	    require_once(LIBS_MAIN_PATH . 'breweries.class.php');
	    $Breweries = new Breweries();

        // festival
        $data_festival = $Festivals->get_festivals_item($url['id']);
        $smarty->assign('data_festival', $data_festival);
        if (!$data_festival) {
            my_header_code(404);
            Location('/');
            die();
        }
	    
	    $smarty->assign('list_breweries', $Breweries->get_list(0, 10000, ['festival' => $data_festival['id'], 'order' => 'title']));
	    $smarty->assign('list_beer', $Beer->get_list_grouped_by_brewery(0, 10000, ['festival_id' => $data_festival['id']]));
	    
	    
	    // url validation
        $this_url = $dispatch->buildUrl('/index.php?module=beer&action=show&id=' . $data_festival['id'] . '&rec_url_name=' . $data_festival['title']);
        $syspage['page_og_url'] = $this_url;
        if ($this_url != $_SERVER['REQUEST_URI']) {
            my_header_code(301);
            Location($this_url);
            die();
        }


        // festivals
        $smarty->assign('list_festivals', $Festivals->get_relations_list_by_relations('breweries', $data_brewery['id']));

        $syspage['page_title'] = array($Translate->get_item('beer'), $data_festival['title']);
        $syspage['page_description'] = $data_festival['title'] . ' ' . $Translate->get_item('beer');
        $syspage['page_og_title'] = $syspage['page_title'];
        $syspage['page_og_description'] = $syspage['page_description'];

        $mod = $smarty->fetch('beer.show.tpl');
        break;


}


