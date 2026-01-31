<section>
    <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="http://placehold.it/1920x1280">
        <div class="container pt-50 pb-50">
            <div class="section-title">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <br />
                        <h2 class="title text-white mb-0">{$data.title}</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section>
    <div class="container">
        <div class="section-content">
            <div class="row">
                <div class="col-md-4">
                    <div class="thumb">
                        <img src="{"/index.php?module=images&action=show&id=`$data.gallery_id`&type=crop400x400"|make_url}" alt="{$data.title}" />
                    </div>
                </div>
                <div class="col-md-8">
                    <h3 class="name font-28 text-gray mb-0 mt-0">
                        <a href="{"/index.php?module=catalogue&action=festivals"|make_url}">{"festivals"|translate}</a>
                        {if $data.drink_type}
                            / {$list_drink_types[$data.drink_type]}
                        {/if}
                    </h3>
                    <h1 class="text-uppercase text-gray-darkgray font-48 mt-0">
                        {$data.title}
                    </h1>
                    <h4>
                        {if !$data.event_finished}
                            <i class="fa fa-calendar mr-5"></i>
                            {$data.last_date_start|my_date_format:"short"}
                            {if $data.last_date_end}
                                / {$data.last_date_end|my_date_format:"short"}
                            {/if}
                        {/if}
                        <br />
                        <i class="fa fa-map-marker mr-5"></i>
                        {$data.city_title}, {$data.country_title}
                    </h4>
                    {if $data.description}
                        <p>{$data.description}</p>
                    {/if}
                    {if $list_dates}
                        <p>
                            <br /><br />
                            <strong>
                                <i class="fa fa-calendar mr-5"></i>
                                {"dates"|translate}
                            </strong>
                            <ul>
                                {foreach $list_dates AS $item_dates}
                                    <li>
                                        {$item_dates.date_start|my_date_format:"short"}
                                        {if $item_dates.date_end}
                                            / {$item_dates.date_end|my_date_format:"short"}
                                        {/if}
                                    </li>
                                {/foreach}
                            </ul>
                        </p>
                    {/if}
                </div>
            </div>
            <div class="row mt-30">
                <div class="col-md-12">
                    <div class="Speaker-address">
                        <div class="row">
                            <div class="col-sm-4">
                            </div>
                            <div class="col-sm-8">
                                <div class="media border-bottom p-15 mb-20 bg-light">
                                    <div class="media-body">
                                        <ul class="list-inline font-30 p-15">
                                            {if $data.link_social_website}<li class="pl-15 pr-15"><a href="{$data.link_social_website}" target="_blank"><i class="fa fa-globe text-theme-colored"></i></a></li>{/if}
                                            {if $data.link_social_facebook}<li class="pl-15 pr-15"><a href="{$data.link_social_facebook}" target="_blank"><i class="fa fa-facebook text-theme-colored"></i></a></li>{/if}
                                            {if $data.link_social_youtube}<li class="pl-15 pr-15"><a href="{$data.link_social_youtube}" target="_blank"><i class="fa fa-youtube text-theme-colored"></i></a></li>{/if}
                                            {if $data.link_social_twitter}<li class="pl-15 pr-15"><a href="{$data.link_social_twitter}" target="_blank"><i class="fa fa-twitter text-theme-colored"></i></a></li>{/if}
                                            {if $data.link_social_instagram}<li class="pl-15 pr-15"><a href="{$data.link_social_instagram}" target="_blank"><i class="fa fa-instagram text-theme-colored"></i></a></li>{/if}
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>





