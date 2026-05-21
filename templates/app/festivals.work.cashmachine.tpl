<div class="te-pos-wrapper te-topup-theme">
	<div class="te-topup-header">
		<span class="te-label">PAPILDYMO SUMA</span>
		<div class="te-display-row">
			<span id="te-total-display" data-testing-card="{$testing_card|default:''}" data-dev="{$is_development_version|default:0}">0.00</span>
		</div>
	</div>

	<div class="te-container">
		<div class="te-quick-grid">
			<button type="button" class="te-amount-btn" data-val="5">5</button>
			<button type="button" class="te-amount-btn" data-val="10">10</button>
			<button type="button" class="te-amount-btn" data-val="20">20</button>
			<button type="button" class="te-amount-btn" data-val="50">50</button>
		</div>

		<div class="te-input-wrapper">
			<input type="number" id="te-custom-price" aria-label="Įveskite sumą" placeholder="Kita suma..." inputmode="decimal">
			<button type="button" id="te-reset-btn">✕</button>
		</div>

		<div class="te-status-bar">Pridėkite kortelę papildymui</div>
	</div>

	<form id="te-hidden-form" action="app.php" method="POST" style="display:none;" autocomplete="off">
		<input type="hidden" name="module" value="festivals">
		<input type="hidden" name="action" value="cashmachineAct">
		<input type="hidden" name="topup_amount" id="te-final-amount">
		<input type="hidden" name="nfc_id" id="te-nfc-id">
		<input type="hidden" name="request_id" id="te-request-id">
	</form>
</div>


<script>
{literal}
     $(document).ready(function() {
         $('#te-request-id').val('');

         $('.te-amount-btn').on('click', function() {
             updateDisplay(parseFloat($(this).data('val')));
             // clear manual input when a quick amount button is used
             try { $('#te-custom-price').val(''); } catch(e){}
         });

         $('#te-custom-price').on('input', function() {
             updateDisplay(parseFloat($(this).val()) || 0);
         });

         $('#te-reset-btn').on('click', function() {
             updateDisplay(0);
             $('#te-custom-price').val('');
         });

         function updateDisplay(val) {
             $('#te-total-display').text(val.toFixed(2));
             $('#te-final-amount').val(val.toFixed(2));
         }

         window.processNFC = function(nfcTagId, opts) {
            if (window._nfcModalOpen || (typeof window.isNfcBlockedByModal === 'function' && window.isNfcBlockedByModal())) {
                try { console.log('Ignored NFC read - modal open'); } catch(e){}
                return;
            }

            nfcTagId = (nfcTagId || '').toString().trim();
            if (!nfcTagId || /^empty\s*tag$/i.test(nfcTagId) || /empty\s*tag/i.test(nfcTagId)) {
                try { console.warn('Ignored empty NFC tag'); } catch(e){}
                return;
            }

            var force = opts && opts.force;
             var amount = parseFloat($('#te-final-amount').val()) || 0;
             var now = Date.now();
             if (!force && window._nfcProcessLockedUntil && now < window._nfcProcessLockedUntil) {
                 try { console.log('Ignored NFC read - global cooldown'); } catch(e){}
                 return;
             }
             // If amount is zero, treat this as a balance-check request: still send AJAX to get wallet
             var isBalanceCheck = false;
             if (amount <= 0) {
                 isBalanceCheck = true;
             }

             // simple client-side lock & debounce to avoid duplicate processing when card is held
             window._processingNFC = window._processingNFC || false;
             window._lastNFC = window._lastNFC || { tag: null, ts: 0, requestId: null };
             var debounceMs = 3000;
             if (window._processingNFC && !force) { return; }
             if (!force && window._lastNFC.tag === nfcTagId && (now - window._lastNFC.ts) < debounceMs) { return; }

            if (!force) {
                window._processingNFC = true;
                window._lastNFC.tag = nfcTagId;
                window._lastNFC.ts = now;
                window._nfcProcessLockedUntil = now + 10000;
            }

             function showFinal(respObj) {
                 if (typeof window.showScanResult === 'function') {
                     window.showScanResult(respObj || {});
                 } else {
                     try { alert((respObj && respObj.message) ? respObj.message : 'Klaida'); } catch(e){}
                 }
             }

             if (isBalanceCheck) {
                 if (!force) {
                     $('.te-pos-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.6' });
                 }

                 $.ajax({
                     url: '/app.php',
                     method: 'POST',
                     data: { module: 'festivals', action: 'walletInfo', nfc_id: nfcTagId },
                     headers: { 'X-Requested-With': 'XMLHttpRequest' },
                     dataType: 'json'
                 }).done(function(resp){
                     if (resp && resp.success) {
                         try { window._lastWallet = parseFloat(resp.wallet); } catch(e){ window._lastWallet = null; }
                         showFinal({ success: true, message: '', topup: 0, wallet: resp.wallet });
                     } else {
                         showFinal(resp || { success: false, message: 'Klaida' });
                     }
                 }).fail(function(jqXHR, textStatus){
                     showFinal({ success:false, message: 'Tinklo klaida: ' + textStatus });
                 }).always(function(){
                     if (!force) {
                         $('.te-pos-wrapper').css({ 'pointer-events': '', 'opacity': '1' });
                         window._processingNFC = false;
                     }
                     window._lastNFC.ts = Date.now();
                 });
                 return;
             }

             // generate request id - prefer secure UUID when possible
             function generateRequestId(nfcTag) {
                 var idPart = null;
                 try {
                     if (window.crypto && typeof crypto.randomUUID === 'function') {
                         idPart = crypto.randomUUID();
                     } else if (window.crypto && crypto.getRandomValues) {
                         var ts = Date.now().toString(36);
                         var a = new Uint8Array(8);
                         crypto.getRandomValues(a);
                         var rand = Array.from(a).map(function(b){ return ('0' + b.toString(16)).slice(-2); }).join('');
                         idPart = ts + '-' + rand;
                     }
                 } catch(e) { idPart = null; }
                 if (!idPart) idPart = Date.now().toString(36) + '-' + Math.random().toString(36).slice(2,10);
                 return 'top-' + nfcTag + '-' + idPart;
             }
             // generate request id
             var requestId = generateRequestId(nfcTagId);
             $('#te-request-id').val(requestId);

             // visual lock (skip for forced test calls so clicking works)
             if (!force) {
                 $('.te-pos-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.6' });
             }

             // ensure global lastAjax container exists
             window._lastNfcAjax = window._lastNfcAjax || null;
             // If this is a forced test call, abort previous AJAX to make it immediate
             if (force && window._lastNfcAjax && typeof window._lastNfcAjax.abort === 'function') {
                 try { window._lastNfcAjax.abort(); } catch(e){}
                 window._lastNfcAjax = null;
             }
             // AJAX POST to server; server returns JSON
             var _jq = $.ajax({
                 url: '/app.php',
                 method: 'POST',
                 data: {
                     module: 'festivals',
                     action: 'cashmachineAct',
                     nfc_id: nfcTagId,
                     topup_amount: amount.toFixed(2),
                     request_id: requestId
                 },
                 headers: { 'X-Requested-With': 'XMLHttpRequest' },
                 dataType: 'json'
             }).done(function(resp){
                // clear stored lastAjax on success
                try { if (window._lastNfcAjax === _jq) window._lastNfcAjax = null; } catch(e){}
                 // If server returned an explicit 'empty cart' error, fetch wallet and then show modal
                 var msg = (resp && resp.message) ? resp.message.toString() : '';
                 var emptyCartPattern = /(empty cart|krepšelis|tuščias|tuščia)/i;
                 var isTestData = (msg.toString().toLowerCase().indexOf('test data') !== -1);
                 if (resp && resp.success === false && (emptyCartPattern.test(msg) || isTestData)) {
                     // fetch wallet info and then show modal with wallet
                     $.ajax({
                         url: '/app.php',
                         method: 'POST',
                         data: { module: 'festivals', action: 'walletInfo', nfc_id: nfcTagId },
                         headers: { 'X-Requested-With': 'XMLHttpRequest' },
                         dataType: 'json'
                     }).done(function(wr){
                         if (wr && wr.success) {
                             resp.wallet = wr.wallet;
                         }
                         if (resp && typeof resp.wallet !== 'undefined') {
                             try { window._lastWallet = parseFloat(resp.wallet); } catch(e){ window._lastWallet = null; }
                         }
                         if (typeof window.showScanResult === 'function') {
                             window.showScanResult(resp || {});
                         } else {
                             try { alert((resp && resp.message) ? resp.message : 'Klaida'); } catch(e){}
                         }
                     }).fail(function(){
                         // fallback: show original response
                         if (typeof window.showScanResult === 'function') {
                             window.showScanResult(resp || {});
                         } else {
                             try { alert((resp && resp.message) ? resp.message : 'Klaida'); } catch(e){}
                         }
                     }).always(function(){
                         // restore UI and processing state
                         if (!force) {
                             $('.te-pos-wrapper').css({ 'pointer-events': '', 'opacity': '1' });
                             window._processingNFC = false;
                         }
                         window._lastNFC.ts = Date.now();
                     });
                     return;
                 }

                 // update last wallet in memory if present
                 // determine if wallet looks valid; if not, fetch authoritative walletInfo
                 var parsedWallet = null;
                 if (resp && typeof resp.wallet !== 'undefined') {
                     parsedWallet = parseFloat(resp.wallet);
                     if (!isNaN(parsedWallet)) {
                         window._lastWallet = parsedWallet;
                     } else {
                         parsedWallet = null;
                     }
                 }

                 var needWalletFetch = false;
                 // If wallet is missing or zero when this was not explicitly a balance-check, fetch walletInfo
                 if (parsedWallet === null) {
                     needWalletFetch = true;
                 } else if (parsedWallet === 0 && !isBalanceCheck) {
                     // if server reports wallet 0 but we didn't request balance intentionally, double-check
                     needWalletFetch = true;
                 }

                  if (needWalletFetch) {
                     $.ajax({
                         url: '/app.php',
                         method: 'POST',
                         data: { module: 'festivals', action: 'walletInfo', nfc_id: nfcTagId },
                         headers: { 'X-Requested-With': 'XMLHttpRequest' },
                         dataType: 'json'
                     }).done(function(wr){
                         if (wr && wr.success) {
                             resp.wallet = wr.wallet;
                             try { window._lastWallet = parseFloat(wr.wallet); } catch(e){ window._lastWallet = null; }
                         }
                         showFinal(resp);
                     }).fail(function(){
                         showFinal(resp);
                     });
                 } else {
                     showFinal(resp);
                 }

                 if (resp && resp.success) {
                     // clear input on success
                     $('#te-custom-price').val('');
                     $('#te-total-display').text('0.00');
                     $('#te-final-amount').val('0.00');
                 }
             }).fail(function(jqXHR, textStatus){
                 var msg = 'Tinklo klaida: ' + textStatus;
                 window.showScanResult({ success:false, message: msg });
             }).always(function(){
                 // if we didn't early-return for empty-cart, restore UI state here
                 if (!force) {
                     $('.te-pos-wrapper').css({ 'pointer-events': '', 'opacity': '1' });
                     window._processingNFC = false;
                 }
                 window._lastNFC.ts = Date.now();
                try { if (window._lastNfcAjax === _jq) window._lastNfcAjax = null; } catch(e){}
             });
            // store jq for potential abort by forced calls
            try { window._lastNfcAjax = _jq; } catch(e){}
          };
        // Testing on localhost (bind always but only active if data-testing-card present)
         (function(){
             var el = document.getElementById('te-total-display');
             var testCard = el ? (el.getAttribute('data-testing-card') || '') : '';
             // robust dev detection: prefer server flag but fallback to client hostname (localhost or *.localhost)
             var devAttr = el ? (el.getAttribute('data-dev') || '') : '';
             var host = (typeof window !== 'undefined' && window.location && window.location.hostname) ? window.location.hostname.toLowerCase() : '';
             var isLocalDev = (devAttr === '1' || devAttr === 'true') || host === 'localhost' || host === '127.0.0.1' || (host && host.slice(-10) === '.localhost');
             // Only attach test handler when running on development host and a testing card is configured
             if (!testCard || !isLocalDev) return;
             $('#te-total-display').off('click.testcard').on('click.testcard', function(e) {
                 e && e.preventDefault && e.preventDefault();
                 e && e.stopPropagation && e.stopPropagation();
                 // clear client-side lock/state so repeated test clicks trigger immediately
                 try { window._processingNFC = false; } catch(e){}
                 try { window._lastNFC = { tag: null, ts: 0, requestId: null }; } catch(e){}
                 try { if (window._lastNfcAjax && typeof window._lastNfcAjax.abort === 'function') { window._lastNfcAjax.abort(); window._lastNfcAjax = null; } } catch(e){}
                 window.processNFC(testCard, {force:true});
                 return false;
             });
          })();
    	});
{/literal}
</script>

