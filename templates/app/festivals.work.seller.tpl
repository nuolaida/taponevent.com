<div class="te-pos-wrapper">
	<div class="te-header">
		<div id="te-total-display" data-testing-card="{$testing_card|default:''}" data-dev="{$is_development_version|default:0}">0.00</div>
	</div>

	<div class="te-container">
		<div class="te-button-grid">
            {foreach $list_prices as $item}
				<button class="te-product-btn"
				        data-id="{$item.id}"
				        data-name="{$item.title}"
				        data-price="{$item.price}">
					<span class="te-btn-title">{$item.title}</span>
					<span class="te-btn-price">{$item.price|string_format:"%.2f"}</span>
				</button>
            {/foreach}
		</div>

		<div class="te-manual-entry">
			<input type="text" id="te-custom-price" aria-label="Įrašyti kainą" placeholder="Įrašyti kainą" inputmode="decimal">
			<button type="button" id="te-add-custom">PRIDĖTI</button>
		</div>

		<div class="te-cart-card">
			<ul id="te-cart-list">
			</ul>
		</div>
	</div>

	<form id="te-hidden-form" action="app.php" method="POST" style="display:none;" autocomplete="off">
		<input type="hidden" name="module" value="festivals">
		<input type="hidden" name="action" value="checkout">
		<input type="hidden" name="cart_json" id="te-cart-json">
		<input type="hidden" name="nfc_id" id="te-nfc-id">
		<input type="hidden" name="total_sum" id="te-total-sum">
		<input type="hidden" name="request_id" id="te-request-id">
	</form>
</div>

<script>
{literal}
    $(document).ready(function() {
        var cart = [];
        var $wrapper = $('.te-pos-wrapper');
        $('#te-request-id').val('');

        // Pridėjimas paspaudus ant prekės
        $wrapper.on('click', '.te-product-btn', function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            var price = parseFloat($(this).data('price'));
            addToCart(id, name, price);
        });

        // Pridėjimas įvedus ranka
        $wrapper.on('click', '#te-add-custom', function() {
            var $input = $('#te-custom-price');
            var val = $input.val().replace(',', '.');
            var price = parseFloat(val);

            if (!isNaN(price) && price > 0) {
                addToCart(0, 'Laisva kaina', price);
                $input.val('');
            }
        });

        // Pašalinimas iš sąrašo apačioje
        $wrapper.on('click', '.te-remove-item', function() {
            var index = $(this).closest('li').index();
            cart.splice(index, 1);
            renderCart();
        });

        function addToCart(id, name, price) {
            cart.push({
                item_id: id,
                name: name,
                price: price,
                uid: Date.now()
            });
            renderCart();
        }

        function renderCart() {
            var $list = $('#te-cart-list');
            $list.empty();
            var total = 0;

            $.each(cart, function(index, item) {
                total += item.price;
                var row = '<li class="te-cart-item">' +
                    '<span>' + item.name + ' <strong>' + item.price.toFixed(2) + ' €</strong></span>' +
                    '<span class="te-remove-item">&times;</span>' +
                    '</li>';
                $list.append(row);
            });

            $('#te-total-display').text(total.toFixed(2));
        }

        function generateUUID(nfcId) {
            // Prefer native crypto.randomUUID() when available (modern browsers)
            try {
                if (window.crypto && typeof window.crypto.randomUUID === 'function') {
                    return 'req-' + nfcId + '-' + Date.now() + '-' + window.crypto.randomUUID();
                }
            } catch (e) { /* fallthrough to fallback */ }

            // Fallback: generate RFC4122 v4 UUID using crypto.getRandomValues
            function uuidv4() {
                if (window.crypto && window.crypto.getRandomValues) {
                    const bytes = new Uint8Array(16);
                    window.crypto.getRandomValues(bytes);
                    // Per RFC4122 v4
                    bytes[6] = (bytes[6] & 0x0f) | 0x40;
                    bytes[8] = (bytes[8] & 0x3f) | 0x80;
                    const hex = Array.from(bytes).map(b => ('0' + b.toString(16)).slice(-2)).join('');
                    return [hex.substr(0,8), hex.substr(8,4), hex.substr(12,4), hex.substr(16,4), hex.substr(20,12)].join('-');
                }
                // Last resort: Math.random (very unlikely on modern devices, but fallback)
                return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                    var r = Math.random() * 16 | 0;
                    var v = c === 'x' ? r : (r & 0x3 | 0x8);
                    return v.toString(16);
                });
             }

             return 'req-' + nfcId + '-' + Date.now() + '-' + uuidv4();
         }

        // global small lock to prevent double-processing when card is held
        window._processingNFC = window._processingNFC || false;
        window._lastNFC = window._lastNFC || { tag: null, ts: 0, requestId: null };

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

            var now = Date.now();
            var force = opts && opts.force;
            if (!force && window._nfcProcessLockedUntil && now < window._nfcProcessLockedUntil) {
                try { console.log('Ignored NFC read - global cooldown'); } catch(e){}
                return;
            }

            try {
                // debug log
                try { console.log('processNFC start', { tag: nfcTagId, force: !!force, cartCount: cart.length }); } catch(e){}

             // If another processing is ongoing, ignore unless forced
             if (window._processingNFC && !force) {
                 console.log('Ignored NFC read - already processing');
                 return;
             }

            // Debounce rapid repeated reads for the same tag (3 seconds) unless forced
            var debounceMs = 3000;
            if (!force && window._lastNFC.tag === nfcTagId && (now - window._lastNFC.ts) < debounceMs) {
                console.log('Ignored NFC rapid repeat for tag', nfcTagId);
                return;
            }

            // Mark as processing and remember last tag/timestamp
            if (!force) {
                window._processingNFC = true;
            } else {
                // when forced, reset processing lock so test triggers immediately
                window._processingNFC = false;
            }
            window._lastNFC.tag = nfcTagId;
            window._lastNFC.ts = now;
            if (!force) {
                window._nfcProcessLockedUntil = now + 10000;
            }

            // Reuse the previous requestId for this tag session if present; otherwise generate a new one
            var newId = window._lastNFC.requestId || generateUUID(nfcTagId);
            window._lastNFC.requestId = newId;

            // Paruošiame duomenis. NFC readerio neabortiname, kad Android neperimtų tuščios žymos.
            $('#te-request-id').val(newId);
            $('#te-cart-json').val(JSON.stringify(cart));
            $('#te-nfc-id').val(nfcTagId);

            var total = 0;
            $.each(cart, function(i, item) { total += item.price; });
            $('#te-total-sum').val(total.toFixed(2));

            $('.te-pos-wrapper').css('pointer-events', 'none').css('opacity', '0.6');

            // Use AJAX POST and expect JSON response; server side returns JSON for XHR
            // Guard: if no nfcTagId present, release locks and inform user
            if (!nfcTagId) {
                try { console.warn('processNFC called without nfcTagId'); } catch(e){}
                try { window.showScanResult({ success:false, message: 'Kortelės ID nerastas' }); } catch(e){}
                $('.te-pos-wrapper').css('pointer-events', '').css('opacity', '1');
                window._processingNFC = false;
                window._lastNFC.ts = Date.now();
                return;
            }

            setTimeout(function() {
                // debug: show payload
                try { console.debug('AJAX payload', { module:'festivals', action:'checkout', cart_json: JSON.stringify(cart), nfc_id: nfcTagId, total_sum: total.toFixed(2), request_id: newId }); } catch(e){}
                $.ajax({
                     url: '/app.php',
                     method: 'POST',
                     data: {
                         module: 'festivals',
                         action: 'checkout',
                         cart_json: JSON.stringify(cart),
                         nfc_id: nfcTagId,
                         total_sum: total.toFixed(2),
                         request_id: newId
                     },
                     headers: { 'X-Requested-With': 'XMLHttpRequest' },
                     dataType: 'json'
                }).done(function(resp){
                    // debug log full response
                    console.log('Checkout response:', resp);

                    // helper to final-handle response (may be augmented with wallet)
                    function finalHandle(finalResp) {
                        window.showScanResult(finalResp || {});
                        // Update on-page wallet display if present
                        if (finalResp && typeof finalResp.wallet !== 'undefined' && finalResp.wallet !== null) {
                            try { window._lastWallet = parseFloat(finalResp.wallet); } catch(e){ window._lastWallet = null; }
                        }
                        if (finalResp && finalResp.success) {
                             cart = [];
                             renderCart();
                             // clear requestId on success so next hold will generate a new request
                             window._lastNFC.requestId = null;
                         } else {
                            // if response doesn't contain expected info, show raw for debugging
                            if (finalResp && (!finalResp.items && typeof finalResp.wallet === 'undefined' && typeof finalResp.checkout === 'undefined')) {
                                window.showScanResult({ success:false, message: 'Netikėtas atsakymas: ' + JSON.stringify(finalResp), request_id: finalResp.request_id || null });
                            }
                            // if duplicate, don't clear requestId immediately so server-side duplicate is recognized
                            if (finalResp && finalResp.duplicate) {
                                // keep requestId for short period to avoid retrying
                                setTimeout(function(){ window._lastNFC.requestId = null; }, 2000);
                            } else {
                                // for other errors, allow immediate new request
                                window._lastNFC.requestId = null;
                            }
                         }
                     }

                    // Detect empty-cart responses in common languages or explicit checkout===0
                    var msg = (resp && resp.message) ? resp.message.toString() : '';
                    var emptyCartPattern = /(empty cart|krepšelis|tuščias|tuščia)/i;
                    var isTestData = (msg.toString().toLowerCase().indexOf('test data') !== -1);
                    var isEmptyCart = !!(resp && (resp.checkout === 0 || emptyCartPattern.test(msg) || isTestData));
                    if (isEmptyCart) {
                        // If server already provided wallet in the response, use it. Otherwise fetch walletInfo.
                        if (resp && typeof resp.wallet !== 'undefined') {
                            finalHandle(resp);
                        } else {
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
                                finalHandle(resp);
                            }).fail(function(){
                                finalHandle(resp);
                            });
                        }
                    } else {
                        finalHandle(resp);
                    }

                }).fail(function(jqXHR, textStatus){
                    var msg = 'Tinklo klaida: ' + textStatus;
                    window.showScanResult({ success: false, message: msg });
                }).always(function(){
                    $('.te-pos-wrapper').css('pointer-events', '').css('opacity', '1');
                    // release processing lock after AJAX completes
                    window._processingNFC = false;
                    // update timestamp so subsequent quick reads are still debounced
                    window._lastNFC.ts = Date.now();
                });
            }, 150);
            } catch (e) {
                // Ensure we always release locks and restore UI on unexpected JS error
                console.error('processNFC unexpected error', e);
                try { $('.te-pos-wrapper').css('pointer-events', '').css('opacity', '1'); } catch(_e){}
                window._processingNFC = false;
                window._lastNFC.ts = Date.now();
                try { window.showScanResult({ success:false, message: 'JS klaida: ' + (e && e.message ? e.message : String(e)) }); } catch(_e){}
            }
        };

        // Attach test handler only on development hosts and when a testing card is configured
        (function(){
            var el = document.getElementById('te-total-display');
            var testCard = el ? (el.getAttribute('data-testing-card') || '') : '';
            // robust dev detection: prefer server flag but fallback to client hostname (localhost or *.localhost)
            var devAttr = el ? (el.getAttribute('data-dev') || '') : '';
            var host = (typeof window !== 'undefined' && window.location && window.location.hostname) ? window.location.hostname.toLowerCase() : '';
            var isLocalDev = (devAttr === '1' || devAttr === 'true') || host === 'localhost' || host === '127.0.0.1' || (host && host.slice(-10) === '.localhost');
            if (!testCard || !isLocalDev) return;
             $('#te-total-display').off('click.testcard').on('click.testcard', function(e){
                 e && e.preventDefault && e.preventDefault();
                 e && e.stopPropagation && e.stopPropagation();
                 try { window._processingNFC = false; } catch(e){}
                 try { window._lastNFC = { tag: null, ts: 0, requestId: null }; } catch(e){}
                 try { if (window._lastNfcAjax && typeof window._lastNfcAjax.abort === 'function') { window._lastNfcAjax.abort(); window._lastNfcAjax = null; } } catch(e){}
                 // Force the test read so it bypasses client-side locks/debounce
                 window.processNFC(testCard, {force:true});
                 return false;
             });
         })();
        // Accept NFC forwarded from native app / webview via postMessage
        function handleExternalNfc(tagId) {
            try {
                if (window._nfcModalOpen) return;
                if (!tagId) return;
                console.log('External NFC received', tagId);
                if (typeof window.processNFC === 'function') {
                    window.processNFC(tagId);
                }
            } catch (e) { console.error('handleExternalNfc error', e); }
        }

        window.addEventListener('message', function(ev){
            try {
                var d = ev.data;
                if (typeof d === 'string') {
                    try { d = JSON.parse(d); } catch(e) {}
                }
                if (!d) return;
                // Support {type:'nfc', id:'...'} or {nfc_id:'...'} or plain string
                if (d.type === 'nfc' && d.id) { handleExternalNfc(d.id); return; }
                if (d.nfc_id) { handleExternalNfc(d.nfc_id); return; }
                if (typeof d === 'string' && d.length > 4) { handleExternalNfc(d); return; }
            } catch(e){ console.error('postMessage handler error', e); }
        }, false);

        // Support custom DOM event: document.dispatchEvent(new CustomEvent('nfc-read', {detail:{id:'TAGID'}}))
        document.addEventListener('nfc-read', function(ev){
            try { if (ev && ev.detail && ev.detail.id) handleExternalNfc(ev.detail.id); } catch(e){}
        });
     });
{/literal}
</script>
