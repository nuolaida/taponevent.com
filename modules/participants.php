<?php
require_once(LIBS_MAIN_PATH . 'participants.class.php');
$Participants = new Participants();


switch ($url['_action_']) {
    case 'show':
        require_once(LIBS_MAIN_PATH . 'festivals.class.php');
        $Festivals = new Festivals();
        require_once(LIBS_MAIN_PATH . 'pictures.class.php');
        $Pictures = new Pictures();

        // festival
        $data_participant = $Participants->get_item($url['id']);
        $smarty->assign('data_participant', $data_participant);
        if (!$data_participant) {
            my_header_code(404);
            Location('/');
            die();
        }

        // url validation
        $this_url = $dispatch->buildUrl('/index.php?module=participants&action=show&id=' . $data_participant['id'] . '&rec_url_name=' . $data_participant['title']);
        $syspage['page_og_url'] = $this_url;
        if ($this_url != $_SERVER['REQUEST_URI']) {
            my_header_code(301);
            Location($this_url);
            die();
        }

        // gallery
        $rel_gallery = $Pictures->get_relations_list('participants', $data_participant['id']);
        $smarty->assign('list_gallery', $rel_gallery);

        if ($rel_gallery) {
            $photo = reset($rel_gallery);
            $photo = $photo['gallery_id'];
        } else {
            $photo = $data_participant['gallery_id'];
        }
        $smarty->assign('header_photo', $photo);


        // festivals
        $smarty->assign('list_festivals', $Festivals->get_relations_list_by_relations('participants', $data_participant['id']));

        $syspage['page_title'] = array($data_participant['title'], $Translate->get_item('participants'));
        $syspage['page_description'] = $data_participant['title'];
        $syspage['page_og_title'] = $syspage['page_title'];
        $syspage['page_og_description'] = $syspage['page_description'];

        $mod = $smarty->fetch('participants.show.tpl');
        break;
}


