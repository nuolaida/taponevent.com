<?php
require_once(LIBS_MAIN_PATH . 'Catalogue.class.php');
$Catalogue = new Catalogue();


switch ($url['_action_']) {
    case 'show':
        $data = $Catalogue->get_item($url['id']);
        $smarty->assign('data', $data);
	    $syspage['page_og_image'] = $dispatch->buildUrl('/index.php?module=images&action=show&id=' . $data['gallery_id'] . '&type=any1000x1000');
	    $syspage['page_fb_image'] = $syspage['page_og_image'];
        if (!$data) {
            my_header_code(404);
            Location('/');
            die();
        }

        // url validation
        $this_url = $dispatch->buildUrl('/index.php?module=catalogue&action=show&id=' . $data['id'] . '&rec_url_name=' . $data['title']);
        $syspage['page_og_url'] = $this_url;
        if ($this_url != $_SERVER['REQUEST_URI']) {
            my_header_code(301);
            Location($this_url);
            die();
        }
	    
	    $smarty->assign('list_drink_types', $Catalogue->module_config['drink_types']);

		// dates
	    $smarty->assign('list_dates', $Catalogue->get_dates_list($data['id']));

		switch ($data['type']) {
			case 'festivals':
				$syspage['page_title'] = array($data['title'], $Translate->get_item('festivals'));
				$syspage['page_description'] = $data['title'] . '. ' . cut_html_text($data['description'], 120);
				$syspage['page_og_title'] = $syspage['page_title'];
				$syspage['page_og_description'] = $syspage['page_description'];
				
				$mod = $smarty->fetch('catalogue.festivals.show.tpl');
				break;
			case 'competitions':
				$syspage['page_title'] = array($data['title'], $Translate->get_item('competitions'));
				$syspage['page_description'] = $data['title'] . '. ' . cut_html_text($data['description'], 120);
				$syspage['page_og_title'] = $syspage['page_title'];
				$syspage['page_og_description'] = $syspage['page_description'];
				
				$mod = $smarty->fetch('catalogue.competitions.show.tpl');
				break;
		}
        break;
	
	
	
	case 'festivals':
		
		$smarty->assign('list', $Catalogue->get_list('festivals', 0, 500));
		
		$smarty->assign('list_drink_types', $Catalogue->module_config['drink_types']);
		
		$syspage['page_title'] = [$Translate->get_item('festivals')];
		$syspage['page_description'] = $Translate->get_item('festivals') . ': '. $Translate->get_item('beer') . ', ' . $Translate->get_item('mead') . ', ' . $Translate->get_item('cider') . ', ' . $Translate->get_item('wine');
		$syspage['page_og_title'] = $syspage['page_title'];
		$syspage['page_og_description'] = $syspage['page_description'];
		$syspage['page_og_image'] = $dispatch->buildUrl('/index.php?module=images&action=show&id=' . $page_special_config['default_sharing_image_id']['catalogue_festivals'] . '&type=any1000x1000');
		
		$mod = $smarty->fetch('catalogue.festivals.tpl');
		break;
	
	
	
	case 'competitions':
		
		$smarty->assign('list', $Catalogue->get_list('competitions', 0, 500));
		
		$smarty->assign('list_drink_types', $Catalogue->module_config['drink_types']);
		
		$syspage['page_title'] = [$Translate->get_item('competitions')];
		$syspage['page_description'] = $Translate->get_item('competitions') . ': '. $Translate->get_item('beer') . ', ' . $Translate->get_item('mead') . ', ' . $Translate->get_item('cider') . ', ' . $Translate->get_item('wine');
		$syspage['page_og_title'] = $syspage['page_title'];
		$syspage['page_og_description'] = $syspage['page_description'];
		$syspage['page_og_image'] = $dispatch->buildUrl('/index.php?module=images&action=show&id=' . $page_special_config['default_sharing_image_id']['catalogue_competitions'] . '&type=any1000x1000');
		
		$mod = $smarty->fetch('catalogue.competitions.tpl');
		break;
}


