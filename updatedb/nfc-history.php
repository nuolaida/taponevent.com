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

function nfc_history_bootstrap(): void
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

function nfc_history_json(array $data, int $status = 200): void
{
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

function nfc_history_time($time): string
{
	$time = (int)$time;
	if (!$time) {
		return '';
	}
	return date('Y-m-d H:i:s', $time);
}

if (($_GET['action'] ?? '') === 'festivals') {
	nfc_history_bootstrap();
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
		ORDER BY F.time_starts DESC, F.id DESC
	";
	$rows = $adodb->getAll($sql);
	if (!is_array($rows)) {
		$rows = [];
	}

	nfc_history_json([
		'success' => true,
		'items' => array_map(function ($row) {
			return [
				'id' => (int)$row['id'],
				'title' => $row['title'],
			];
		}, $rows),
	]);
}

if (($_GET['action'] ?? '') === 'history') {
	nfc_history_bootstrap();
	global $adodb;

	$nfc_id = trim((string)($_POST['nfc_id'] ?? $_GET['nfc_id'] ?? ''));
	$festival_id = (int)($_POST['festival_id'] ?? $_GET['festival_id'] ?? 0);
	if ($nfc_id === '') {
		nfc_history_json(['success' => false, 'message' => 'NFC ID negautas.'], 400);
	}

	$nfc_sql = $adodb->qstr($nfc_id);
	$festival_where = $festival_id ? ' AND CKT.festival_id = ' . $festival_id : '';
	$sql = "
		SELECT
			CKT.id,
			CKT.festival_id,
			CKT.user_id,
			CKT.rec_time,
			CKT.nfc_id,
			CKT.price_id,
			CKT.price,
			CKT.request_id,
			PRI.title AS price_title,
			COALESCE(PRICE_FC.title, USER_FC.title) AS company_title,
			U.email AS operator_email,
			U.name AS operator_name,
			LVT.text AS festival_title
		FROM festivals_checkout CKT
		LEFT JOIN festivals_users_companies_prices PRI ON PRI.id = CKT.price_id
		LEFT JOIN festivals_users_companies PRICE_FC ON PRICE_FC.id = PRI.company_id
		LEFT JOIN festivals_users_companies_users FU ON FU.user_id = CKT.user_id
		LEFT JOIN festivals_users_companies USER_FC ON USER_FC.id = FU.company_id AND USER_FC.festival_id = CKT.festival_id
		LEFT JOIN users U ON U.id = CKT.user_id
		LEFT JOIN languages_varchar LVT ON LVT.table_name = 'festivals'
			AND LVT.table_id = CKT.festival_id
			AND LVT.keyword = 'title'
			AND LVT.language = 'lt'
		WHERE CKT.nfc_id = {$nfc_sql}{$festival_where}
		GROUP BY CKT.id
		ORDER BY CKT.rec_time ASC, CKT.id ASC
	";

	$rows = $adodb->getAll($sql);
	if (!is_array($rows)) {
		$rows = [];
	}

	$balance = 0.0;
	$topup_total = 0.0;
	$purchase_total = 0.0;
	$items = [];

	foreach ($rows as $row) {
		$amount = round((float)$row['price'], 2);
		$balance = round($balance + $amount, 2);
		if ($amount >= 0) {
			$type = 'Papildymas';
			$topup_total = round($topup_total + $amount, 2);
		} else {
			$type = 'Pirkimas';
			$purchase_total = round($purchase_total + abs($amount), 2);
		}

		$items[] = [
			'id' => (int)$row['id'],
			'time' => nfc_history_time($row['rec_time']),
			'type' => $type,
			'amount' => $amount,
			'balance_after' => $balance,
			'festival' => $row['festival_title'] ?: ('Festivalis #' . (int)$row['festival_id']),
			'company' => $row['company_title'] ?: '',
			'item' => $row['price_title'] ?: '',
			'operator' => $row['operator_name'] ?: $row['operator_email'] ?: ('Vartotojas #' . (int)$row['user_id']),
			'request_id' => $row['request_id'] ?: '',
		];
	}

	nfc_history_json([
		'success' => true,
		'nfc_id' => $nfc_id,
		'festival_id' => $festival_id,
		'balance' => $balance,
		'topup_total' => $topup_total,
		'purchase_total' => $purchase_total,
		'count' => count($items),
		'items' => array_reverse($items),
	]);
}
?>
<!DOCTYPE html>
<html lang="lt">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NFC istorija</title>
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; padding: 18px; background: #f4f7f9; color: #172033; font-family: Arial, sans-serif; }
		main { width: 100%; max-width: 780px; margin: 0 auto; }
		h1 { margin: 0 0 14px; font-size: 24px; }
		.panel, .summary, .item { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 4px 14px rgba(16,24,40,.06); }
		.panel { padding: 16px; margin-bottom: 14px; }
		.actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 12px; }
		button { min-height: 44px; border: 0; border-radius: 7px; padding: 10px 14px; background: #0b66c3; color: white; font-weight: 700; font-size: 15px; cursor: pointer; }
		button.secondary { background: #344054; }
		button:disabled { opacity: .55; cursor: not-allowed; }
		input { width: 100%; min-height: 42px; margin-top: 10px; padding: 9px 10px; border: 1px solid #cfd5df; border-radius: 7px; font-size: 15px; }
		select { width: 100%; min-height: 42px; margin-top: 10px; padding: 9px 10px; border: 1px solid #cfd5df; border-radius: 7px; background: #fff; font-size: 15px; }
		.status { margin-top: 10px; color: #667085; font-size: 14px; line-height: 1.4; }
		.summary { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1px; overflow: hidden; margin-bottom: 14px; }
		.summary div { padding: 14px; background: #fff; }
		.summary span { display: block; color: #667085; font-size: 13px; margin-bottom: 4px; }
		.summary strong { font-size: 20px; }
		.item { padding: 13px; margin-bottom: 10px; }
		.item-head { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; margin-bottom: 7px; }
		.type { font-weight: 700; }
		.amount { font-weight: 700; font-size: 18px; white-space: nowrap; }
		.amount.plus { color: #16803c; }
		.amount.minus { color: #b42318; }
		.meta { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 4px 14px; color: #475467; font-size: 13px; line-height: 1.35; }
		.empty { padding: 16px; color: #667085; background: #fff; border: 1px dashed #cfd5df; border-radius: 8px; }
		@media (max-width: 560px) {
			body { padding: 12px; }
			.summary { grid-template-columns: 1fr; }
			.meta { grid-template-columns: 1fr; }
			.item-head { display: block; }
			.amount { margin-top: 5px; }
		}
	</style>
</head>
<body>
<main>
	<h1>NFC kortelės istorija</h1>

	<section class="panel">
		<div>Nuskenuokite kortelę arba įveskite NFC ID rankiniu būdu.</div>
		<select id="festival-id">
			<option value="">Visi festivaliai</option>
		</select>
		<input id="nfc-id" type="text" placeholder="NFC ID">
		<div class="actions">
			<button id="scan-btn" type="button">Skenuoti NFC</button>
			<button id="load-btn" class="secondary" type="button">Rodyti istoriją</button>
		</div>
		<div id="status" class="status"></div>
	</section>

	<section id="summary" class="summary" hidden>
		<div><span>Likutis</span><strong id="balance">0.00</strong></div>
		<div><span>Papildyta</span><strong id="topup-total">0.00</strong></div>
		<div><span>Išleista</span><strong id="purchase-total">0.00</strong></div>
	</section>

	<section id="results"></section>
</main>

<script>
	const nfcInput = document.getElementById('nfc-id');
	const festivalSelect = document.getElementById('festival-id');
	const scanButton = document.getElementById('scan-btn');
	const loadButton = document.getElementById('load-btn');
	const statusBox = document.getElementById('status');
	const summaryBox = document.getElementById('summary');
	const resultsBox = document.getElementById('results');
	let readerStarted = false;

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

	async function loadHistory(nfcId) {
		nfcId = (nfcId || nfcInput.value || '').trim();
		if (!nfcId) {
			setStatus('NFC ID tuščias.');
			return;
		}

		setStatus('Kraunama istorija...');
		resultsBox.innerHTML = '';
		summaryBox.hidden = true;

		const body = new URLSearchParams();
		body.set('nfc_id', nfcId);
		if (festivalSelect.value) {
			body.set('festival_id', festivalSelect.value);
		}

		const response = await fetch('?action=history', {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString(),
		});
		const data = await response.json();
		if (!data.success) {
			setStatus(data.message || 'Nepavyko gauti istorijos.');
			return;
		}

		document.getElementById('balance').textContent = money(data.balance);
		document.getElementById('topup-total').textContent = money(data.topup_total);
		document.getElementById('purchase-total').textContent = money(data.purchase_total);
		summaryBox.hidden = false;

		if (!data.items || !data.items.length) {
			resultsBox.innerHTML = '<div class="empty">Šiai kortelei įrašų nerasta.</div>';
			setStatus('Kortelė nuskaityta: ' + data.nfc_id);
			return;
		}

		resultsBox.innerHTML = data.items.map(function(item) {
			const isTopup = parseFloat(item.amount) >= 0;
			const amountClass = isTopup ? 'plus' : 'minus';
			const amountPrefix = isTopup ? '+' : '';
			return '<article class="item">' +
				'<div class="item-head">' +
					'<div><div class="type">' + escapeHtml(item.type) + '</div><div>' + escapeHtml(item.time) + '</div></div>' +
					'<div class="amount ' + amountClass + '">' + amountPrefix + money(item.amount) + '</div>' +
				'</div>' +
				'<div class="meta">' +
					'<div><strong>Likutis po:</strong> ' + money(item.balance_after) + '</div>' +
					'<div><strong>Festivalis:</strong> ' + escapeHtml(item.festival) + '</div>' +
					'<div><strong>Vieta:</strong> ' + escapeHtml(item.company || '-') + '</div>' +
					'<div><strong>Prekė:</strong> ' + escapeHtml(item.item || '-') + '</div>' +
					'<div><strong>Operatorius:</strong> ' + escapeHtml(item.operator || '-') + '</div>' +
					'<div><strong>ID:</strong> ' + escapeHtml(item.id) + '</div>' +
				'</div>' +
			'</article>';
		}).join('');
		setStatus('Kortelė nuskaityta: ' + data.nfc_id);
	}

	async function loadFestivals() {
		try {
			const response = await fetch('?action=festivals');
			const data = await response.json();
			if (!data.success || !data.items) {
				return;
			}

			data.items.forEach(function(item) {
				const option = document.createElement('option');
				option.value = item.id;
				option.textContent = item.title;
				festivalSelect.appendChild(option);
			});
		} catch (error) {
			setStatus('Festivalio filtro nepavyko užkrauti, bet NFC istoriją galima tikrinti be filtro.');
		}
	}

	async function startNfc() {
		if (!('NDEFReader' in window)) {
			setStatus('Ši naršyklė nepalaiko Web NFC. Reikia Android Chrome.');
			return;
		}
		if (!window.isSecureContext) {
			setStatus('Web NFC veikia tik per HTTPS arba lokalų testavimo adresą.');
			return;
		}
		if (readerStarted) {
			setStatus('Skenavimas jau įjungtas. Prilieskite kortelę.');
			return;
		}

		try {
			setStatus('Jungiamas NFC skenavimas...');
			const reader = new NDEFReader();
			await reader.scan();
			readerStarted = true;
			scanButton.disabled = true;
			setStatus('Prilieskite NFC kortelę prie telefono.');

			reader.onreading = function(event) {
				const nfcId = (event.serialNumber || '').trim();
				if (!nfcId || /empty\s*tag/i.test(nfcId)) {
					setStatus('Kortelė sureagavo, bet NFC ID tuščias. Pabandykite priliesti trumpiau.');
					return;
				}
				nfcInput.value = nfcId;
				if (navigator.vibrate) {
					navigator.vibrate(120);
				}
				loadHistory(nfcId);
			};
			reader.onreadingerror = function() {
				setStatus('Kortelė sureagavo, bet nepavyko nuskaityti. Bandykite dar kartą.');
			};
		} catch (error) {
			const name = error && error.name ? error.name : '';
			if (name === 'NotAllowedError') {
				setStatus('Chrome leidimas atmestas. Paspauskite dar kartą ir suteikite leidimą.');
			} else if (name === 'NotSupportedError') {
				setStatus('Telefonas arba Chrome nepalaiko Web NFC.');
			} else if (name === 'NotReadableError') {
				setStatus('NFC nepasileido. Patikrinkite, ar telefone įjungtas NFC.');
			} else {
				setStatus('Nepavyko įjungti NFC: ' + (error.message || name || 'nežinoma klaida'));
			}
		}
	}

	scanButton.addEventListener('click', startNfc);
	loadButton.addEventListener('click', function() {
		loadHistory();
	});
	festivalSelect.addEventListener('change', function() {
		if (nfcInput.value.trim()) {
			loadHistory();
		}
	});
	loadFestivals();
</script>
</body>
</html>
