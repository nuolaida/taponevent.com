<?php
require_once(LIBS_MAIN_PATH . 'breweries.class.php');
$Breweries = new Breweries();


switch ($url['_action_']) {
    case 'show':
        require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
        $Festivals = new Festivals();
        require_once(LIBS_MAIN_PATH . 'pictures.class.php');
        $Pictures = new Pictures();

        // festival
        $data_brewery = $Breweries->get_item($url['id']);
        $smarty->assign('data_brewery', $data_brewery);
        if (!$data_brewery) {
            my_header_code(404);
            Location('/');
            die();
        }

        // url validation
        $this_url = $dispatch->buildUrl('/index.php?module=breweries&action=show&id=' . $data_brewery['id'] . '&rec_url_name=' . $data_brewery['title']);
        $syspage['page_og_url'] = $this_url;
        if ($this_url != $_SERVER['REQUEST_URI']) {
            my_header_code(301);
            Location($this_url);
            die();
        }

        // gallery
        $rel_gallery = $Pictures->get_relations_list('breweries', $data_brewery['id']);
        $smarty->assign('list_gallery', $rel_gallery);

        if ($rel_gallery) {
            $photo = reset($rel_gallery);
            $photo = $photo['gallery_id'];
        } else {
            $photo = $data_brewery['gallery_id'];
        }
        $smarty->assign('header_photo', $photo);

        // festivals
        $smarty->assign('list_festivals', $Festivals->get_relations_list_by_relations('breweries', $data_brewery['id']));

        $syspage['page_title'] = array($data_brewery['title'], $Translate->get_item('breweries'));
        $syspage['page_description'] = $data_brewery['title'];
        $syspage['page_og_title'] = $syspage['page_title'];
        $syspage['page_og_description'] = $syspage['page_description'];

        $mod = $smarty->fetch('breweries.show.tpl');
        break;



    case 'map':
        $smarty->assign('config_google_api_key', $page_special_config['google_api_key']);

        $smarty->assign('list_breweries', $Breweries->get_list_attenders_grouped_by_country());

        $syspage['page_title'] = [$Translate->get_item('breweries map'), $Translate->get_item('archive')];
        $syspage['page_description'] = $Translate->get_item('breweries map');
        $syspage['page_og_title'] = $syspage['page_title'];
        $syspage['page_og_description'] = $syspage['page_description'];

        $mod = $smarty->fetch('breweries.map.tpl');
        break;
}


