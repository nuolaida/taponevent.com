<div class="te-pos-wrapper">
	<div class="te-header">
		<div id="te-total-display">0.00</div>
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
			<input type="text" id="te-custom-price" placeholder="Įrašyti kainą" inputmode="decimal">
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
            return 'req-' + nfcId + '-' + Date.now() + '-' + Math.floor(Math.random() * 999999);
        }

        // Pagrindinė funkcija, kurią kviečia App
        window.processNFC = function(nfcTagId) {
            // Tikriname krepšelį
            if (cart.length === 0) return;

            // Sugeneruojame visiškai naują ID būtent šiam nuskaitymui
            var newId = generateUUID(nfcTagId);
            $('#te-request-id').val(newId);

            // Užpildome kitus laukus
            $('#te-cart-json').val(JSON.stringify(cart));
            $('#te-nfc-id').val(nfcTagId);

            var total = 0;
            $.each(cart, function(i, item) { total += item.price; });
            $('#te-total-sum').val(total.toFixed(2));

            // Apsauga nuo dvigubo paspaudimo JS lygyje
            $('.te-pos-wrapper').css('pointer-events', 'none').css('opacity', '0.6');

            // Siunčiame
            $('#te-hidden-form').submit();
        };
    });

    {if $testing_card}
        $('#te-total-display').on('click', function() { window.processNFC("{$testing_card}"); });
    {/if}

        $('#te-total-display').on('click', function() {
        console.log("Imituojamas NFC nuskaitymas...");
        window.processNFC("TEST-KORTELE-123456");
    });
</script>

