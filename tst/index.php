<!DOCTYPE html>
<html lang="lt">
<head>
	<meta charset="UTF-8">
	<title>NFC Kasos Terminalas</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<style>
        .status-box { padding: 20px; border: 2px dashed #ccc; text-align: center; margin: 20px; }
        .active { border-color: #28a745; background-color: #eaffea; }
	</style>
</head>
<body>

<div id="nfc-status" class="status-box">
	<h3>NFC Terminalas</h3>
	<p id="msg">Spauskite mygtuką, kad įjungtumėte skenavimą</p>
	<button id="startScan">Įjungti NFC</button>
</div>

<div id="user-info" style="display:none; text-align:center;">
	<h2 id="user-name">Vardas Pavardė</h2>
	<p>Likutis: <span id="user-balance">0.00</span> €</p>
</div>

<script>
    const btn = document.getElementById('startScan');
    const msg = document.getElementById('msg');
    const nfcBox = document.getElementById('nfc-status');

    btn.addEventListener('click', async () => {
        // 1. Tikriname, ar naršyklė palaiko WebNFC
        if ('NDEFReader' in window) {
            try {
                const ndef = new NDEFReader();

                // 2. Aktyvuojame skenavimą (iššoks naršyklės užklausa "Allow")
                await ndef.scan();

                msg.innerText = "Skenavimas aktyvus! Priglauskite kortelę.";
                btn.style.display = 'none';
                nfcBox.classList.add('active');

                // 3. Klausomės nuskaitymo
                ndef.onreading = event => {
                    const cardUID = event.serialNumber;
                    processCard(cardUID);
                };

            } catch (error) {
                msg.innerText = "Klaida: " + error;
                console.error(error);
            }
        } else {
            msg.innerText = "Jūsų naršyklė nepalaiko WebNFC. Naudokite Android + Chrome.";
        }
    });

    // 4. Siunčiame ID į tavo PHP serverį
    function processCard(uid) {
        msg.innerText = "Ieškoma: " + uid;

        fetch('get_user_data.php?uid=' + uid)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('user-info').style.display = 'block';
                    document.getElementById('user-name').innerText = data.name;
                    document.getElementById('user-balance').innerText = data.balance;
                    msg.innerText = "Skenuoti kitą kortelę...";
                } else {
                    msg.innerText = "Kortelė neatpažinta: " + uid;
                }
            })
            .catch(err => {
                msg.innerText = "Serverio klaida.";
            });
    }
</script>
</body>
</html>