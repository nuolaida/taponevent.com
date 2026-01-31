<div class="te-pos-wrapper te-topup-theme">
	<div class="te-topup-header">
		<span class="te-label">PAPILDYMO SUMA</span>
		<div class="te-display-row">
			<span id="te-total-display">0.00</span>
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
			<input type="number" id="te-custom-price" placeholder="Kita suma..." inputmode="decimal">
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
    $(document).ready(function() {
        $('#te-request-id').val('');

        $('.te-amount-btn').on('click', function() {
            updateDisplay(parseFloat($(this).data('val')));
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

        window.processNFC = function(nfcTagId) {
            var amount = parseFloat($('#te-final-amount').val()) || 0;
            if (amount <= 0) return alert("Įveskite sumą!");

            $('#te-nfc-id').val(nfcTagId);
            $('#te-request-id').val('top-' + nfcTagId + '-' + Date.now());
            $('.te-pos-wrapper').css({ 'pointer-events': 'none', 'opacity': '0.6' });
            $('#te-hidden-form').submit();
        };

        // Testing on localhost
	    {if $testing_card}
            $('#te-total-display').on('click', function() { window.processNFC("{$testing_card}"); });
        {/if}
    });
</script>

