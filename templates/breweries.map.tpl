<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
<script type="text/javascript">
    google.charts.load('current', {
        'packages':['geochart'],
        // Note: you will need to get a mapsApiKey for your project.
        // See: https://developers.google.com/chart/interactive/docs/basic_load_libs#load-settings
        'mapsApiKey': '{$config_google_api_key}'
    });
    google.charts.setOnLoadCallback(drawRegionsMap);

    function drawRegionsMap() {
        var data = google.visualization.arrayToDataTable([
            [{ label: 'Country', type: 'string' },
            { type: 'string', role: 'annotation' }],
            {foreach $list_breweries as $item}
                ['{$item.country_keyword|upper}', '{$item.country_title}'],
            {/foreach}
            ['', '']
]);

var options = {
};

var chart = new google.visualization.GeoChart(document.getElementById('regions_div'));

chart.draw(data, options);
}
</script>




<section class="inner-header divider parallax layer-overlay overlay-dark-5" data-stellar-background-ratio="0.5" data-bg-img="">
    <div class="container pt-90 pb-30">
        <div class="section-content pt-100">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-10 text-white">{"breweries map"|translate}</h2>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div id="regions_div" style="width: 100%;"></div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                {foreach $list_breweries as $item_brewery}
                    <div class="heading-line-bottom">
                        <h4 class="heading-title">{$item_brewery.country_title}</h4>
                    </div>

                    <div class="portfolio-gallery grid-6 masonry gutter-small clearfix" data-lightbox="gallery">
                        {foreach $item_brewery.list as $item}
                            <div class="portfolio-item">
                                <a href="{"/index.php?module=breweries&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}" title="{$item.title}"><img src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop400x400"|make_url}" alt=""></a>
                            </div>
                        {/foreach}
                    </div>
                {/foreach}
            </div>
        </div>
    </div>
</section>




