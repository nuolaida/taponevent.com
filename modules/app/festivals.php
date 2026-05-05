<?php
	require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
	$Festivals = new Festivals();

	// Ensure expected globals are available
	global $smarty, $page_action, $url, $Translate;

	$module_name = 'festivals';
	$smarty->assign('module_name', $module_name);

	// Helper: detect AJAX (XHR) or client expecting JSON
	function is_ajax_request() {
	    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') return true;
	    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
	    if (stripos($accept, 'application/json') !== false) return true;
	    return false;
	}

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
			// Read params (prefer POST for form/AJAX)
			$nfc_id = $_POST['nfc_id'] ?? $url['nfc_id'] ?? null;
			$price_total = isset($_POST['total_sum']) ? (float)$_POST['total_sum'] : (float)($url['total_sum'] ?? 0);
			$request_id = $_POST['request_id'] ?? $url['request_id'] ?? '';
			$price_items = [];
			$price_total_count = 0;

			// get cart JSON from POST first (form or AJAX) or from $url
			$cart_json = $_POST['cart_json'] ?? $url['cart_json'] ?? null;
			if ($cart_json) {
				$cart = json_decode($cart_json, true) ?: [];
				foreach ($cart as $item) {
					$price_items[] = [
						'price_id' => (int)($item['item_id'] ?? 0),
						'title' => $item['name'] ?? '',
						'price' => (float)($item['price'] ?? 0),
					];
					$price_total_count += (float)($item['price'] ?? 0);
				}
			}

			$isAjax = is_ajax_request();

			if (!is_development_version() && $nfc_id !== null && substr((string)$nfc_id, 0, strlen($Festivals->test_nfc_card)) == $Festivals->test_nfc_card && $Festivals->testing) {
				$_SESSION['main_messages'][] = 'TEST DATA';
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'TEST DATA']); exit; }
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user || ($data_user['user_app_type'] ?? '') != 'seller') {
				$msg = $Translate->get_item('error user has no rights');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			if (!$nfc_id) {
				$msg = $Translate->get_item('error not valid nfc card');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			if (abs($price_total_count - $price_total) > 0.001) {
				$msg = $Translate->get_item('error not valid total price');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			if (!$request_id) {
				$msg = $Translate->get_item('error request id dublicate');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			} else {
				if ($Festivals->check_checkout_by_request_id($request_id)) {
					$msg = $Translate->get_item('error request id dublicate');
					if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
					$_SESSION['main_messages'][] = $msg;
					Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
					die();
				}
				// Server-side extra protection: detect near-duplicate by nfc_id+total within short window
				if ($nfc_id && $Festivals->check_recent_total_by_nfc($nfc_id, $_SESSION['user']['id'], $price_total_count, 5)) {
					$msg = $Translate->get_item('error duplicate recent');
					if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'duplicate'=>true,'message'=>$msg,'request_id'=>$request_id]); exit; }
					$_SESSION['main_messages'][] = $msg;
					Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
					die();
				}
			}

		// If cart is empty, return explicit error
		if (!$price_total_count) {
			// If there are no items, treat AJAX requests as a balance-check / no-op success
			usleep(100000);
			$wallet = $Festivals->get_wallet_item($nfc_id);
			if ($isAjax) {
				header('Content-Type: application/json');
				echo json_encode([
					'success' => true,
					'message' => '',
					'items' => $price_items,
					'checkout' => 0,
					'wallet' => (float)$wallet,
					'request_id' => $request_id,
				]);
				exit;
			}
			// Non-AJAX fallback: redirect to checkoutOk (shows wallet and checkout=0)
			$_SESSION['main_messages'][] = $Translate->get_item('error empty cart');
			Location('?module=festivals&action=checkoutOk&checkout=0&wallet=' . $wallet);
			die();
		}

		// Then check wallet sufficiency
		$wallet = $Festivals->get_wallet_item($nfc_id);
		if ($wallet < $price_total_count) {
			usleep(300000);
			if ($isAjax) {
				header('Content-Type: application/json');
				echo json_encode([
					'success' => false,
					'message' => $Translate->get_item('error not enough money'),
					'wallet' => (float)$wallet,
					'checkout' => (float)$price_total_count,
					'shortfall' => max(0, round($price_total_count - (float)$wallet, 2)),
					'items' => $price_items,
					'request_id' => $request_id,
				]);
				exit;
			}
			Location('?module=festivals&action=checkoutError&wallet=' . $wallet);
			die();
		}

			$insert_ids = [];
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
				$res = $Festivals->add_checkout_item($form5);
				if (!is_array($res) || !$res['success']) {
					if (is_array($res) && !empty($res['duplicate'])) {
						// duplicate request id - fetch existing checkout row and return it so client gets the same canonical result
						$existing = $Festivals->get_checkout_by_request_id($_SESSION['app']['festival'], $request_id);
						$walletNow = $Festivals->get_wallet_item($nfc_id);
						if ($isAjax) {
							header('Content-Type: application/json');
							echo json_encode([
								'success' => true,
								'duplicate' => true,
								'message' => 'OK',
								'checkout' => isset($existing['price']) ? (float)$existing['price'] : $price_total_count,
								'items' => $price_items,
								'wallet' => (float)$walletNow,
								'request_id' => $request_id,
								'insert_ids' => $insert_ids,
								'transaction_id' => isset($existing['id']) ? $existing['id'] : (!empty($insert_ids) ? $insert_ids[0] : null),
								'server_time' => time(),
								'operator' => isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
							]);
							exit;
						}
						// Non-AJAX: set message and redirect back
						$_SESSION['main_messages'][] = $Translate->get_item('error request id dublicate');
						Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
						die();
					} else {
						// generic db error
						if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'db error','request_id'=>$request_id]); exit; }
						$_SESSION['main_messages'][] = $Translate->get_item('error saving data');
						Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
						die();
					}
				} else {
					if (isset($res['insert_id'])) $insert_ids[] = $res['insert_id'];
				}
			}

			$wallet = $Festivals->get_wallet_item($nfc_id);

			usleep(300000);
			if ($isAjax) {
				header('Content-Type: application/json');
				echo json_encode([
					'success'=>true,
					'message'=>'OK',
					'checkout'=>$price_total_count,
					'wallet'=>$wallet,
					'items'=>$price_items,
					'request_id'=>$request_id,
					'insert_ids' => $insert_ids,
					'transaction_id' => (!empty($insert_ids) ? $insert_ids[0] : null),
					'server_time' => time(),
					'operator' => isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
				]);
				exit;
			}

			Location('?module=festivals&action=checkoutOk&checkout=' . $price_total_count . '&wallet=' . $wallet);
			die();
			break;
			
		case 'selectAct':
			$data = $Festivals->get_festivals_item_by_company_user($url['id'] ?? null, $_SESSION['user']['id']);
			if ($data) {
				$_SESSION['app']['festival'] = $data['festival_id'];
				Location('/app.php?module=festivals&action=work');
				die();
			}
			Location('/app.php');
			die();
			break;
		
		case 'checkoutOk':
			if (isset($smarty) && is_object($smarty)) {
				$smarty->assign('checkout', (float)($url['checkout'] ?? 0));
				$smarty->assign('wallet', (float)($url['wallet'] ?? 0));
			}

			$template_name = $module_name . '.checkout.ok';
			break;
		
		case 'checkoutError':
			if (isset($smarty) && is_object($smarty)) {
				$smarty->assign('wallet', (float)($url['wallet'] ?? 0));
			}

			$template_name = $module_name . '.checkout.error';
			break;
		
		case 'checkoutList':
			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user) {
				Location('/app.php');
				die();
			}
			
			$incomes_user = $Festivals->get_checkout_sum_by_user($_SESSION['user']['id'], $_SESSION['app']['festival']);
			if (isset($smarty) && is_object($smarty)) {
				$smarty->assign('incomes_user', $incomes_user);
			}
			
			$incomes_company = $Festivals->get_checkout_sum_by_company($data_user['company_id'], $_SESSION['app']['festival']);
			if (isset($smarty) && is_object($smarty)) {
				$smarty->assign('incomes_company', $incomes_company);
			}
			
			$list_festival = $Festivals->get_checkout_list(
				0,
				30,
				[
					'festival' => $_SESSION['app']['festival'],
					'user' => $_SESSION['user']['id'],
				]
			);
			if (isset($smarty) && is_object($smarty)) {
				$smarty->assign('list', $list_festival);
			}

			$template_name = $module_name . '.checkout.list';
			break;
		
		case 'cashmachineAct':
			$nfc_id = $_POST['nfc_id'] ?? $url['nfc_id'] ?? null;
			$topup_amount = isset($_POST['topup_amount']) ? (float)$_POST['topup_amount'] : (float)($url['topup_amount'] ?? 0);
			$request_id = $_POST['request_id'] ?? $url['request_id'] ?? '';

			$isAjax = is_ajax_request();

			if (!is_development_version() && $nfc_id !== null && substr((string)$nfc_id, 0, strlen($Festivals->test_nfc_card)) == $Festivals->test_nfc_card && !$Festivals->testing) {
				$_SESSION['main_messages'][] = 'TEST DATA';
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'TEST DATA']); exit; }
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user || ($data_user['user_app_type'] ?? '') != 'cashmachine') {
				$msg = $Translate->get_item('error user has no rights');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			if (!$nfc_id) {
				$msg = $Translate->get_item('error not valid nfc card');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}

			if (!$request_id) {
				$msg = $Translate->get_item('error request id dublicate');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			} else {
				if ($Festivals->check_checkout_by_request_id($request_id)) {
					$msg = $Translate->get_item('error request id dublicate');
					if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
					$_SESSION['main_messages'][] = $msg;
					Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
					die();
				}
			}

			if (!(float)$topup_amount) {
				// If topup amount is zero, for AJAX treat as balance-check: return wallet so client can show balance
				$wallet = $Festivals->get_wallet_item($nfc_id);
				if ($isAjax) {
					header('Content-Type: application/json');
					echo json_encode([
						'success' => true,
						'message' => '',
						'topup' => 0,
						'wallet' => (float)$wallet,
						'request_id' => $request_id,
					]);
					exit;
				}
				// Non-AJAX fallback: keep previous behavior (show message and redirect)
				$msg = $Translate->get_item('error empty cart');
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
				die();
			}
			if ((float)$topup_amount > $Festivals->max_topup_ammount) {
				$msg = $Translate->get_item('error not enough money');
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$msg]); exit; }
				$_SESSION['main_messages'][] = $msg;
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
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
			$res = $Festivals->add_checkout_item($form5);
			if (!is_array($res) || !$res['success']) {
				if (is_array($res) && !empty($res['duplicate'])) {
					// duplicate topup - fetch existing checkout row and return its info so client sees canonical state
					$existing = $Festivals->get_checkout_by_request_id($_SESSION['app']['festival'], $request_id);
					$walletNow = $Festivals->get_wallet_item($nfc_id);
					if ($isAjax) {
						header('Content-Type: application/json');
						echo json_encode([
							'success' => true,
							'duplicate' => true,
							'message' => 'OK',
							'topup' => isset($existing['price']) ? (float)$existing['price'] : $topup_amount,
							'wallet' => (float)$walletNow,
							'request_id' => $request_id,
							'transaction_id' => isset($existing['id']) ? $existing['id'] : null,
							'server_time' => time(),
							'operator' => isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
						]);
						exit;
					}
					// Non-AJAX: set message and redirect back
					$_SESSION['main_messages'][] = $Translate->get_item('error request id dublicate');
					Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
					die();
				} else {
					if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>'db error','request_id'=>$request_id]); exit; }
					$_SESSION['main_messages'][] = $Translate->get_item('error saving data');
					Location($_SERVER['HTTP_REFERER'] ?? '/app.php');
					die();
				}
			} else {
				$cash_insert_id = $res['insert_id'] ?? null;
			}

			$wallet = $Festivals->get_wallet_item($nfc_id);

			if ($isAjax) {
				header('Content-Type: application/json');
				echo json_encode([
					'success'=>true,
					'message'=>'OK',
					'topup'=>$topup_amount,
					'wallet'=>$wallet,
					'request_id'=>$request_id,
					'transaction_id' => $cash_insert_id ?? null,
					'server_time' => time(),
					'operator' => isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null,
				]);
				exit;
			}

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
		case 'walletInfo':
			// Return current wallet sum for a given NFC id (AJAX only)
			$nfc_id = $_POST['nfc_id'] ?? $url['nfc_id'] ?? null;
			$isAjax = is_ajax_request();
			if (!$nfc_id) {
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$Translate->get_item('error not valid nfc card')]); exit; }
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php'); die();
			}

			// Permission: must be logged in and seller or cashmachine
			$data_user = $Festivals->get_users_item($_SESSION['user']['id']);
			if (!$data_user) {
				if ($isAjax) { header('Content-Type: application/json'); echo json_encode(['success'=>false,'message'=>$Translate->get_item('error user has no rights')]); exit; }
				Location($_SERVER['HTTP_REFERER'] ?? '/app.php'); die();
			}

			$wallet = $Festivals->get_wallet_item($nfc_id);
			if ($isAjax) {
				header('Content-Type: application/json');
				echo json_encode(['success'=>true,'wallet'=>$wallet]);
				exit;
			}
			// Non-AJAX fallback
			Location('?module=festivals&action=work');
			die();
			break;
	}















