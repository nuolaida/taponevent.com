<?php
error_reporting(E_ERROR | E_PARSE);
require __DIR__ . '/../vendor/autoload.php';
date_default_timezone_set('Europe/Vilnius');

require_once __DIR__ . '/../config.globals.php';

function is_development_version() {
	$host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? '';
	$host = preg_replace('/:\d+$/', '', (string)$host);
	$host = strtolower(trim($host));
	if ($host === 'localhost' || $host === '127.0.0.1' || $host === '::1') {
		return true;
	}
	if (substr($host, -10) === '.localhost') {
		return true;
	}
	return false;
}

function seller_bootstrap(): void
{
	static $bootstrapped = false;
	if ($bootstrapped) {
		return;
	}

	global $page_special_config, $domain_config, $adodb;

	if (session_status() !== PHP_SESSION_ACTIVE) {
		ini_set("session.gc_maxlifetime", 28800);
		ini_set("session.cookie_lifetime", 10800);
		ini_set("session.cookie_domain", "");
		session_cache_limiter("must-revalidate");
		session_set_cookie_params(time() + 3600);
		session_start();
	}

	include_once CONFIG_FILES_PATH . 'config.bn2.php';
	require_once LIBS_MAIN_PATH . 'Translate.class.php';
	$Translate = new Translate;
	include_once CONFIG_FILES_PATH . 'config.php';
	$bootstrapped = true;
}

function seller_json(array $data, int $status = 200): void
{
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

function seller_date_start(string $date): int
{
	$ts = strtotime($date . ' 00:00:00');
	return $ts ?: strtotime(date('Y-m-d') . ' 00:00:00');
}

function seller_date_end(string $date): int
{
	$ts = strtotime($date . ' 23:59:59');
	return $ts ?: strtotime(date('Y-m-d') . ' 23:59:59');
}

if (($_GET['action'] ?? '') === 'companies') {
	seller_bootstrap();
	global $adodb;

	$festival_id = (int)($_GET['festival_id'] ?? $_POST['festival_id'] ?? 0);
	$festival_where = $festival_id ? ' AND FC.festival_id = ' . $festival_id : '';
	$sql = "
		SELECT
			FC.id,
			FC.title,
			FC.festival_id,
			COALESCE(LVT_LT.text, LVT_EN.text, CONCAT('Festivalis #', FC.festival_id)) AS festival_title
		FROM festivals_users_companies FC
		LEFT JOIN languages_varchar LVT_LT ON LVT_LT.table_name = 'festivals'
			AND LVT_LT.table_id = FC.festival_id
			AND LVT_LT.keyword = 'title'
			AND LVT_LT.language = 'lt'
		LEFT JOIN languages_varchar LVT_EN ON LVT_EN.table_name = 'festivals'
			AND LVT_EN.table_id = FC.festival_id
			AND LVT_EN.keyword = 'title'
			AND LVT_EN.language = 'en'
		WHERE (FC.app_type = 'seller'
			OR EXISTS (
				SELECT 1
				FROM festivals_users_companies_prices PRI
				WHERE PRI.company_id = FC.id
			))
			{$festival_where}
		ORDER BY festival_title ASC, FC.title ASC
	";
	$rows = $adodb->getAll($sql);
	if (!is_array($rows)) {
		$rows = [];
	}

	seller_json([
		'success' => true,
		'items' => array_map(function ($row) {
			return [
				'id' => (int)$row['id'],
				'title' => $row['title'],
				'festival_id' => (int)$row['festival_id'],
				'festival_title' => $row['festival_title'],
			];
		}, $rows),
	]);
}

if (($_GET['action'] ?? '') === 'festivals') {
	seller_bootstrap();
	global $adodb;

	$sql = "
		SELECT F.id, COALESCE(LVT_LT.text, LVT_EN.text, CONCAT('Festivalis #', F.id)) AS title
		FROM festivals F
		LEFT JOIN languages_varchar LVT_LT ON LVT_LT.table_name = 'festivals'
			AND LVT_LT.table_id = F.id
			AND LVT_LT.keyword = 'title'
			AND LVT_LT.language = 'lt'
		LEFT JOIN languages_varchar LVT_EN ON LVT_EN.table_name = 'festivals'
			AND LVT_EN.table_id = F.id
			AND LVT_EN.keyword = 'title'
			AND LVT_EN.language = 'en'
		WHERE EXISTS (
			SELECT 1
			FROM festivals_users_companies FC
			WHERE FC.festival_id = F.id
		)
		ORDER BY F.time_starts DESC, F.id DESC
	";
	$rows = $adodb->getAll($sql);
	if (!is_array($rows)) {
		$rows = [];
	}

	seller_json([
		'success' => true,
		'items' => array_map(function ($row) {
			return [
				'id' => (int)$row['id'],
				'title' => $row['title'],
			];
		}, $rows),
	]);
}

if (($_GET['action'] ?? '') === 'report') {
	seller_bootstrap();
	global $adodb;

	$company_id = (int)($_POST['company_id'] ?? $_GET['company_id'] ?? 0);
	$festival_id = (int)($_POST['festival_id'] ?? $_GET['festival_id'] ?? 0);
	$date_from = trim((string)($_POST['date_from'] ?? $_GET['date_from'] ?? date('Y-m-d')));
	$date_to = trim((string)($_POST['date_to'] ?? $_GET['date_to'] ?? date('Y-m-d')));
	$time_from = seller_date_start($date_from);
	$time_to = seller_date_end($date_to);

	if (!$festival_id) {
		seller_json(['success' => false, 'message' => 'Pasirinkite festivalį.'], 400);
	}
	if ($time_from > $time_to) {
		seller_json(['success' => false, 'message' => 'Data „nuo“ negali būti vėlesnė už datą „iki“.'], 400);
	}

	if (!$company_id) {
		$sql_sellers = "
			SELECT
				FC.id,
				FC.title,
				COUNT(CKT.id) AS quantity,
				COUNT(DISTINCT IF(CKT.request_id IS NULL OR CKT.request_id = '', CONCAT('row-', CKT.id), CKT.request_id)) AS receipts,
				SUM(0 - CKT.price) AS total
			FROM festivals_checkout CKT
			INNER JOIN festivals_users_companies_users FU ON FU.user_id = CKT.user_id
			INNER JOIN festivals_users_companies FC ON FC.id = FU.company_id AND FC.festival_id = CKT.festival_id
			WHERE CKT.festival_id = " . $festival_id . "
				AND CKT.price < 0
				AND CKT.rec_time BETWEEN " . (int)$time_from . " AND " . (int)$time_to . "
			GROUP BY FC.id, FC.title
			ORDER BY total DESC, FC.title ASC
		";
		$sellers = $adodb->getAll($sql_sellers);
		if (!is_array($sellers)) {
			$sellers = [];
		}

		$total = 0.0;
		$quantity = 0;
		$receipts = 0;
		$items = array_map(function ($row) use (&$total, &$quantity, &$receipts) {
			$row_total = round((float)$row['total'], 2);
			$row_quantity = (int)$row['quantity'];
			$row_receipts = (int)$row['receipts'];
			$total = round($total + $row_total, 2);
			$quantity += $row_quantity;
			$receipts += $row_receipts;
			return [
				'id' => (int)$row['id'],
				'title' => $row['title'],
				'quantity' => $row_quantity,
				'receipts' => $row_receipts,
				'total' => $row_total,
			];
		}, $sellers);

		seller_json([
			'success' => true,
			'mode' => 'sellers',
			'date_from' => date('Y-m-d', $time_from),
			'date_to' => date('Y-m-d', $time_to),
			'total' => $total,
			'quantity' => $quantity,
			'receipts' => $receipts,
			'items' => $items,
		]);
	}

	$sql_company = "
		SELECT
			FC.id,
			FC.title,
			FC.festival_id,
			COALESCE(LVT_LT.text, LVT_EN.text, CONCAT('Festivalis #', FC.festival_id)) AS festival_title
		FROM festivals_users_companies FC
		LEFT JOIN languages_varchar LVT_LT ON LVT_LT.table_name = 'festivals'
			AND LVT_LT.table_id = FC.festival_id
			AND LVT_LT.keyword = 'title'
			AND LVT_LT.language = 'lt'
		LEFT JOIN languages_varchar LVT_EN ON LVT_EN.table_name = 'festivals'
			AND LVT_EN.table_id = FC.festival_id
			AND LVT_EN.keyword = 'title'
			AND LVT_EN.language = 'en'
		WHERE FC.id = " . $company_id . " AND FC.festival_id = " . $festival_id . "
		LIMIT 1
	";
	$company = $adodb->getRow($sql_company);
	if (!$company) {
		seller_json(['success' => false, 'message' => 'Prekybininkas nerastas.'], 404);
	}

	$sql_items = "
		SELECT
			COALESCE(PRI.id, 0) AS id,
			COALESCE(PRI.title, 'Laisva kaina') AS title,
			COUNT(CKT.id) AS quantity,
			SUM(0 - CKT.price) AS total
		FROM festivals_checkout CKT
		INNER JOIN festivals_users_companies_users FU ON FU.user_id = CKT.user_id
		INNER JOIN festivals_users_companies FC ON FC.id = FU.company_id AND FC.festival_id = CKT.festival_id
		LEFT JOIN festivals_users_companies_prices PRI ON PRI.id = CKT.price_id
		WHERE FC.id = " . $company_id . "
			AND CKT.festival_id = " . $festival_id . "
			AND CKT.price < 0
			AND CKT.rec_time BETWEEN " . (int)$time_from . " AND " . (int)$time_to . "
		GROUP BY COALESCE(PRI.id, 0), COALESCE(PRI.title, 'Laisva kaina')
		ORDER BY total DESC, PRI.title ASC
	";
	$items = $adodb->getAll($sql_items);
	if (!is_array($items)) {
		$items = [];
	}

	$sql_total = "
		SELECT
			COUNT(CKT.id) AS quantity,
			COUNT(DISTINCT IF(CKT.request_id IS NULL OR CKT.request_id = '', CONCAT('row-', CKT.id), CKT.request_id)) AS receipts,
			SUM(0 - CKT.price) AS total
		FROM festivals_checkout CKT
		INNER JOIN festivals_users_companies_users FU ON FU.user_id = CKT.user_id
		INNER JOIN festivals_users_companies FC ON FC.id = FU.company_id AND FC.festival_id = CKT.festival_id
		WHERE FC.id = " . $company_id . "
			AND CKT.festival_id = " . $festival_id . "
			AND CKT.price < 0
			AND CKT.rec_time BETWEEN " . (int)$time_from . " AND " . (int)$time_to . "
	";
	$total = $adodb->getRow($sql_total) ?: [];

	seller_json([
		'success' => true,
		'company' => [
			'id' => (int)$company['id'],
			'title' => $company['title'],
			'festival_id' => (int)$company['festival_id'],
			'festival_title' => $company['festival_title'],
		],
		'date_from' => date('Y-m-d', $time_from),
		'date_to' => date('Y-m-d', $time_to),
		'total' => round((float)($total['total'] ?? 0), 2),
		'quantity' => (int)($total['quantity'] ?? 0),
		'receipts' => (int)($total['receipts'] ?? 0),
		'items' => array_map(function ($row) {
			return [
				'id' => (int)$row['id'],
				'title' => $row['title'] ?: 'Laisva kaina',
				'quantity' => (int)$row['quantity'],
				'total' => round((float)$row['total'], 2),
			];
		}, $items),
	]);
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Prekybos taško pardavimai</title>
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; padding: 18px; background: #f4f7f9; color: #172033; font-family: Arial, sans-serif; }
		main { width: 100%; max-width: 880px; margin: 0 auto; }
		h1 { margin: 0 0 14px; font-size: 24px; }
		.panel, .summary, table { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 4px 14px rgba(16,24,40,.06); }
		.panel { padding: 16px; margin-bottom: 14px; }
		.filters { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 10px; align-items: end; }
		label { display: block; color: #475467; font-size: 13px; font-weight: 700; }
		input, select { width: 100%; min-height: 42px; margin-top: 5px; padding: 9px 10px; border: 1px solid #cfd5df; border-radius: 7px; background: #fff; font-size: 15px; }
		button { min-height: 42px; border: 0; border-radius: 7px; padding: 10px 14px; background: #0b66c3; color: white; font-weight: 700; font-size: 15px; cursor: pointer; }
		button:disabled { opacity: .55; cursor: not-allowed; }
		.status { margin-top: 10px; color: #667085; font-size: 14px; line-height: 1.4; }
		.summary { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1px; overflow: hidden; margin-bottom: 14px; }
		.summary div { padding: 14px; background: #fff; }
		.summary span { display: block; color: #667085; font-size: 13px; margin-bottom: 4px; }
		.summary strong { font-size: 20px; }
		table { width: 100%; border-collapse: collapse; overflow: hidden; }
		th, td { padding: 11px 12px; border-bottom: 1px solid #eef1f5; text-align: left; font-size: 14px; }
		th { background: #f8fafc; color: #475467; }
		td.number, th.number { text-align: right; }
		tr:last-child td { border-bottom: 0; }
		.empty { padding: 16px; color: #667085; background: #fff; border: 1px dashed #cfd5df; border-radius: 8px; }
		@media (max-width: 720px) {
			body { padding: 12px; }
			.filters { grid-template-columns: 1fr; }
			.summary { grid-template-columns: 1fr 1fr; }
			th, td { padding: 9px 8px; }
		}
	</style>
</head>
<body>
<main>
	<h1>Prekybos taško pardavimai</h1>

	<section class="panel">
		<div class="filters">
			<label>Data nuo
				<input id="date-from" type="date">
			</label>
			<label>Data iki
				<input id="date-to" type="date">
			</label>
			<label>Festivalis
				<select id="festival-id">
					<option value="">Pasirinkite</option>
				</select>
			</label>
			<label>Prekybininkas
				<select id="company-id">
					<option value="">Visi prekybininkai</option>
				</select>
			</label>
			<button id="load-btn" type="button">Rodyti</button>
		</div>
		<div id="status" class="status"></div>
	</section>

	<section id="summary" class="summary" hidden>
		<div><span>Prekybininkas</span><strong id="company-title">-</strong></div>
		<div><span>Suma</span><strong id="total">0.00</strong></div>
		<div><span>Kiekis</span><strong id="quantity">0</strong></div>
		<div><span>Čekiai</span><strong id="receipts">0</strong></div>
	</section>

	<section id="results"></section>
</main>

<script>
	const dateFrom = document.getElementById('date-from');
	const dateTo = document.getElementById('date-to');
	const festivalSelect = document.getElementById('festival-id');
	const companySelect = document.getElementById('company-id');
	const loadButton = document.getElementById('load-btn');
	const statusBox = document.getElementById('status');
	const summaryBox = document.getElementById('summary');
	const resultsBox = document.getElementById('results');

	function today() {
		return new Date().toISOString().slice(0, 10);
	}

	function money(value) {
		const number = parseFloat(value || 0);
		return number.toFixed(2);
	}

	function setStatus(text) {
		statusBox.textContent = text || '';
	}

	function escapeHtml(text) {
		return String(text || '').replace(/[&<>"']/g, function(char) {
			return ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' })[char];
		});
	}

	async function loadFestivals() {
		setStatus('Kraunami festivaliai...');
		const response = await fetch('?action=festivals');
		const data = await response.json();
		if (!data.success) {
			setStatus('Nepavyko užkrauti festivalių.');
			return;
		}

		data.items.forEach(function(item) {
			const option = document.createElement('option');
			option.value = item.id;
			option.textContent = item.title;
			festivalSelect.appendChild(option);
		});
		setStatus('');
	}

	async function loadCompanies() {
		companySelect.innerHTML = '<option value="">Visi prekybininkai</option>';
		if (!festivalSelect.value) {
			return;
		}
		setStatus('Kraunami prekybininkai...');
		const response = await fetch('?action=companies&festival_id=' + encodeURIComponent(festivalSelect.value));
		const data = await response.json();
		if (!data.success) {
			setStatus('Nepavyko užkrauti prekybininkų.');
			return;
		}

		data.items.forEach(function(item) {
			const option = document.createElement('option');
			option.value = item.id;
			option.textContent = item.title;
			companySelect.appendChild(option);
		});
		setStatus('');
	}

	async function loadReport() {
		if (!festivalSelect.value) {
			setStatus('Pasirinkite festivalį.');
			return;
		}

		setStatus('Kraunama ataskaita...');
		resultsBox.innerHTML = '';
		summaryBox.hidden = true;

		const body = new URLSearchParams();
		body.set('festival_id', festivalSelect.value);
		if (companySelect.value) {
			body.set('company_id', companySelect.value);
		}
		body.set('date_from', dateFrom.value);
		body.set('date_to', dateTo.value);

		const response = await fetch('?action=report', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString(),
		});
		const data = await response.json();
		if (!data.success) {
			setStatus(data.message || 'Nepavyko užkrauti ataskaitos.');
			return;
		}

		document.getElementById('company-title').textContent = data.mode === 'sellers' ? 'Visi' : data.company.title;
		document.getElementById('total').textContent = money(data.total);
		document.getElementById('quantity').textContent = data.quantity;
		document.getElementById('receipts').textContent = data.receipts;
		summaryBox.hidden = false;

		if (!data.items || !data.items.length) {
			resultsBox.innerHTML = '<div class="empty">Šiam laikotarpiui pardavimų nėra.</div>';
			setStatus(data.date_from + ' - ' + data.date_to);
			return;
		}

		const firstColumn = data.mode === 'sellers' ? 'Prekybininkas' : 'Prekė';
		const receiptColumn = data.mode === 'sellers' ? '<th class="number">Čekiai</th>' : '';
		resultsBox.innerHTML = '<table>' +
			'<thead><tr><th>' + firstColumn + '</th><th class="number">Kiekis</th>' + receiptColumn + '<th class="number">Suma</th></tr></thead>' +
			'<tbody>' + data.items.map(function(item) {
				const receiptCell = data.mode === 'sellers' ? '<td class="number">' + item.receipts + '</td>' : '';
				return '<tr>' +
					'<td>' + escapeHtml(item.title) + '</td>' +
					'<td class="number">' + item.quantity + '</td>' +
					receiptCell +
					'<td class="number">' + money(item.total) + '</td>' +
				'</tr>';
			}).join('') + '</tbody></table>';
		const statusPrefix = data.mode === 'sellers' ? 'Visi prekybininkai' : data.company.festival_title;
		setStatus(statusPrefix + ', ' + data.date_from + ' - ' + data.date_to);
	}

	dateFrom.value = today();
	dateTo.value = today();
	loadButton.addEventListener('click', loadReport);
	festivalSelect.addEventListener('change', loadCompanies);
	loadFestivals();
</script>
</body>
</html>
