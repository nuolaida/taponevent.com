<!DOCTYPE HTML>
<html lang="en">
<head>
	{if $conf_google_analytics_id}
		<!-- Global site tag (gtag.js) - Google Analytics -->
		<script async src="https://www.googletagmanager.com/gtag/js?id={$conf_google_analytics_id}"></script>
		<script>
			window.dataLayer = window.dataLayer || [];
			function gtag(){ dataLayer.push(arguments); }
			gtag('js', new Date());

			gtag('config', '{$conf_google_analytics_id}');
		</script>
    {/if}
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,initial-scale=1"/>
	<meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
	<title></title>

	{foreach $page_styles as $item}
		<link href="{$item}" rel="stylesheet" type="text/css"/>
	{/foreach}

	<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
	<script>
        if (typeof jQuery == 'undefined') {
            document.write('<script src="/includes/jquery-3.7.1.min.js"><\/script>');
        }
	</script>

	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

	<script language="javascript" type="text/javascript">
        $(document).ready(function() {
            const $input = $('#price-input');
            const $button = $('#pay-button');

            $button.prop('disabled', true);

            $input.on('input', function() {
                let val = $(this).val().replace(',', '.');
                $(this).val(val);
                const isValid = val !== '' && !isNaN(val) && parseFloat(val) > 0;
                $button.prop('disabled', !isValid);
            });
        });

        // NFC & scan logic
        $(document).ready(function() {
            const url = window.location.href;

            if ('NDEFReader' in window) {
                // Jei esame darbo lange - skenuojame normaliai
                if (url.includes('action=work')) {
                    initNfcScanning();
                }
                    // VISUOSE KITUOSE puslapiuose (Ok, Error ir t.t.)
                // paleidžiame "tylųjį" skenavimą, kad Android sistema nesikištų
                else {
                    keepNfcBusyIfAllowed();
                }
            } else if (url.includes('action=work')) {
                showNfcEnablePrompt('NFC skenavimui reikia Android telefono su Chrome ir įjungtu NFC.');
            }
        });

        // Robust menu logic: single handler, debounce, aria, touch-friendly
        (function($){
            var menuProcessing = false;
            var debounceMs = 250;

            // Ensure menu is hidden initially
            $('#menu-dropdown').hide();
            $('#menu-icon').attr('role', 'button').attr('aria-expanded', 'false');

            // Use delegated document handler to be safe with dynamic content
            $(document).off('click.appMenu').on('click.appMenu', '#menu-icon', function(e){
                e.preventDefault();
                e.stopPropagation();

                if (menuProcessing) return;
                menuProcessing = true;
                setTimeout(function(){ menuProcessing = false; }, debounceMs);

                var $btn = $(this);
                var $menu = $('#menu-dropdown');

                // Toggle display and class for CSS
                var isOpen = $menu.is(':visible');
                if (isOpen) {
                    $menu.hide();
                    $btn.attr('aria-expanded', 'false');
                    $btn.closest('.menu').removeClass('open');
                } else {
                    // ensure other dropdowns (if any) are closed
                    $('.menu-dropdown').not($menu).hide();
                    $('.menu').not($btn.closest('.menu')).removeClass('open');

                    $menu.show();
                    $btn.attr('aria-expanded', 'true');
                    $btn.closest('.menu').addClass('open');
                }
            });

            // Close menu when clicking outside
            $(document).off('click.appMenuOutside').on('click.appMenuOutside', function(e){
                if (!$(e.target).closest('.menu').length) {
                    $('#menu-dropdown').hide();
                    $('#menu-icon').attr('aria-expanded', 'false');
                    $('.menu').removeClass('open');
                }
            });

            // Also handle Escape key to close
            $(document).off('keydown.appMenuEsc').on('keydown.appMenuEsc', function(e){
                if (e.key === 'Escape' || e.keyCode === 27) {
                    $('#menu-dropdown').hide();
                    $('#menu-icon').attr('aria-expanded', 'false');
                    $('.menu').removeClass('open');
                }
            });
        })(jQuery);

        // Global NFC scan controller + re-arm timer for repeated scans
        let nfcAbortController = null;
        let nfcAbortControllers = [];
        let nfcRearmTimer = null;
        let nfcScanActive = false;
        let nfcScanStarting = false;
        let nfcReader = null;
        let nfcLastRead = { tag: null, ts: 0 };
        let nfcReadLocked = false;
        let nfcPaused = false;
        const nfcDuplicateGapMs = 10000;

        function registerNfcAbortController(controller) {
            nfcAbortController = controller;
            nfcAbortControllers.push(controller);
            nfcAbortControllers = nfcAbortControllers.filter(function(item) {
                return item && !item.signal.aborted;
            });
        }

        function abortAllNfcReaders() {
            nfcAbortControllers.forEach(function(controller) {
                try { controller.abort(); } catch(e) {}
            });
            nfcAbortControllers = [];
            if (nfcAbortController) {
                try { nfcAbortController.abort(); } catch(e) {}
                nfcAbortController = null;
            }
            if (nfcReader) {
                try { nfcReader.onreading = null; } catch(e) {}
                nfcReader = null;
            }
        }

        function isScanResultOpen() {
            const modal = document.getElementById('scanResultModal');
            return !!(window._nfcHardPaused || window._nfcModalOpen || document.body.classList.contains('nfc-modal-open') || (modal && modal.classList && modal.classList.contains('open')));
        }

        async function getNfcPermissionState() {
            if (!navigator.permissions || !navigator.permissions.query) return null;

            try {
                const status = await navigator.permissions.query({ name: 'nfc' });
                return status && status.state ? status.state : null;
            } catch (e) {
                return null;
            }
        }

        function showNfcEnablePrompt(message) {
            let panel = document.getElementById('nfc-enable-panel');
            if (!panel) {
                panel = document.createElement('div');
                panel.id = 'nfc-enable-panel';
                panel.className = 'nfc-enable-panel';
                panel.innerHTML = '<div class="nfc-enable-text"></div><button type="button" class="nfc-enable-button">Įjungti NFC skenavimą</button>';
                document.body.appendChild(panel);

                const button = panel.querySelector('.nfc-enable-button');
                if (button) {
                    button.addEventListener('click', function() {
                        startNfcScanning({ userInitiated: true });
                    });
                }
            }

            const text = panel.querySelector('.nfc-enable-text');
            if (text) {
                text.textContent = message || 'NFC skenavimas išjungtas. Paspauskite „Įjungti“ ir patvirtinkite Chrome leidimą.';
            }

            panel.hidden = false;
        }

        function hideNfcEnablePrompt() {
            const panel = document.getElementById('nfc-enable-panel');
            if (panel) panel.hidden = true;
        }

        function setNfcEnabledFlag(isEnabled) {
            try {
                if (isEnabled) {
                    window.localStorage.setItem('tapNfcEnabled', '1');
                } else {
                    window.localStorage.removeItem('tapNfcEnabled');
                }
            } catch (e) {}
        }

        function wasNfcEnabledBefore() {
            try {
                return window.localStorage.getItem('tapNfcEnabled') === '1';
            } catch (e) {
                return false;
            }
        }

        async function initNfcScanning() {
            const state = await getNfcPermissionState();

            if (state === 'granted' || wasNfcEnabledBefore()) {
                startNfcScanning();
                return;
            }

            showNfcEnablePrompt('NFC skenavimas neįjungtas. Paspauskite „Įjungti“ ir patvirtinkite Chrome leidimą.');
        }

        async function startNfcScanning(options) {
            if (nfcPaused || isScanResultOpen()) return;
            if (nfcScanActive && nfcReader) return;
            if (nfcScanStarting) return;
            nfcScanStarting = true;
            options = options || {};

            registerNfcAbortController(new AbortController());

            try {
                const ndef = new NDEFReader();
                nfcReader = ndef;
                // Naudojame signalą, bet NE pridedame jokių papildomų skenavimų klaidų puslapiuose
                await ndef.scan({ signal: nfcAbortController.signal });
                nfcScanActive = true;
                setNfcEnabledFlag(true);
                hideNfcEnablePrompt();

                nfcReadLocked = false;

                ndef.onreading = event => {
                    if (isScanResultOpen()) {
                        try { console.log('Ignored NFC read while result modal is open'); } catch(e) {}
                        return;
                    }

                    const nfcId = (event.serialNumber || '').toString().trim();
                    const isEmptyTag = !nfcId || /^empty\s*tag$/i.test(nfcId) || /empty\s*tag/i.test(nfcId);

                    if (isEmptyTag) {
                        try { console.warn('Ignored empty NFC read'); } catch(e) {}
                    } else {
                        if (navigator.vibrate) navigator.vibrate(200);

                        if (typeof window.processNFC === 'function') {
                            window.processNFC(nfcId);
                        }
                    }

                    // Re-arm only after the result modal is closed or by an explicit restart.
                    if (nfcRearmTimer) clearTimeout(nfcRearmTimer);
                };

                ndef.onreadingerror = event => {
                    try { console.log('Ignored NFC reading error', event); } catch(e) {}
                };
            } catch (e) {
                nfcScanActive = false;

                if (!e || e.name !== 'AbortError') {
                    console.error(e);
                    const permissionState = await getNfcPermissionState();
                    if (permissionState === 'denied' || (e && e.name === 'NotAllowedError')) {
                        setNfcEnabledFlag(false);
                        showNfcEnablePrompt('NFC skenavimas išjungtas. Paspauskite „Įjungti“ ir Chrome lange suteikite leidimą.');
                    } else {
                        showNfcEnablePrompt('Nepavyko įjungti NFC skenavimo. Paspauskite „Įjungti“ ir bandykite dar kartą.');
                    }
                }
            } finally {
                nfcScanStarting = false;
            }
        }

        // Ši funkcija "pasisavina" NFC, kol vartotojas laiko kortelę pridėtą
        async function keepNfcBusy() {
            if (nfcPaused || isScanResultOpen()) return;
            if (nfcReader) {
                try { nfcReader.onreading = null; } catch(e) {}
                nfcReader = null;
            }
            if (nfcAbortController) nfcAbortController.abort();
            registerNfcAbortController(new AbortController());

            try {
                const ndef = new NDEFReader();
                nfcReader = ndef;
                // Skenuojame be signalo - tai laikys NFC užimtą visą laiką, kol matomas šis puslapis
                await ndef.scan({ signal: nfcAbortController.signal });
                ndef.onreading = () => { /* Ignoruojam */ };
            } catch (e) { }
        }

        // Funkcija, kurią gali iškviesti kiti šablonai
        async function keepNfcBusyIfAllowed() {
            const state = await getNfcPermissionState();
            if (state === 'granted') {
                keepNfcBusy();
            }
        }

        function stopNfcScanning() {
            nfcPaused = true;
            if (nfcRearmTimer) {
                clearTimeout(nfcRearmTimer);
                nfcRearmTimer = null;
            }
            nfcReadLocked = true;
        }

        window.stopNfcScanning = stopNfcScanning;

        function resumeNfcScanning() {
            nfcPaused = false;
            nfcReadLocked = false;
            window._nfcHardPaused = false;
        }

        window.resumeNfcScanning = resumeNfcScanning;

        function restartNfcScanning() {
            const url = window.location.href;
            if (isScanResultOpen()) return;
            resumeNfcScanning();
            if (nfcScanActive && nfcReader) return;
            if ('NDEFReader' in window && url.includes('action=work')) {
                startNfcScanning();
            }
        }

        window.restartNfcScanning = restartNfcScanning;
    </script>
</head>
<body>
	<div class="center-wrapper">
		{if $page_messages}
			<div class="messages messages-error">
				<ul>
	            {foreach $page_messages as $item}
					<li>{$item}</li>
	            {/foreach}
				</ul>
			</div>
		{/if}

		<div class="page-center">
	        {$module_html}
		</div>
	</div>

	<div class="footer-bar">
		<div></div>
		<div class="logo">
			<div class="logo-mark" aria-hidden="true"></div>
		</div>
		<div class="menu">
			<span class="material-icons" id="menu-icon">menu</span>
			<ul class="menu-dropdown" id="menu-dropdown">
				<li><a href="?module=festivals&action=work">{"cash machine"|translate}</a></li>
				<li><a href="?module=festivals&action=checkoutList">{"checkout list"|translate}</a></li>
				<li><a href="?module=festivals&action=select">{"festivals"|translate}</a></li>
				{if $user_info}
					<li><a href="?module=users&action=logoutAct">{"logout"|translate}</a></li>
				{else}
					<li><a href="?module=users&action=login">{"login"|translate}</a></li>
				{/if}
			</ul>
		</div>
	</div>

	<!-- Modal for scan result -->
	<div id="scanResultModal" class="custom-modal" tabindex="-1" role="dialog" aria-hidden="true">
	  <div class="custom-modal-overlay" data-dismiss="modal"></div>
	  <div class="custom-modal-dialog" role="document" aria-modal="true">
	    <div class="custom-modal-content">
	      <div class="custom-modal-header">
	        <button type="button" class="custom-modal-close" data-dismiss="modal" aria-label="Close">×</button>
	        <!-- title intentionally removed as requested -->
	      </div>
	      <div class="custom-modal-body">
	        <div id="scanResultBody">...</div>
	      </div>
	      <div class="custom-modal-footer">
	        <button type="button" class="custom-modal-ok" data-dismiss="modal">Uždaryti</button>
	      </div>
	    </div>
	  </div>
	</div>

	<style>
	/* Minimal, self-contained modal styles so it works without Bootstrap */
	#scanResultModal.custom-modal { display: none; position: fixed; inset: 0; z-index: 3000; align-items: center; justify-content: center; }
	#scanResultModal.custom-modal.open { display: flex; }
	#scanResultModal .custom-modal-overlay { position: absolute; inset: 0; background: rgba(0,0,0,0.45); }
	#scanResultModal .custom-modal-dialog { position: relative; max-width: 420px; width: 92%; margin: 16px; z-index: 2; }
	#scanResultModal .custom-modal-content { border-radius: 8px; overflow: hidden; box-shadow: 0 8px 24px rgba(0,0,0,0.35); font-family: inherit; }
	#scanResultModal .custom-modal-header { padding: 8px 12px; display:flex; align-items:center; justify-content:flex-end; }
	#scanResultModal .custom-modal-close { background:transparent; border:0; font-size:1.4rem; line-height:1; cursor:pointer; color:#fff; }
	#scanResultModal .custom-modal-body { padding: 14px; background: #fff; color: #222; text-align:center; }
	#scanResultModal .custom-modal-footer { padding: 10px 14px; text-align:center; background:#fafafa; }
	#scanResultModal .custom-modal-ok { padding:8px 16px; border-radius:4px; border:0; cursor:pointer; }
	/* success / error variants (header color only)
	   body stays white for readable large amounts */
	#scanResultModal .custom-modal-content.ok .custom-modal-header { background: #2ecc71; }
	#scanResultModal .custom-modal-content.ok .custom-modal-ok { background: #27ae60; color:#fff; }
	#scanResultModal .custom-modal-content.error .custom-modal-header { background: #e74c3c; }
	#scanResultModal .custom-modal-content.error .custom-modal-ok { background: #c0392b; color: #fff; }
	#scanResultModal .custom-modal-content.error .custom-modal-body { background: #fff; color: #222; }
	/* Amount styling */
	#scanResultModal .label-small { display:block; font-size:0.95rem; color:#333; margin-top:8px; font-weight:400; }
	#scanResultModal .amount-large { font-size:3.6rem; font-weight:700; color:#222; margin-top:6px; }
	/* balance number - make noticeably larger */
	#scanResultModal .amount-medium { font-size:3.9rem; font-weight:700; color:#222; margin-top:6px; }
	/* topup/added amount (shown after balance) - 2x label size */
	#scanResultModal .amount-added { font-size:2.0rem; font-weight:700; color:#222; margin-top:6px; }
	#scanResultModal .meta { color:#666; font-size:0.95rem; margin-top:6px; }
	</style>

	<script>
	(function(){
		// track previously focused element so we can restore focus when modal closes
		var _inertedElems = [];
		var _previouslyFocused = null;
		window.isNfcBlockedByModal = function() {
			var m = document.getElementById('scanResultModal');
			return !!(window._nfcModalOpen || (m && m.classList && m.classList.contains('open')));
		};

		function closeModal() {
			var m = document.getElementById('scanResultModal');
			if (!m) return;
			window._nfcHardPaused = false;
			window._nfcModalOpen = false;
			document.body.classList.remove('nfc-modal-open');
			m.classList.remove('open');
			var content = m.querySelector('.custom-modal-content');
			if (content) { content.classList.remove('ok'); content.classList.remove('error'); }
			// restore previous focus if available before hiding from AT
			try { if (_previouslyFocused && typeof _previouslyFocused.focus === 'function') _previouslyFocused.focus(); else document.body.focus(); } catch(e){}
			// If a descendant still has focus, blur it first so it's not hidden from AT while focused
			try {
				var active = document.activeElement;
				if (active && m.contains(active)) {
					try { active.blur(); } catch(e){}
				}
			} catch(e){}
			// remove inert from previously inerted elements
			try {
				if (Array.isArray(_inertedElems)) {
					_inertedElems.forEach(function(el){ try { if (el && el.removeAttribute) el.removeAttribute('inert'); } catch(e){} });
				}
				_inertedElems = [];
			} catch(e){}
			// mark modal as hidden for assistive tech (after focus moved/blur)
			try { m.setAttribute('aria-hidden','true'); } catch(e){}
			document.body.classList.remove('custom-modal-open');
			try { window._nfcProcessLockedUntil = 0; } catch(e){}
			try { window._processingNFC = false; } catch(e){}
			try { window._lastNFC = { tag: null, ts: 0, requestId: null }; } catch(e){}
			if (typeof window.resumeNfcScanning === 'function') {
				window.resumeNfcScanning();
			}
			if (typeof window.restartNfcScanning === 'function') {
				setTimeout(function(){ window.restartNfcScanning(); }, 700);
			}
		}

		function bindModalHandlers() {
			var m = document.getElementById('scanResultModal');
			if (!m) return;
			m.querySelectorAll('[data-dismiss="modal"]').forEach(function(el){
				el.addEventListener('click', function(e){ e.preventDefault(); closeModal(); });
			});
			// overlay click closes
			var overlay = m.querySelector('.custom-modal-overlay');
			if (overlay) overlay.addEventListener('click', function(){ closeModal(); });
			// ESC closes
			document.addEventListener('keydown', function(e){ if (e.key === 'Escape') closeModal(); });
		}

		function makeCurrency(val) { var n = parseFloat(val); if (isNaN(n)) return ''; return n.toFixed(2) + ' €'; }

		window.showScanResult = function(data){
			try {
				window._nfcHardPaused = true;
				window._nfcModalOpen = true;
				document.body.classList.add('nfc-modal-open');
				if (typeof window.stopNfcScanning === 'function') {
					window.stopNfcScanning();
				}
				var m = document.getElementById('scanResultModal');
				var body = document.getElementById('scanResultBody');
				if (!m || !body) { alert(data.message || (data.success? 'OK' : 'Error')); return; }

				// normalize data types
				var success = !!data.success;
				var message = data.message || '';
				var wallet = null;
				if (typeof data.wallet !== 'undefined') {
					var w = parseFloat(data.wallet);
					if (!isNaN(w)) wallet = w;
				}
				// Client-side fallback: try to read known DOM elements if wallet missing/invalid
				if (wallet === null) {
					var fallbackIds = ['te-wallet','te-wallet-display','wallet-display','te-total-display','te-total-sum'];
					for (var fi = 0; fi < fallbackIds.length; fi++) {
						var el = document.getElementById(fallbackIds[fi]);
						if (!el) continue;
						var txt = '';
						if (el.value !== undefined && el.value !== null && el.value !== '') txt = el.value;
						else txt = (el.textContent || el.innerText || '').toString();
						txt = txt.replace(',', '.').replace(/[^0-9\.\-]/g, '').trim();
						if (!txt) continue;
						var parsed = parseFloat(txt);
						if (!isNaN(parsed)) { wallet = parsed; break; }
					}
				}
				var checkout = (typeof data.checkout !== 'undefined') ? parseFloat(data.checkout) : null;
				var topup = (typeof data.topup !== 'undefined') ? parseFloat(data.topup) : null;
				// items intentionally ignored per request

				// build html
				var html = '';
				if (success) {
					html += '<div>';
					// small label + very large scanned amount (3x)
					if (checkout !== null) {
						html += '<span class="label-small">Nuskaityta</span>';
						html += '<div class="amount-large">' + makeCurrency(checkout) + '</div>';
					}
					// wallet label + larger balance amount
					if (wallet !== null) {
						html += '<span class="label-small">Balansas</span>';
						html += '<div class="amount-medium">' + makeCurrency(wallet) + '</div>';
					}
					// if there was a topup amount from server, show it after balance
					if (typeof topup !== 'undefined' && topup !== null && !isNaN(topup) && topup > 0) {
						html += '<span class="label-small">Pridėta</span>';
						html += '<div class="amount-added">' + makeCurrency(topup) + '</div>';
					}
					// show server message only if it's not 'OK' (case-insensitive) and not empty
					if (message && message.toString().trim() !== '' && message.toString().toLowerCase() !== 'ok') html += '<div class="meta">' + message + '</div>';
					html += '</div>';
				} else {
					html += '<div>';
					// error title inside body
					html += '<div style="font-size:1.1rem;font-weight:600;margin-bottom:6px;">' + (data.duplicate ? 'DUPLIKATAS' : 'Klaida') + '</div>';
					if (message) html += '<div class="meta" style="margin-bottom:8px">' + message + '</div>';
					if (checkout !== null) {
						html += '<span class="label-small">Reikalinga</span>';
						html += '<div class="amount-large">' + makeCurrency(checkout) + '</div>';
					}
					if (wallet !== null) {
						html += '<span class="label-small">Balansas</span>';
						html += '<div class="amount-medium">' + makeCurrency(wallet) + '</div>';
					}
					if (typeof data.shortfall !== 'undefined') html += '<div class="meta"><strong>Trūksta:</strong> ' + makeCurrency(data.shortfall) + '</div>';
					html += '</div>';
				}

				body.innerHTML = html;
				var content = m.querySelector('.custom-modal-content');
				if (content) { content.classList.remove('ok'); content.classList.remove('error'); content.classList.add(success ? 'ok' : 'error'); }
				// explicitly mark modal visible to assistive tech
				try { m.setAttribute('aria-hidden', 'false'); } catch(e) {}
				// remember previous focused element to restore later
				try { _previouslyFocused = document.activeElement; } catch(e){ _previouslyFocused = null; }
				// set inert on main content regions so AT won't focus/hide them while modal is open (if supported)
				try {
					_inertedElems = [];
					var c = document.querySelector('.center-wrapper'); if (c && c.setAttribute) { c.setAttribute('inert',''); _inertedElems.push(c); }
					var f = document.querySelector('.footer-bar'); if (f && f.setAttribute) { f.setAttribute('inert',''); _inertedElems.push(f); }
				} catch(e){}
				m.classList.add('open');
				document.body.classList.add('custom-modal-open');
				// focus the primary action button inside modal on next animation frame so accessibility tree updates
				try {
					var okBtn = m.querySelector('.custom-modal-ok');
					if (okBtn) {
						// ensure focusable
						okBtn.setAttribute('tabindex', '0');
						requestAnimationFrame(function(){ try{ okBtn.focus(); } catch(e){} });
					}
				} catch(e) {}

			} catch (e) {
				console.error('showScanResult error', e);
			}
		};

		// bind once
		bindModalHandlers();

		function installNfcModalGuard() {
			if (typeof window.processNFC !== 'function' || window.processNFC._modalGuardInstalled) return;
			var originalProcessNFC = window.processNFC;
			window.processNFC = function() {
				if (typeof window.isNfcBlockedByModal === 'function' && window.isNfcBlockedByModal()) {
					try { console.log('Blocked NFC because result modal is open'); } catch(e){}
					return;
				}
				return originalProcessNFC.apply(this, arguments);
			};
			window.processNFC._modalGuardInstalled = true;
		}

		installNfcModalGuard();
		setTimeout(installNfcModalGuard, 0);
		setTimeout(installNfcModalGuard, 500);
	})();
	</script>

</body>
</html>
