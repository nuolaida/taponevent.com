<!DOCTYPE html>
<html lang="lt">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>NFC Diagnostika</title>
	<style>
        body { font-family: sans-serif; background: #f4f7f9; padding: 20px; display: flex; justify-content: center; }
        .diag-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        h2 { margin-top: 0; color: #333; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .step { display: flex; align-items: center; margin-bottom: 15px; padding: 10px; border-radius: 8px; background: #fafafa; }
        .status-icon { font-size: 24px; margin-right: 15px; }
        .info { flex-grow: 1; }
        .info div { font-weight: bold; font-size: 14px; }
        .info span { font-size: 12px; color: #666; }

        .ok { border-left: 5px solid #28a745; }
        .fail { border-left: 5px solid #dc3545; }
        .warn { border-left: 5px solid #ffc107; }
	</style>
</head>
<body>

<div class="diag-card">
	<h2>Telefono patikra</h2>

	<div id="check-https" class="step">
		<div class="status-icon">⏳</div>
		<div class="info">
			<div>Saugus ryšys (HTTPS)</div>
			<span id="https-desc">Tikrinama...</span>
		</div>
	</div>

	<div id="check-browser" class="step">
		<div class="status-icon">⏳</div>
		<div class="info">
			<div>WebNFC palaikymas</div>
			<span id="browser-desc">Tikrinama...</span>
		</div>
	</div>

	<div id="check-hardware" class="step">
		<div class="status-icon">⏳</div>
		<div class="info">
			<div>NFC modulis / leidimas</div>
			<span id="hardware-desc">Laukiama ankstesnių žingsnių...</span>
		</div>
	</div>

	<button id="btn-recheck" style="width:100%; padding:12px; background:#007bff; color:white; border:none; border-radius:6px; cursor:pointer; margin-top:10px;">Tikrinti iš naujo</button>
</div>

<script>
	function runDiagnostics() {
		// 1. HTTPS Tikrinimas
		const httpsStep = document.getElementById('check-https');
		if (window.location.protocol === 'https:') {
			updateStep(httpsStep, 'ok', '✅', 'Ryšys saugus.');
		} else {
			updateStep(httpsStep, 'fail', '❌', 'Būtinas HTTPS! Per HTTP neveiks.');
		}

		// 2. Naršyklės palaikymo tikrinimas
		const browserStep = document.getElementById('check-browser');
		if ('NDEFReader' in window) {
			updateStep(browserStep, 'ok', '✅', 'Chrome naršyklė tinkama.');

			// 3. Tikriname ar NFC įjungtas (reikia vartotojo paspaudimo)
			const hardwareStep = document.getElementById('check-hardware');
			checkHardware(hardwareStep);
		} else {
			updateStep(browserStep, 'fail', '❌', 'Naršyklė nepalaiko WebNFC. Naudokite Chrome.');
			updateStep(document.getElementById('check-hardware'), 'fail', '🚫', 'Negalima patikrinti.');
		}
	}

	async function checkHardware(element) {
		try {
			const ndef = new NDEFReader();
			// Bandome tiesiog "paliesti" skenerį
			await ndef.scan();
			updateStep(element, 'ok', '✅', 'NFC įjungtas ir veikia.');
		} catch (error) {
			if (error.name === 'NotAllowedError') {
				updateStep(element, 'fail', '❌', 'Leidimas atmestas.');
			} else if (error.name === 'NotSupportedError') {
				updateStep(element, 'fail', '❌', 'NFC modulis nerastas.');
			} else {
				updateStep(element, 'warn', '⚠️', 'NFC išjungtas nustatymuose.');
			}
		}
	}

	function updateStep(el, status, icon, desc) {
		el.className = 'step ' + status;
		el.querySelector('.status-icon').textContent = icon;
		el.querySelector('span').textContent = desc;
	}

	document.getElementById('btn-recheck').onclick = () => location.reload();

	// Paleidžiame diagnostiką
	runDiagnostics();
</script>

</body>
</html>