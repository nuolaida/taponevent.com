

<!-- Section: Upcoming Events -->
<section>
    <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="http://placehold.it/1920x1280">
        <div class="container pt-50 pb-50">
            <div class="section-title">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <br />
                        <h2 class="title text-white mb-0">{"festivals"|translate}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="divider">
        <div class="container pb-50 pt-80">
            <div class="section-content">
                <div class="row">
                    {foreach $list as $item}
                        {assign var="link" value="/index.php?module=catalogue&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}
                        <div class="col-sm-6 col-md-4 col-lg-4">
                            <div class="schedule-box maxwidth500 mb-30 bg-lighter">
                                <div class="thumb">
                                    <img class="img-fullwidth" alt="{$item.title}" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop380x254"|make_url}">
                                    <div class="overlay">
                                        <a href="{$link}"><i class="fa fa-calendar mr-5"></i></a>
                                    </div>
                                </div>
                                <div class="schedule-details clearfix p-15 pt-10">
                                    <h5 class="font-16 title"><a href="{$link}">{$item.title}</a></h5>
                                    <ul class="list-inline mb-5">
                                        {if !$item.event_finished}
                                            <li>
                                                <i class="fa fa-calendar mr-5"></i>
                                                {$item.last_date_start|my_date_format:"short"}
                                                {if $item.last_date_end}
                                                    / {$item.last_date_end|my_date_format:"day"}
                                                {/if}
                                            </li>
                                        {else}
                                            <li></li>
                                        {/if}
                                    </ul>
                                    <ul class="list-inline mb-10">
                                        {if $item.city_id}
                                            <li>
                                                <i class="fa fa-map-marker mr-5"></i>
                                                {$item.city_title}, {$item.country_title}
                                            </li>
                                        {else}
                                            <li></li>
                                        {/if}
                                    </ul>
                                    <ul class="list-inline mb-20">
                                        {if $item.drink_type}
                                            <li>
                                                <i class="fa fa-glass mr-5"></i>
                                                {$list_drink_types[$item.drink_type]}
                                            </li>
                                        {else}
                                            <li></li>
                                        {/if}
                                    </ul>
                                    <div class="mt-10">
                                        <a class="btn btn-colored btn-theme-colored btn-sm" href="{$link}">{"details"|translate}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
</section>




