<?php
	require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
	$Festivals = new Festivals();
	
	$module_name = 'festivals';
	$smarty->assign('module_name', $module_name);

	switch ($page_action) {
		case 'work':
			$data_festival = $Festivals->get_festivals_item_by_company_user($_SESSION['app']['festival'], $_SESSION['user']['id']);
			$smarty->assign('data_festival', $data_festival);

			if (is_development_version() || $Festivals->testing) {
				$smarty->assign('testing_card', $Festivals->test_nfc_card);
			}
			
			if ($data_festival['user_app_type'] == 'seller') {
				$list_prices = $Festivals->get_festivals_users_prices_list($_SESSION['app']['festival'], $_SESSION['user']['id']);
				$smarty->assign('list_prices', $list_prices);
				
				$template_name = $module_name . '.work.seller';
			} elseif ($data_festival['user_app_type'] == 'cashmachine') {
				
				$template_name = $module_name . '.work.cashmachine';
			} else {
				Location('/app.php');
				die();
			}
			break;
			
		case 'checkout':
			$nfc_id = ($url['nfc_id'] ?: null);
			$price_total = ($url['total_sum'] ?? 0);
			$request_id = $url['request_id'] ?? '';
			$price_items = [];
			$price_total_count = 0;
			if (isset($_POST['cart_json'])) {
				$cart = json_decode($url['cart_json'], true);
				foreach ($cart as $item) {
					$price_items[] = [
						'price_id' => (int)$item['item_id'],
						'title' => $item['name'],
						'price' => (float)$item['price'],
					];
					$price_total_count += (float)$item['price'];
				}
			}
			
			if (!is_development_version() && substr($nfc_id, 0, strlen($Festivals->test_nfc_card)) == $Festivals->test_nfc_card && $Festivals->testing) {
				$_SESSION['main_messages'][] = 'TEST DATA';
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user || $data_user['user_app_type'] != 'seller') {
				$_SESSION['main_messages'][] = $Translate->get_item('error user has no rights');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			if (!$nfc_id) {
				$_SESSION['main_messages'][] = $Translate->get_item('error not valid nfc card');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			if ($price_total_count != $price_total) {
				$_SESSION['main_messages'][] = $Translate->get_item('error not valid total price');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			if (!$price_total_count) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty cart');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			if (!$request_id) {
				$_SESSION['main_messages'][] = $Translate->get_item('error request id dublicate');
				Location($_SERVER['HTTP_REFERER']);
				die();
			} else {
				if ($Festivals->check_checkout_by_request_id($request_id)) {
					$_SESSION['main_messages'][] = $Translate->get_item('error request id dublicate');
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
			}
			
			$wallet = $Festivals->get_wallet_item($nfc_id);
			if ($wallet < $price_total_count) {
				$_SESSION['main_messages'][] = $Translate->get_item('error not enough money');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			foreach ($price_items as $item) {
				$form5 = [
					'festival_id' => (int)$_SESSION['app']['festival'],
					'user_id' => (int)$_SESSION['user']['id'],
					'rec_time' => time(),
					'nfc_id' => $nfc_id,
					'price_id' => ((int)$item['price_id']) ?: null,
					'price' => ((float)$item['price']) ? (0 - $item['price']) : 0,
					'request_id' => $request_id,
				];
				$Festivals->add_checkout_item($form5);
			}
			
			$wallet = $Festivals->get_wallet_item($nfc_id);
			
			Location('?module=festivals&action=checkoutOk&checkout=' . $price_total_count . '&wallet=' . $wallet);
			die();
			break;
			
		case 'selectAct':
			$data = $Festivals->get_festivals_item_by_company_user($url['id'], $_SESSION['user']['id']);
			if ($data) {
				$_SESSION['app']['festival'] = $data['festival_id'];
				Location('/app.php?module=festivals&action=work');
				die();
			}
			Location('/app.php');
			die();
			break;
		
		case 'checkoutOk':
			$smarty->assign('checkout', (float)$url['checkout']);
			$smarty->assign('wallet', (float)$url['wallet']);
			
			$template_name = $module_name . '.checkout.ok';
			break;
		
		case 'checkoutList':
			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user) {
				Location('/app.php');
				die();
			}
			
			$incomes_user = $Festivals->get_checkout_sum_by_user($_SESSION['user']['id'], $_SESSION['app']['festival']);
			$smarty->assign('incomes_user', $incomes_user);
			
			$incomes_company = $Festivals->get_checkout_sum_by_company($data_user['company_id'], $_SESSION['app']['festival']);
			$smarty->assign('incomes_company', $incomes_company);
			
			$list_festival = $Festivals->get_checkout_list(
				0,
				30,
				[
					'festival' => $_SESSION['app']['festival'],
					'user' => $_SESSION['user']['id'],
				],
			);
			$smarty->assign('list', $list_festival);
			
			$template_name = $module_name . '.checkout.list';
			break;
		
		case 'cashmachineAct':
			$nfc_id = ($url['nfc_id'] ?: null);
			$topup_amount = ($url['topup_amount'] ?? 0);
			$request_id = $url['request_id'] ?? '';
			
			if (!is_development_version() && substr($nfc_id, 0, strlen($Festivals->test_nfc_card)) == $Festivals->test_nfc_card && !$Festivals->testing) {
				$_SESSION['main_messages'][] = 'TEST DATA';
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user || $data_user['user_app_type'] != 'cashmachine') {
				$_SESSION['main_messages'][] = $Translate->get_item('error user has no rights');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			if (!$nfc_id) {
				$_SESSION['main_messages'][] = $Translate->get_item('error not valid nfc card');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			if (!$request_id) {
				$_SESSION['main_messages'][] = $Translate->get_item('error request id dublicate');
				Location($_SERVER['HTTP_REFERER']);
				die();
			} else {
				if ($Festivals->check_checkout_by_request_id($request_id)) {
					$_SESSION['main_messages'][] = $Translate->get_item('error request id dublicate');
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
			}
			
			if (!(float)$topup_amount) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty cart');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			if ((float)$topup_amount > $Festivals->max_topup_ammount) {
				$_SESSION['main_messages'][] = $Translate->get_item('error not enough money');
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			
			$form5 = [
				'festival_id' => (int)$_SESSION['app']['festival'],
				'user_id' => (int)$_SESSION['user']['id'],
				'rec_time' => time(),
				'nfc_id' => $nfc_id,
				'price' => (float)$topup_amount,
				'request_id' => $request_id,
			];
			$Festivals->add_checkout_item($form5);
			
			$wallet = $Festivals->get_wallet_item($nfc_id);
			
			Location('?module=festivals&action=cashmachineOk&topup=' . $topup_amount . '&wallet=' . $wallet);
			die();
			break;
		
		case 'cashmachineOk':
			$smarty->assign('topup', (float)$url['topup']);
			$smarty->assign('wallet', (float)$url['wallet']);
			
			$template_name = $module_name . '.cashmachine.ok';
			break;
		
		case 'select':
		default:
			$list = $Festivals->get_festivals_list_by_company_user($_SESSION['user']['id']);
			$smarty->assign('list', $list);
			
			$template_name = $module_name . '.select';
			break;
	}


