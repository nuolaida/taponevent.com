<?php
require_once(LIBS_MAIN_PATH . 'texts.class.php');
$Texts = new Texts;

switch ($url['_action_']) {
    case 'show':
        $data = $Texts->get_item($url['id']);
        $smarty->assign('data', $data);

	    // url validation
	    $this_url = $dispatch->buildUrl('/index.php?module=texts&action=show&id=' . $data['id'] . '&rec_url_name=' . $data['title']);
	    $syspage['page_og_url'] = $this_url;
	    if ($this_url != $_SERVER['REQUEST_URI']) {
		    my_header_code(301);
		    Location($this_url);
		    die();
	    }

	    $smarty->assign('social_links', $page_special_config['social']);

	    $syspage['page_title'] = array($data['title'], $Translate->get_item('information'));
	    $syspage['page_description'] = $data['title'];
	    $syspage['page_og_title'] = $syspage['page_title'];
	    $syspage['page_og_description'] = $syspage['page_description'];

		if ($data['template']) {
			$mod = my_fetch('text.show.' . $data['template'] . '.tpl');
		} else {
			$mod = my_fetch('text.show.tpl');
        }
        break;
}