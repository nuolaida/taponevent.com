<html>
<head>
	<title></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<style>
        .labels_block {
            display: flex;
        }
        .label {
            border: 2px solid #000;
            border-right: none;
            width: 60mm;
            min-height: 60mm;
            padding: 10mm;
        }
        .label:last-child {
	        border-right: 2px solid #000;
        }
        .label > div {
	        margin-bottom: 3mm;
        }
        .label .id {
	        font-weight: bold;
	        font-size: 2em;
        }
	</style>
</head>

<body style="font-family: verdana; background-color: #ffffff; font-size: 13px; color: #000000; margin: 10px 10px 10px 10px;">
<div class="labels_block">
	{for $i=1 to 2}
		<div class="label">
            <div class="id">{$data_item.gen_id}</div>
			<div class="category">{$data_item.category}</div>
			<div class="sweetness">{if $data_item.sweetness}{"competition sweetness `$data_item.sweetness`"|translate}{else}???{/if}</div>
			<div class="carbonation">{if $data_item.carbonation}{"competition carbonation `$data_item.carbonation`"|translate}{else}???{/if}</div>
			<div class="ingredients">{$data_item.ingredients}</div>
			<div class="abv">{if $data_item.abv}{$data_item.abv}{else}?.?{/if}%</div>
		</div>
	{/for}
</div>
</body>
</html>

