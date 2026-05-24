<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telefono NFC patikra</title>
    <style>
        :root {
            color-scheme: light;
            --bg: #f4f7f9;
            --card: #ffffff;
            --text: #202733;
            --muted: #667085;
            --border: #e4e7ec;
            --ok: #1f9d55;
            --fail: #d92d20;
            --warn: #d89000;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 20px;
            background: var(--bg);
            color: var(--text);
            font-family: Arial, sans-serif;
        }

        .diag-card {
            width: 100%;
            max-width: 430px;
            padding: 22px;
            border-radius: 10px;
            background: var(--card);
            box-shadow: 0 8px 24px rgba(16, 24, 40, 0.10);
        }

        h1 {
            margin: 0 0 18px;
            padding-bottom: 12px;
            border-bottom: 1px solid var(--border);
            font-size: 22px;
            line-height: 1.2;
        }

        .result {
            margin-bottom: 16px;
            padding: 14px;
            border-radius: 8px;
            border: 1px solid var(--border);
            border-left: 5px solid #98a2b3;
            background: #fbfcfe;
        }

        .result.ok {
            border-left-color: var(--ok);
            background: #f2fbf5;
        }

        .result.fail {
            border-left-color: var(--fail);
            background: #fff5f4;
        }

        .result.warn {
            border-left-color: var(--warn);
            background: #fffaf0;
        }

        .result strong {
            display: block;
            margin-bottom: 4px;
            font-size: 17px;
        }

        .result span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.4;
        }

        .step {
            display: grid;
            grid-template-columns: 34px 1fr;
            gap: 12px;
            align-items: center;
            margin-bottom: 12px;
            padding: 12px;
            border: 1px solid var(--border);
            border-left-width: 5px;
            border-radius: 8px;
            background: #fbfcfe;
        }

        .step.ok {
            border-left-color: var(--ok);
        }

        .step.fail {
            border-left-color: var(--fail);
        }

        .step.warn {
            border-left-color: var(--warn);
        }

        .status-icon {
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: #eef2f6;
            color: #475467;
            font-weight: 700;
            font-size: 14px;
        }

        .step.ok .status-icon {
            background: #e8f7ee;
            color: var(--ok);
        }

        .step.fail .status-icon {
            background: #fdecec;
            color: var(--fail);
        }

        .step.warn .status-icon {
            background: #fff6dd;
            color: var(--warn);
        }

        .info strong {
            display: block;
            margin-bottom: 3px;
            font-size: 14px;
        }

        .info span {
            display: block;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.35;
        }
    </style>
</head>
<body>

<main class="diag-card">
    <h1>Telefono NFC patikra</h1>

    <section id="main-result" class="result warn">
        <strong>Tikrinama...</strong>
        <span>Vertinama telefono naršyklė ir puslapio saugumas.</span>
    </section>

    <section id="check-secure" class="step warn">
        <div class="status-icon">...</div>
        <div class="info">
            <strong>Saugus kontekstas</strong>
            <span>Tikrinama...</span>
        </div>
    </section>

    <section id="check-device" class="step warn">
        <div class="status-icon">...</div>
        <div class="info">
            <strong>Telefono sistema</strong>
            <span>Tikrinama...</span>
        </div>
    </section>

    <section id="check-browser" class="step warn">
        <div class="status-icon">...</div>
        <div class="info">
            <strong>Chrome Web NFC</strong>
            <span>Tikrinama...</span>
        </div>
    </section>
</main>

<script>
    function updateStep(id, status, icon, text) {
        const element = document.getElementById(id);
        element.className = 'step ' + status;
        element.querySelector('.status-icon').textContent = icon;
        element.querySelector('span').textContent = text;
    }

    function updateResult(status, title, text) {
        const element = document.getElementById('main-result');
        element.className = 'result ' + status;
        element.querySelector('strong').textContent = title;
        element.querySelector('span').textContent = text;
    }

    function runChecks() {
        const userAgent = navigator.userAgent || '';
        const isAndroid = /Android/i.test(userAgent);
        const isChrome = /Chrome\/|CriOS\//i.test(userAgent) && !/Edg\/|OPR\//i.test(userAgent);
        const hasWebNfc = 'NDEFReader' in window;
        const isSecure = window.isSecureContext;
        const host = window.location.hostname;
        const isLocalHost = host === 'localhost' || host === '127.0.0.1' || host === '[::1]' || host.endsWith('.localhost');
        const isHttps = window.location.protocol === 'https:';

        if (isHttps) {
            updateStep('check-secure', 'ok', 'OK', 'HTTPS įjungtas. Web NFC gali veikti.');
        } else if (isSecure && isLocalHost) {
            updateStep('check-secure', 'warn', '!', 'Tai lokalus testavimo adresas. Naršyklė jį leidžia be HTTPS, bet telefone per tikrą domeną reikės HTTPS.');
        } else if (isSecure) {
            updateStep('check-secure', 'warn', '!', 'Naršyklė šį puslapį laiko saugiu, bet prieš renginį verta patikrinti su realiu HTTPS adresu.');
        } else {
            updateStep('check-secure', 'fail', 'X', 'Reikia HTTPS. Per paprastą HTTP telefone atsiskaitymo NFC nebus.');
        }

        if (isAndroid) {
            updateStep('check-device', 'ok', 'OK', 'Android telefonas tinkamas.');
        } else {
            updateStep('check-device', 'fail', 'X', 'Atsiskaitymui reikia Android telefono.');
        }

        if (isChrome && hasWebNfc) {
            updateStep('check-browser', 'ok', 'OK', 'Chrome palaiko Web NFC.');
        } else if (isChrome) {
            updateStep('check-browser', 'fail', 'X', 'Ši Chrome versija arba įrenginys Web NFC nepalaiko.');
        } else {
            updateStep('check-browser', 'fail', 'X', 'Atsiskaitymui reikia Chrome naršyklės su Web NFC palaikymu.');
        }

        if (isSecure && isAndroid && isChrome && hasWebNfc) {
            updateResult('ok', 'Šiuo telefonu atsiskaitymas turėtų veikti', 'Atsiskaitymo metu Chrome dar gali paprašyti įjungti NFC leidimą.');
        } else {
            updateResult('fail', 'Šiuo telefonu atsiskaitymas neveiks', 'Patikrinkite pažymėtus punktus žemiau.');
        }
    }

    runChecks();
</script>

</body>
</html>
