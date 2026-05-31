<?php
	require_once(LIBS_MAIN_PATH . 'Festivals.class.php');
	$Festivals = new Festivals();
	require_once(LIBS_MAIN_PATH . 'Users.class.php');
	$Users = new Users;
	
	$module_name = 'festivals';
	/** @var object $smarty */
	$smarty->assign('module_name', $module_name);
	check_permissions($module_name);
	
	/** @var object $Translate */
	$title = [$Translate->get_item('festivals')];
	$submenu = [
		$Translate->get_item('list') => 'browse',
	];
	//$adodb->debug = true;
	
	
	/** @var array $url */
	switch ($url['_action_']) {
	
		case 'info':
			$page = $module_name . ".info";
	
			if ($url['id']) {
				$data = $Festivals->get_festivals_item($url['id']);
				$smarty->assign('data', $data);
			}
			$smarty->assign('language_active', $Translate->language);
			
			if ($data) {
				$title[] = ['title' => $data['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data['id']];
			}
			break;
	
	
		case 'infoAct':
			check_permissions($module_name, 'write');
			$form = $url['form'];
			$form2 = $url['form2'];
			$form['is_active'] = ($form2['is_active']) ? 1 : null;
			$form['time_starts'] = strtotime($form2['time_starts']);
			$form['time_ends'] = strtotime($form2['time_ends']);
			$user_id = 0;
			$oki = true;
			
			$data_user = $Users->get_item_email(trim($form2['email']));
			if (!trim($form2['email']) || !$data_user) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$form['is_active'] = null;
			} else {
				$form['user_id'] = $data_user['id'];
			}
			
			if (
				$form['time_starts'] === false || $form['time_starts'] < 0
				|| $form['time_ends'] === false || $form['time_ends'] < 0
			) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$oki = false;
			}

			if (!trim($form['title'])) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$oki = false;
			}
			
			if ($oki) {
				if ((int)$form2['id']) {
					$festival_id = $form2['id'];
					$Festivals->update_festivals_item($festival_id, $form);
					
				} else {
					$festival_id = $Festivals->add_festivals_item($form);
				}
				
			}
	
			if (isset($festival_id)) {
				Location('?module=' . $module_name . '&action=view&id=' . $festival_id);
				die();
			}
			Location('?module=' . $module_name . '&action=info');
			die();
		
		
		case 'view':
			$page = $module_name . ".view";
			
			$data = $Festivals->get_festivals_item($url['id']);
			$smarty->assign('data', $data);
			
			$list_companies = $Festivals->get_companies_list(0, 1000, ['festival' => $data['id']]);
			$smarty->assign('list_companies', $list_companies);

			$default_sales_from = ((int)($data['time_starts'] ?? 0)) ? date('Y-m-d', (int)$data['time_starts']) : date('Y-m-d');
			$default_sales_till = ((int)($data['time_ends'] ?? 0)) ? date('Y-m-d', (int)$data['time_ends']) : $default_sales_from;
			$sales_from = trim((string)($url['sales_from'] ?? $default_sales_from));
			$sales_till = trim((string)($url['sales_till'] ?? $default_sales_till));
			$sales_time_from = strtotime($sales_from . ' 00:00:00');
			$sales_time_till = strtotime($sales_till . ' 23:59:59');
			if (!$sales_time_from) {
				$sales_from = date('Y-m-d');
				$sales_time_from = strtotime($sales_from . ' 00:00:00');
			}
			if (!$sales_time_till) {
				$sales_till = $sales_from;
				$sales_time_till = strtotime($sales_till . ' 23:59:59');
			}
			if ($sales_time_from > $sales_time_till) {
				$tmp_time = $sales_time_from;
				$sales_time_from = $sales_time_till;
				$sales_time_till = $tmp_time;
				$tmp_date = $sales_from;
				$sales_from = $sales_till;
				$sales_till = $tmp_date;
			}
			$list_companies_sales = $Festivals->get_companies_sales_ranking($data['id'], $sales_time_from, $sales_time_till);
			$list_products_sales = $Festivals->get_products_sales_ranking($data['id'], $sales_time_from, $sales_time_till, 30);
			$nfc_stats = $Festivals->get_nfc_stats($data['id']);
			$smarty->assign('sales_from', $sales_from);
			$smarty->assign('sales_till', $sales_till);
			$smarty->assign('list_companies_sales', $list_companies_sales);
			$smarty->assign('list_products_sales', $list_products_sales);
			$smarty->assign('nfc_stats', $nfc_stats);
			
			$smarty->assign('language_active', $Translate->language);
			
			$title[] = ['title' => $data['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data['id']];
			break;

		case 'nfcList':
			$page = $module_name . ".nfc.list";

			$data = $Festivals->get_festivals_item($url['id']);
			if (!$data) {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			$search = urldecode((string)$url['search']);
			$pg = ((int)$url['pg']) ? $url['pg'] : 0;
			$pg_items = 50;
			$list = $Festivals->get_nfc_list($data['id'], $pg, $pg_items, ['search' => $search]);
			$pg_records = $Festivals->get_nfc_list_cnt($data['id'], ['search' => $search]);

			$smarty->assign('data', $data);
			$smarty->assign('search', $search);
			$smarty->assign('list', $list);
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "?module=" . $module_name . "&action=nfcList&id=" . (int)$data['id'] . "&search=" . urlencode($search) . "&pg="));

			$title[] = ['title' => $data['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data['id']];
			$title[] = $Translate->get_item('nfc list');
			break;

		case 'nfcView':
			$page = $module_name . ".nfc.view";

			$data = $Festivals->get_festivals_item($url['id']);
			$nfc_id = trim((string)$url['nfc_id']);
			if (!$data || !$nfc_id) {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			$list = $Festivals->get_nfc_log($data['id'], $nfc_id);

			$smarty->assign('data', $data);
			$smarty->assign('nfc_id', $nfc_id);
			$smarty->assign('list', $list);

			$title[] = ['title' => $data['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data['id']];
			$title[] = ['title' => $Translate->get_item('nfc list'), 'link' => '?module=' . $module_name . '&action=nfcList&id=' . $data['id']];
			$title[] = $nfc_id;
			break;

		case 'companiesSalesExport':
			$data = $Festivals->get_festivals_item($url['id']);
			if (!$data) {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}

			$default_sales_from = ((int)($data['time_starts'] ?? 0)) ? date('Y-m-d', (int)$data['time_starts']) : date('Y-m-d');
			$default_sales_till = ((int)($data['time_ends'] ?? 0)) ? date('Y-m-d', (int)$data['time_ends']) : $default_sales_from;
			$sales_from = trim((string)($url['sales_from'] ?? $default_sales_from));
			$sales_till = trim((string)($url['sales_till'] ?? $default_sales_till));
			$sales_time_from = strtotime($sales_from . ' 00:00:00');
			$sales_time_till = strtotime($sales_till . ' 23:59:59');
			if (!$sales_time_from) {
				$sales_from = $default_sales_from;
				$sales_time_from = strtotime($sales_from . ' 00:00:00');
			}
			if (!$sales_time_till) {
				$sales_till = $default_sales_till;
				$sales_time_till = strtotime($sales_till . ' 23:59:59');
			}
			if ($sales_time_from > $sales_time_till) {
				$tmp_time = $sales_time_from;
				$sales_time_from = $sales_time_till;
				$sales_time_till = $tmp_time;
				$tmp_date = $sales_from;
				$sales_from = $sales_till;
				$sales_till = $tmp_date;
			}

			$list_companies_sales = $Festivals->get_companies_sales_ranking($data['id'], $sales_time_from, $sales_time_till);
			$filename = 'sales-by-sellers-' . (int)$data['id'] . '-' . $sales_from . '-' . $sales_till . '.csv';

			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename="' . $filename . '"');
			header('Pragma: no-cache');
			header('Expires: 0');

			echo "\xEF\xBB\xBF";
			$output = fopen('php://output', 'w');
			fputcsv($output, ['Seller', 'Incomes', '25%', '10%'], ';');
			foreach ($list_companies_sales as $item) {
				$incomes_total = (float)$item['incomes_total'];
				fputcsv($output, [
					$item['title'],
					number_format($incomes_total, 2, '.', ''),
					number_format($incomes_total * 0.25, 2, '.', ''),
					number_format($incomes_total * 0.10, 2, '.', ''),
				], ';');
			}
			fclose($output);
			die();
		

		case 'companiesInfo':
			$page = $module_name . ".companies.info";

			if ($url['id']) {
				$data = $Festivals->get_companies_item($url['id']);
				$smarty->assign('data', $data);
				$festival_id = $data['festival_id'];
			} elseif ($url['festival_id']) {
				$festival_id = $url['festival_id'];
			}
			
			$data_festival = $Festivals->get_festivals_item($festival_id);
			$smarty->assign('data_festival', $data_festival);
			
			$smarty->assign('language_active', $Translate->language);
			$title[] = ['title' => $data_festival['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data_festival['id']];
			$title[] = $Translate->get_item('companies');
			if (isset($data)) {
				$title[] = ['title' => $data['title'], 'link' => '?module=' . $module_name . '&action=companiesView&id=' . $data['id']];
			}
			break;
		
		
		case 'companiesInfoAct':
			check_permissions($module_name, 'write');
			$form = $url['form'];
			$form2 = $url['form2'];
			$form['is_active'] = ($form2['is_active']) ? 1 : null;
			$oki = true;
			
			if (!trim($form['title'])) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$oki = false;
			}
			
			if (!$form2['id'] && !(int)$form2['festival_id']) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$oki = false;
			}
			
			if ($oki) {
				if ((int)$form2['id']) {
					$companies_id = $form2['id'];
					$Festivals->update_companies_item($companies_id, $form);
					
				} else {
					$form['festival_id'] = $form2['festival_id'];
					$companies_id = $Festivals->add_companies_item($form);
				}
				
			}
			
			if (isset($companies_id)) {
				Location('?module=' . $module_name . '&action=companiesView&id=' . $companies_id);
				die();
			}
			Location('?module=' . $module_name . '&action=companiesInfo');
			die();
		
		
		case 'companiesView':
			$page = $module_name . ".companies.view";
			$data_companies = $Festivals->get_companies_item($url['id']);
			$smarty->assign('data_companies', $data_companies);
			
			$data_festivals = $Festivals->get_festivals_item($data_companies['festival_id']);
			$smarty->assign('data_festivals', $data_festivals);
			
			$list_users = $Festivals->get_users_list_by_company($data_companies['id']);
			$smarty->assign('list_users', $list_users);
			
			$list_prices = $Festivals->get_prices_list_by_company($data_companies['id']);
			$smarty->assign('list_prices', $list_prices);

			$default_sales_from = ((int)($data_festivals['time_starts'] ?? 0)) ? date('Y-m-d', (int)$data_festivals['time_starts']) : date('Y-m-d');
			$default_sales_till = ((int)($data_festivals['time_ends'] ?? 0)) ? date('Y-m-d', (int)$data_festivals['time_ends']) : $default_sales_from;
			$sales_from = trim((string)($url['sales_from'] ?? $default_sales_from));
			$sales_till = trim((string)($url['sales_till'] ?? $default_sales_till));
			$sales_time_from = strtotime($sales_from . ' 00:00:00');
			$sales_time_till = strtotime($sales_till . ' 23:59:59');
			if (!$sales_time_from) {
				$sales_from = $default_sales_from;
				$sales_time_from = strtotime($sales_from . ' 00:00:00');
			}
			if (!$sales_time_till) {
				$sales_till = $default_sales_till;
				$sales_time_till = strtotime($sales_till . ' 23:59:59');
			}
			if ($sales_time_from > $sales_time_till) {
				$tmp_time = $sales_time_from;
				$sales_time_from = $sales_time_till;
				$sales_time_till = $tmp_time;
				$tmp_date = $sales_from;
				$sales_from = $sales_till;
				$sales_till = $tmp_date;
			}
			$list_company_products_sales = $Festivals->get_company_products_sales($data_companies['id'], $data_festivals['id'], $sales_time_from, $sales_time_till, $Translate->get_item('custom price'));
			$list_company_users_sales = $Festivals->get_company_users_sales($data_companies['id'], $data_festivals['id'], $sales_time_from, $sales_time_till);
			if (count($list_company_users_sales) <= 1) {
				$list_company_users_sales = [];
			}
			$smarty->assign('sales_from', $sales_from);
			$smarty->assign('sales_till', $sales_till);
			$smarty->assign('list_company_products_sales', $list_company_products_sales);
			$smarty->assign('list_company_users_sales', $list_company_users_sales);
			
			$smarty->assign('language_active', $Translate->language);
			
			$title[] = ['title' => $data_festivals['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data_festivals['id']];
			$title[] = $Translate->get_item('companies');
			$title[] = ['title' => $data_companies['title'], 'link' => '?module=' . $module_name . '&action=companiesView&id=' . $data_companies['id']];
			break;

		case 'companiesSalesEmail':
			$data_companies = $Festivals->get_companies_item($url['id']);
			if (!$data_companies) {
				Location($_SERVER['HTTP_REFERER']);
				die();
			}
			$data_festivals = $Festivals->get_festivals_item($data_companies['festival_id']);
			$default_sales_from = ((int)($data_festivals['time_starts'] ?? 0)) ? date('Y-m-d', (int)$data_festivals['time_starts']) : date('Y-m-d');
			$default_sales_till = ((int)($data_festivals['time_ends'] ?? 0)) ? date('Y-m-d', (int)$data_festivals['time_ends']) : $default_sales_from;
			$sales_from = trim((string)($url['sales_from'] ?? $default_sales_from));
			$sales_till = trim((string)($url['sales_till'] ?? $default_sales_till));
			$sales_time_from = strtotime($sales_from . ' 00:00:00');
			$sales_time_till = strtotime($sales_till . ' 23:59:59');
			if (!$sales_time_from) {
				$sales_from = $default_sales_from;
				$sales_time_from = strtotime($sales_from . ' 00:00:00');
			}
			if (!$sales_time_till) {
				$sales_till = $default_sales_till;
				$sales_time_till = strtotime($sales_till . ' 23:59:59');
			}
			if ($sales_time_from > $sales_time_till) {
				$tmp_time = $sales_time_from;
				$sales_time_from = $sales_time_till;
				$sales_time_till = $tmp_time;
				$tmp_date = $sales_from;
				$sales_from = $sales_till;
				$sales_till = $tmp_date;
			}

			$list_users = $Festivals->get_users_list_by_company($data_companies['id']);
			$email_to = [];
			$email_to_used = [];
			foreach ($list_users as $item_user) {
				if (!$item_user['is_active']) {
					continue;
				}
				$user_email = trim((string)$item_user['user_email']);
				if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
					continue;
				}
				$user_email_key = strtolower($user_email);
				if (isset($email_to_used[$user_email_key])) {
					continue;
				}
				$email_to[] = [
					'email' => $user_email,
					'name' => $item_user['user_name'],
				];
				$email_to_used[$user_email_key] = true;
			}
			if (!$email_to) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				Location('?module=' . $module_name . '&action=companiesView&id=' . $data_companies['id'] . '&sales_from=' . urlencode($sales_from) . '&sales_till=' . urlencode($sales_till));
				die();
			}
			$list_company_products_sales = $Festivals->get_company_products_sales($data_companies['id'], $data_festivals['id'], $sales_time_from, $sales_time_till, $Translate->get_item('custom price'));
			$list_company_daily_sales = $Festivals->get_company_daily_sales($data_companies['id'], $data_festivals['id'], $sales_time_from, $sales_time_till);
			$list_company_users_sales = $Festivals->get_company_users_sales($data_companies['id'], $data_festivals['id'], $sales_time_from, $sales_time_till);
			if (count($list_company_users_sales) <= 1) {
				$list_company_users_sales = [];
			}
			$total_sales = 0;
			foreach ($list_company_daily_sales as $item_daily_sales) {
				$total_sales += (float)$item_daily_sales['incomes_total'];
			}

			$smarty->assign('data_companies', $data_companies);
			$smarty->assign('data_festivals', $data_festivals);
			$smarty->assign('sales_from', $sales_from);
			$smarty->assign('sales_till', $sales_till);
			$smarty->assign('total_sales', $total_sales);
			$smarty->assign('list_users', $list_users);
			$smarty->assign('list_company_products_sales', $list_company_products_sales);
			$smarty->assign('list_company_daily_sales', $list_company_daily_sales);
			$smarty->assign('list_company_users_sales', $list_company_users_sales);

			$body_html = my_fetch('festivals.companies.view.mail.tpl');
			$email_params = [
				'subject' => $Translate->get_item('sales report') . ' - ' . $data_companies['title'],
				'to' => $email_to,
				'bcc' => [$page_special_config['email']['from_email']],
				'body_html' => $body_html,
			];
			mail_customize_smtp($email_params);
			$_SESSION['main_messages'][] = $Translate->get_item('success email sent');

			Location('?module=' . $module_name . '&action=companiesView&id=' . $data_companies['id'] . '&sales_from=' . urlencode($sales_from) . '&sales_till=' . urlencode($sales_till));
			die();
		
		
		case 'usersInfo':
			$page = $module_name . ".users.info";
			
			if ($url['id']) {
				$data = $Festivals->get_users_item($url['id'], $url['festival_id']);
				$smarty->assign('data', $data);
				if (!$data) {
					Location($_SERVER['HTTP_REFERER']);
					die();
				}
				$company_id = $data['company_id'];
			} elseif ($url['company_id']) {
				$company_id = $url['company_id'];
			}
			
			$data_companies = $Festivals->get_companies_item($company_id);
			$smarty->assign('data_companies', $data_companies);
			
			$data_festival = $Festivals->get_festivals_item($data_companies['festival_id']);
			$smarty->assign('data_festival', $data_festival);
			
			$title[] = ['title' => $data_festival['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data_festival['id']];
			$title[] = $Translate->get_item('companies');
			$title[] = ['title' => $data_companies['title'], 'link' => '?module=' . $module_name . '&action=companiesView&id=' . $data_companies['id']];
			$title[] = $Translate->get_item('users');
			if (isset($data)) {
				$title[] = $data['user_name'];
			}
			break;
		
		
		case 'usersInfoAct':
			check_permissions($module_name, 'write');
			$form = $url['form'];
			$form2 = $url['form2'];
			$form['is_active'] = ($form2['is_active']) ? 1 : null;
			$oki = true;
			
			if (!$form2['id']) {
				$data_user = $Users->get_item_email($form2['email']);
				if (!$data_user || !$data_user['is_active']) {
					$_SESSION['main_messages'][] = $Translate->get_item('error user not found');
					$oki = false;
				} else {
					$form['user_id'] = $data_user['id'];
				}
			}
			
			$data_companies = false;
			if ($form2['company_id']) {
				$data_companies = $Festivals->get_companies_item($form2['company_id']);
			}
			
			if (!$form2['id'] && !$data_companies) {
				$_SESSION['main_messages'][] = $Translate->get_item('error system');
				$oki = false;
			}
			
			if ($oki && !$form2['id']) {
				$check_user = $Festivals->get_users_item($data_user['id'], $data_companies['festival_id']);
				if ($check_user) {
					$_SESSION['main_messages'][] = $Translate->get_item('error user used');
					$oki = false;
				}
			}
			
			if ($oki) {
				if ((int)$form2['id']) {
					$data_companies = $Festivals->get_companies_item($form2['company_id']);
					$company_id = $data_companies['id'];
					$data_user = $Festivals->get_users_item($form2['id'], $data_companies['festival_id']);
					if ($data_user && $data_companies) {
						$Festivals->update_users_item($data_user['user_id'], $data_user['company_id'], ['is_active' => $form['is_active']]);
					}
				} else {
					if (!$data_companies) {
					
					}
					$form['company_id'] = $form2['company_id'];
					$company_id = $form2['company_id'];
					$user_id = $Festivals->add_users_item($form);
				}
				
			}
			
			Location('?module=' . $module_name . '&action=companiesView&id=' . $company_id);
			die();
		
		
		case 'pricesInfo':
			$page = $module_name . ".prices.info";
			
			if ($url['id']) {
				$data = $Festivals->get_prices_item($url['id']);
				$smarty->assign('data', $data);
				$company_id = $data['company_id'];
			} elseif ($url['company_id']) {
				$company_id = $url['company_id'];
			}
			
			$data_companies = $Festivals->get_companies_item($company_id);
			$smarty->assign('data_companies', $data_companies);
			
			$data_festivals = $Festivals->get_festivals_item($data_companies['festival_id']);
			$smarty->assign('data_festivals', $data_festivals);
			
			$title[] = ['title' => $data_festivals['title'], 'link' => '?module=' . $module_name . '&action=view&id=' . $data_festivals['id']];
			$title[] = $Translate->get_item('companies');
			$title[] = ['title' => $data_companies['title'], 'link' => '?module=' . $module_name . '&action=companiesView&id=' . $data_companies['id']];
			$title[] = $Translate->get_item('prices');
			if (isset($data)) {
				$title[] = $data['title'];
			}
			break;
		
		
		case 'pricesInfoAct':
			check_permissions($module_name, 'write');
			$form = $url['form'];
			$form2 = $url['form2'];
			$form['is_active'] = ($form2['is_active']) ? 1 : null;
			$form['price'] = update_decimal_numbers($form['price']);
			$oki = true;
			
			if (!(float)$form['price'] || $form['price'] <= 0) {
				$_SESSION['main_messages'][] = $Translate->get_item('error empty required values');
				$oki = false;
			}
			
			if ($oki) {
				if ((int)$form2['id']) {
					$data = $Festivals->get_prices_item($form2['id']);
					$companies_id = $data['company_id'];
					$Festivals->update_prices_item($form2['id'], $form);
					
				} else {
					$companies_id = $form['company_id'];
					$prices_id = $Festivals->add_prices_item($form);
				}
				
			}
			
			if (isset($companies_id)) {
				Location('?module=' . $module_name . '&action=companiesView&id=' . $companies_id);
				die();
			}
			Location('?module=' . $module_name);
			die();
		
		
		default:
			$page = $module_name;
			$search = urldecode($url['search']);
			$pg = ((int)$url['pg']) ? $url['pg'] : 0;
			$pg_items = 30;
	
			$smarty->assign('search', $search);
	
			$smarty->assign('list', $Festivals->get_festivals_list($pg, $pg_items, ['search' => $search]));
	
			$pg_records = $Festivals->get_festivals_list_cnt(['search' => $url['search']]);
			/** @var object $Paging */
			$smarty->assign('paging', $Paging->show($pg, $pg_records, $pg_items, "?module=" . $module_name . "&action=browse&search=" . urlencode($search) . "&pg="));
			
			break;
	}
