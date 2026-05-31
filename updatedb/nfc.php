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

function nfc_bootstrap(): void
{
	static $bootstrapped = false;
	if ($bootstrapped) {
		return;
	}

	global $adodb, $page_special_config;
	if (session_status() !== PHP_SESSION_ACTIVE) {
		ini_set("session.gc_maxlifetime", 28800);
		ini_set("session.cookie_lifetime", 10800);
		ini_set("session.cookie_domain", "");
		session_cache_limiter("must-revalidate");
		session_set_cookie_params(time() + 3600);
		session_start();
	}

	include_once CONFIG_FILES_PATH . 'config.bn2.php';
	include_once CONFIG_FILES_PATH . 'config.db.php';
	$bootstrapped = true;
}

function nfc_json(array $data, int $status = 200): void
{
	http_response_code($status);
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode($data, JSON_UNESCAPED_UNICODE);
	exit;
}

function nfc_time($time): string
{
	$time = (int)$time;
	return $time ? date('Y-m-d H:i:s', $time) : '';
}

if (($_GET['action'] ?? '') === 'history') {
	nfc_bootstrap();
	global $adodb;

	$nfc_id = trim((string)($_POST['nfc_id'] ?? $_GET['nfc_id'] ?? ''));
	if ($nfc_id === '') {
		nfc_json(['success' => false, 'message' => 'NFC number was not received.'], 400);
	}

	$nfc_sql = $adodb->qstr($nfc_id);
	$sql = "
		SELECT
			CKT.id,
			CKT.rec_time,
			CKT.price,
			PRI.title AS price_title,
			COALESCE(PRICE_FC.title, USER_FC.title) AS company_title
		FROM festivals_checkout CKT
		LEFT JOIN festivals_users_companies_prices PRI ON PRI.id = CKT.price_id
		LEFT JOIN festivals_users_companies PRICE_FC ON PRICE_FC.id = PRI.company_id
		LEFT JOIN festivals_users_companies_users FU ON FU.user_id = CKT.user_id
		LEFT JOIN festivals_users_companies USER_FC ON USER_FC.id = FU.company_id AND USER_FC.festival_id = CKT.festival_id
		WHERE CKT.nfc_id = {$nfc_sql}
		GROUP BY CKT.id
		ORDER BY CKT.rec_time ASC, CKT.id ASC
	";
	$rows = $adodb->getAll($sql);
	if (!is_array($rows)) {
		$rows = [];
	}

	$balance = 0.0;
	$items = [];
	foreach ($rows as $row) {
		$amount = round((float)$row['price'], 2);
		$balance = round($balance + $amount, 2);
		$items[] = [
			'id' => (int)$row['id'],
			'time' => nfc_time($row['rec_time']),
			'amount' => $amount,
			'product' => $amount > 0 ? 'Top-up' : ($row['price_title'] ?: 'Custom price'),
			'company' => $row['company_title'] ?: '',
		];
	}

	nfc_json([
		'success' => true,
		'nfc_id' => $nfc_id,
		'balance' => $balance,
		'items' => array_reverse($items),
	]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NFC Check</title>
	<style>
		* { box-sizing: border-box; }
		body { margin: 0; padding: 18px; background: #f4f7f9; color: #172033; font-family: Arial, sans-serif; }
		main { width: 100%; max-width: 860px; margin: 0 auto; }
		h1 { margin: 0 0 14px; font-size: 24px; }
		.panel, .summary, table { background: #fff; border: 1px solid #e4e7ec; border-radius: 8px; box-shadow: 0 4px 14px rgba(16,24,40,.06); }
		.panel { padding: 16px; margin-bottom: 14px; }
		button { min-height: 44px; border: 0; border-radius: 7px; padding: 10px 14px; background: #0b66c3; color: white; font-weight: 700; font-size: 15px; cursor: pointer; }
		button:disabled { opacity: .55; cursor: not-allowed; }
		.status { margin-top: 10px; color: #667085; font-size: 14px; line-height: 1.4; }
		.summary { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1px; overflow: hidden; margin-bottom: 14px; }
		.summary div { padding: 14px; background: #fff; }
		.summary span { display: block; color: #667085; font-size: 13px; margin-bottom: 4px; }
		.summary strong { font-size: 20px; overflow-wrap: anywhere; }
		table { width: 100%; border-collapse: collapse; overflow: hidden; }
		th, td { padding: 10px; border-bottom: 1px solid #e4e7ec; text-align: left; vertical-align: top; }
		th { color: #475467; font-size: 13px; background: #f8fafc; }
		td.amount { font-weight: 700; white-space: nowrap; }
		.plus { color: #16803c; }
		.minus { color: #b42318; }
		.empty { padding: 16px; color: #667085; background: #fff; border: 1px dashed #cfd5df; border-radius: 8px; }
		@media (max-width: 640px) {
			body { padding: 12px; }
			.summary { grid-template-columns: 1fr; }
			table, thead, tbody, th, td, tr { display: block; }
			thead { display: none; }
			tr { border-bottom: 1px solid #e4e7ec; padding: 8px 0; }
			td { border: 0; padding: 5px 10px; }
			td::before { display: block; color: #667085; font-size: 12px; }
			td:nth-child(1)::before { content: 'Time'; }
			td:nth-child(2)::before { content: 'Amount'; }
			td:nth-child(3)::before { content: 'Product'; }
			td:nth-child(4)::before { content: 'Company'; }
		}
	</style>
</head>
<body>
<main>
	<h1>NFC Check</h1>

	<section class="panel">
		<button id="scan-btn" type="button">Enable NFC scanning</button>
		<div id="status" class="status"></div>
	</section>

	<section id="summary" class="summary" hidden>
		<div><span>NFC number</span><strong id="nfc-number"></strong></div>
		<div><span>Balance</span><strong id="balance">0.00</strong></div>
	</section>

	<section id="results"></section>
</main>

<script>
	const scanButton = document.getElementById('scan-btn');
	const statusBox = document.getElementById('status');
	const summaryBox = document.getElementById('summary');
	const nfcNumberBox = document.getElementById('nfc-number');
	const balanceBox = document.getElementById('balance');
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
		nfcId = (nfcId || '').trim();
		if (!nfcId) {
			setStatus('NFC number is empty.');
			return;
		}

		setStatus('Loading NFC history...');
		resultsBox.innerHTML = '';
		summaryBox.hidden = true;

		const body = new URLSearchParams();
		body.set('nfc_id', nfcId);

		let data;
		try {
			const response = await fetch('?action=history', {
				method: 'POST',
				headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
				body: body.toString(),
			});
			const text = await response.text();
			try {
				data = JSON.parse(text);
			} catch (error) {
				setStatus('Server returned an invalid response: ' + text.replace(/\s+/g, ' ').slice(0, 160));
				return;
			}
			if (!response.ok || !data.success) {
				setStatus((data && data.message) || 'Could not load NFC history.');
				return;
			}
		} catch (error) {
			setStatus('Could not load NFC history: ' + (error.message || 'network error'));
			return;
		}

		nfcNumberBox.textContent = data.nfc_id;
		balanceBox.textContent = money(data.balance);
		summaryBox.hidden = false;

		if (!data.items || !data.items.length) {
			resultsBox.innerHTML = '<div class="empty">No records found for this NFC.</div>';
			setStatus('NFC scanned.');
			return;
		}

		resultsBox.innerHTML = '<table><thead><tr><th>Time</th><th>Amount</th><th>Product</th><th>Company</th></tr></thead><tbody>' +
			data.items.map(function(item) {
				const amount = parseFloat(item.amount);
				const isTopup = amount >= 0;
				return '<tr>' +
					'<td>' + escapeHtml(item.time) + '</td>' +
					'<td class="amount ' + (isTopup ? 'plus' : 'minus') + '">' + (isTopup ? '+' : '') + money(amount) + '</td>' +
					'<td>' + escapeHtml(item.product || '-') + '</td>' +
					'<td>' + escapeHtml(item.company || '-') + '</td>' +
				'</tr>';
			}).join('') +
			'</tbody></table>';
		setStatus('NFC scanned.');
	}

	async function startNfc() {
		if (!('NDEFReader' in window)) {
			setStatus('This browser does not support Web NFC. Android Chrome is required.');
			return;
		}
		if (!window.isSecureContext) {
			setStatus('Web NFC requires HTTPS or a local testing address.');
			return;
		}
		if (readerStarted) {
			setStatus('Scanning is already enabled. Touch an NFC card.');
			return;
		}

		try {
			setStatus('Starting NFC scanning...');
			const reader = new NDEFReader();
			await reader.scan();
			readerStarted = true;
			scanButton.disabled = true;
			setStatus('Touch an NFC card to the phone.');

			reader.onreading = function(event) {
				const nfcId = (event.serialNumber || '').trim();
				if (!nfcId || /empty\s*tag/i.test(nfcId)) {
					setStatus('The card reacted, but NFC number was empty. Try touching it briefly.');
					return;
				}
				if (navigator.vibrate) {
					navigator.vibrate(120);
				}
				loadHistory(nfcId);
			};
			reader.onreadingerror = function() {
				setStatus('The card reacted, but could not be read. Try again.');
			};
		} catch (error) {
			const name = error && error.name ? error.name : '';
			if (name === 'NotAllowedError') {
				setStatus('Chrome permission was denied. Press the button again and allow NFC scanning.');
			} else if (name === 'NotSupportedError') {
				setStatus('This phone or Chrome does not support Web NFC.');
			} else if (name === 'NotReadableError') {
				setStatus('NFC could not be started. Check if NFC is enabled on the phone.');
			} else {
				setStatus('Could not start NFC: ' + (error.message || name || 'unknown error'));
			}
		}
	}

	scanButton.addEventListener('click', startNfc);
</script>
</body>
</html>
