<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NFC Testavimo Įrankis</title>
    <style>
        body {
            font-family: sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f2f5;
        }
        .card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 90%;
            width: 400px;
        }
        #status {
            margin-top: 20px;
            font-weight: bold;
            color: #555;
        }
        #nfc-id {
            font-size: 1.5rem;
            margin-top: 20px;
            padding: 15px;
            background: #e7f3ff;
            border-radius: 10px;
            color: #007bff;
            display: none;
            word-break: break-all;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1rem;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:disabled {
            background-color: #ccc;
        }
        .error { color: #dc3545; }
        .success { color: #28a745; }
    </style>
</head>
<body>

<div class="card">
    <h2>NFC Testas</h2>
    <p>Šis įrankis patikrins, ar jūsų gaminami pakabukai yra suderinami su sistema.</p>
    
    <button id="start-btn">PRADĖTI SKENAVIMĄ</button>
    
    <div id="status">Laukiama paspaudimo...</div>
    <div id="nfc-id"></div>
</div>

<script>
    const startBtn = document.getElementById('start-btn');
    const statusDiv = document.getElementById('status');
    const nfcIdDiv = document.getElementById('nfc-id');

    // 1. Tikriname ar naršyklė palaiko NFC
    if (!('NDEFReader' in window)) {
        statusDiv.innerHTML = '<span class="error">Klaida: Jūsų naršyklė nepalaiko WebNFC. Naudokite Chrome per Android.</span>';
        startBtn.disabled = true;
    }

        try {
            statusDiv.textContent = "Prašoma leidimo...";
            const ndef = new NDEFReader();
            
            // Reikia naudoti scan(), kad gautume prieigą
            await ndef.scan();
            
            statusDiv.innerHTML = '<span class="success">Skenavimas aktyvus! Priglauskite pakabuką prie telefono galinės dalies.</span>';
            startBtn.disabled = true;

            ndef.onreading = event => {
                const serialNumber = event.serialNumber;
                
                // Suvibruojame (jei leidžia telefonas)
                if (navigator.vibrate) navigator.vibrate(200);
                
                // Parodome ID
                nfcIdDiv.textContent = "Nuskaitytas ID: " + serialNumber;
                nfcIdDiv.style.display = "block";
                
                statusDiv.innerHTML = '<span class="success">Sėkmingai nuskaityta!</span>';
                
                // Po 3 sekundžių leidžiame skenuoti vėl
                setTimeout(() => {
                    statusDiv.innerHTML = '<span class="success">Paruošta kitam skenavimui.</span>';
                }, 3000);
            };

            ndef.onreadingerror = () => {
                statusDiv.innerHTML = '<span class="error">Klaida: Nepavyko perskaityti žymos. Pabandykite dar kartą.</span>';
            };

        } catch (error) {
            statusDiv.innerHTML = '<span class="error">Klaida: ' + error + '</span>';
            console.error(error);
        }
</script>

</body>
</html>