<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Išsamus NFC Klaidos Testas</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            background-color: #f8f9fa;
        }
        .card {
            background: white;
            padding: 2.5rem;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            text-align: center;
            max-width: 90%;
            width: 440px;
        }
        #status {
            margin-top: 20px;
            font-size: 1.1rem;
            font-weight: bold;
            color: #495057;
        }
        #log-area {
            margin-top: 20px;
            padding: 15px;
            background: #212529;
            color: #00ff66;
            font-family: monospace;
            border-radius: 8px;
            text-align: left;
            font-size: 13px;
            max-height: 150px;
            overflow-y: auto;
            white-space: pre-wrap;
        }
        button {
            background-color: #20c997;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1rem;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-weight: bold;
        }
        button:disabled {
            background-color: #ced4da;
        }
        .error-box {
            margin-top: 15px;
            padding: 15px;
            background: #fff5f5;
            border-left: 5px solid #e03131;
            color: #c92a2a;
            border-radius: 4px;
            text-align: left;
            display: none;
        }
    </style>
</head>
<body>

<div class="card">
    <h2>Išsamus NFC Diagnostikos Testas</h2>
    <p style="color: #6c757d;">Skirtas nustatyti, kodėl telefonas vibruoja, bet neperduoda duomenų.</p>
    
    <button id="start-btn">PRADĖTI SKENAVIMĄ</button>
    
    <div id="status">Paspauskite mygtuką aukščiau...</div>
    <div id="error-display" class="error-box"></div>
    
    <div style="text-align: left; margin-top: 20px; font-weight: bold; color: #495057;">Sistemos Logas:</div>
    <div id="log-area">Laukia aktyvavimo...</div>
</div>

<script>
    const startBtn = document.getElementById('start-btn');
    const statusDiv = document.getElementById('status');
    const errorDisplay = document.getElementById('error-display');
    const logArea = document.getElementById('log-area');

    function log(text) {
        const time = new Date().toLocaleTimeString();
        logArea.textContent += `[${time}] ${text}\n`;
        logArea.scrollTop = logArea.scrollHeight;
    }

    if (!('NDEFReader' in window)) {
        statusDiv.innerHTML = '<span style="color: #e03131;">Naršyklė nepalaiko WebNFC!</span>';
        log("KLAIDA: NDEFReader nerastas lange (window). Netinkama naršyklė.");
        startBtn.disabled = true;
    }

    startBtn.addEventListener('click', async () => {
        errorDisplay.style.display = 'none';
        errorDisplay.textContent = '';
        log("Inicijuojamas NFC skenavimas...");
        
        try {
            const ndef = new NDEFReader();
            statusDiv.textContent = "Prašoma leidimo iš Android...";
            
            await ndef.scan();
            
            statusDiv.innerHTML = '<span style="color: #2b8a3e;">Skenavimas aktyvus! Priglauskite kortelę.</span>';
            log("Skenavimas sėkmingai paleistas. Laukiamas NFC signalas.");
            startBtn.disabled = true;

            // Sėkmingo nuskaitymo gaudymas
            ndef.onreading = event => {
                log(`SIGNALAS GAUTAS! Serial: ${event.serialNumber}`);
                statusDiv.innerHTML = `<span style="color: #2b8a3e; font-size: 1.4rem;">ID: ${event.serialNumber}</span>`;
                if (navigator.vibrate) navigator.vibrate(200);
            };

            // KLAIDOS GAUDYMAS (Čia svarbiausia dalis šiam S22)
            ndef.onreadingerror = (error) => {
                log("UŽFIKSUOTA SKAITYMO KLAIDA!");
                console.error(error);
                
                errorDisplay.style.display = 'block';
                errorDisplay.innerHTML = `
                    <strong>Klaidos tipas:</strong> ${error.name || 'Nežinomas'}<br>
                    <strong>Žinutė:</strong> ${error.message || 'Nepavyko iškoduoti kortelės duomenų.'}<br><br>
                    <small>Jei telefonas suvibravo, bet matai šią klaidą – Android fone veikianti programa (pvz. Google Pay) blokuoja duomenų perdavimą naršyklei.</small>
                `;
                statusDiv.innerHTML = '<span style="color: #e03131;">Klaida nuskaitant! Žiūrėkite žemiau.</span>';
            };

        } catch (error) {
            log(`Kritinė klaida: ${error.name}`);
            errorDisplay.style.display = 'block';
            errorDisplay.innerHTML = `<strong>Nepavyko paleisti skenerio:</strong> ${error.message} (${error.name})`;
            statusDiv.textContent = "Klaida.";
        }
    });
</script>

</body>
</html>