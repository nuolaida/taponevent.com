<?php

switch ($url['_action_']) {
    default:
	    $valid_ids = [2024, 2023];
	    if (!in_array((int)$url['id'], $valid_ids)) {
		    $id = reset($valid_ids);
	    } else {
		    $id = (int)$url['id'];
	    }
		$data = [
			2024 => [
				'block_slider' => [
					'photos' => [395, 413],
					'texts' => [
						$id,
						$Translate->get_item('sommelier page title'),
						''
					],
				],
				'page_og_image_id' => 413,
				'gallery' => [],
			],
			2023 => [
				'block_slider' => [
					'photos' => [392, 409, 397],
					'texts' => [
						$Translate->get_item('sommelier first time'),
						$Translate->get_item('sommelier page title'),
						$id,
					],
				],
				'page_og_image_id' => 415,
				'gallery' => [392, 393, 394, 395, 396, 397, 398, 399, 400, 401, 402, 403, 404, 405, 406, 407, 408, 409, 410, 411, 412, 413, 414, 415, 416, 417, 418],
			],
		];
		

	    // url validation
        $this_url = $dispatch->buildUrl('/index.php?module=sommelier&action=show&id=' . $id);
        $syspage['page_og_url'] = $this_url;
        if ($this_url != $_SERVER['REQUEST_URI']) {
            my_header_code(301);
            Location($this_url);
            die();
        }
	    
	    $smarty->assign('block_slider', $data[$id]['block_slider']);
	    $smarty->assign('block_gallery', $data[$id]['gallery']);
	    $smarty->assign('config_valid_ids', $valid_ids);
	    $smarty->assign('config_active_id', $id);
	    $smarty->assign('title_year', $id);



        $syspage['page_title'] = [$Translate->get_item('sommelier page title'), $id];
        $syspage['page_description'] = $Translate->get_item('sommelier page description');
        $syspage['page_og_title'] = $syspage['page_title'];
	    $syspage['page_og_image'] = $dispatch->buildUrl('/index.php?module=images&action=show&id=' . $data[$id]['page_og_image_id'] . '&type=any1000x1000');

        $mod = $smarty->fetch('sommelier' . $url['id'] . '.tpl');
        break;


}


