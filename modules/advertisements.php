<?php
require_once(LIBS_USER_PATH . 'advertisements.class.php');
$Advertisements = new Advertisements;



if ($url['_module_'] == 'advertisements') {
	switch ($url['_action_']) {

		// get random modal item
		case 'modalGetRandHtmlAjax':
			if (!$_COOKIE['advertisements_modal_displayed']) {
				setcookie('advertisements_modal_displayed', '1', time()+10*60, '/');
				$data = $Advertisements->get_modal_item_rand();
				if ($data) {
					echo $data['rec_html'];
				}
			}
			die();
			break;
	}
}
?>