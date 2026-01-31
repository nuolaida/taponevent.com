
{* -- Top images -- *}
<section id="home" class="divider">
    <div class="container-fluid p-0">

        <div class="rev_slider_wrapper">
            <div class="rev_slider" data-version="5.0">
                <ul>
                    {foreach $list_topimages as $key => $item}
                        <li data-index="rs-{$key+1}" data-transition="slidingoverlayhorizontal" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="default" data-thumb="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop1920x1281"|make_url}" data-rotate="0" data-saveperformance="off" data-title="" data-description="">
                            <img src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop1920x1281"|make_url}"  alt=""  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-bgparallax="6" data-no-retina>
                            <div class="tp-caption tp-resizeme text-theme-colored"
                                 id="rs-{$key+1}-layer-1"
                                 data-x="['center']"
                                 data-hoffset="['0']"
                                 data-y="['middle']"
                                 data-voffset="['-115']"
                                 data-fontsize="['30']"
                                 data-lineheight="['70']"
                                 data-width="none"
                                 data-height="none"
                                 data-whitespace="nowrap"
                                 data-transform_idle="o:1;s:500"
                                 data-transform_in="y:100;scaleX:1;scaleY:1;opacity:0;"
                                 data-transform_out="x:left(R);s:1000;e:Power3.easeIn;s:1000;e:Power3.easeIn;"
                                 data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                                 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"
                                 data-start="1000"
                                 data-splitin="none"
                                 data-splitout="none"
                                 data-responsive_offset="on"
                                 style="z-index: 7; white-space: nowrap; font-weight:600;">{$item.varchar_title_1}
                            </div>
                            <div class="tp-caption tp-resizeme text-center text-white font-montserrat"
                                 id="rs-{$key+1}-layer-2"
                                 data-x="['center']"
                                 data-hoffset="['0']"
                                 data-y="['middle']"
                                 data-voffset="['-40'']"
                                 data-fontsize="['72','72','54','24']"
                                 data-lineheight="['90']"
                                 data-width="none"
                                 data-height="none"
                                 data-whitespace="nowrap"
                                 data-transform_idle="o:1;s:500"
                                 data-transform_in="y:100;scaleX:1;scaleY:1;opacity:0;"
                                 data-transform_out="x:left(R);s:1000;e:Power3.easeIn;s:1000;e:Power3.easeIn;"
                                 data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                                 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"
                                 data-start="1400"
                                 data-splitin="none"
                                 data-splitout="none"
                                 data-responsive_offset="on"
                                 style="z-index: 5; white-space: nowrap; font-weight:600;">{$item.varchar_title_2}
                            </div>
                            <div class="tp-caption tp-resizeme text-center text-white"
                                 id="rs-{$key+1}-layer-3"
                                 data-x="['center']"
                                 data-hoffset="['0']"
                                 data-y="['middle']"
                                 data-voffset="['40']"
                                 data-fontsize="['24']"
                                 data-lineheight="['36']"
                                 data-width="none"
                                 data-height="none"
                                 data-whitespace="nowrap"
                                 data-transform_idle="o:1;s:500"
                                 data-transform_in="y:100;scaleX:1;scaleY:1;opacity:0;"
                                 data-transform_out="x:left(R);s:1000;e:Power3.easeIn;s:1000;e:Power3.easeIn;"
                                 data-mask_in="x:0px;y:0px;s:inherit;e:inherit;"
                                 data-mask_out="x:inherit;y:inherit;s:inherit;e:inherit;"
                                 data-start="1800"
                                 data-splitin="none"
                                 data-splitout="none"
                                 data-responsive_offset="on"
                                 style="z-index: 5; white-space: nowrap; font-weight:600;">{$item.varchar_title_3}
                            </div>
                        </li>
                    {/foreach}

                </ul>
            </div>
        </div>
        <script>
            $(document).ready(function(e) {
                var revapi = $(".rev_slider").revolution({
                    sliderType:"standard",
                    jsFileLocation: "/web/js/revolution-slider/js/",
                    sliderLayout: "fullscreen",
                    dottedOverlay: "none",
                    delay: 5000,
                    navigation: {
                        keyboardNavigation: "off",
                        keyboard_direction: "horizontal",
                        mouseScrollNavigation: "off",
                        onHoverStop: "off",
                        touch: {
                            touchenabled: "on",
                            swipe_threshold: 75,
                            swipe_min_touches: 1,
                            swipe_direction: "horizontal",
                            drag_block_vertical: false
                        },
                        arrows: {
                            style: "gyges",
                            enable: true,
                            hide_onmobile: false,
                            hide_onleave: true,
                            hide_delay: 200,
                            hide_delay_mobile: 1200,
                            tmp: '',
                            left: {
                                h_align: "left",
                                v_align: "center",
                                h_offset: 0,
                                v_offset: 0
                            },
                            right: {
                                h_align: "right",
                                v_align: "center",
                                h_offset: 0,
                                v_offset: 0
                            }
                        },
                        bullets: {
                            enable: true,
                            hide_onmobile: true,
                            hide_under: 800,
                            style: "hebe",
                            hide_onleave: false,
                            direction: "horizontal",
                            h_align: "center",
                            v_align: "bottom",
                            h_offset: 0,
                            v_offset: 30,
                            space: 5,
                            tmp: '<span class="tp-bullet-image"></span><span class="tp-bullet-imageoverlay"></span><span class="tp-bullet-title"></span>'
                        }
                    },
                    responsiveLevels: [1240, 1024, 778],
                    visibilityLevels: [1240, 1024, 778],
                    gridwidth: [1170, 1024, 778, 480],
                    gridheight: [700, 768, 960, 720],
                    lazyType: "none",
                    parallax:"mouse",
                    parallaxBgFreeze:"off",
                    parallaxLevels:[2,3,4,5,6,7,8,9,10,1],
                    shadow: 0,
                    spinner: "off",
                    stopLoop: "on",
                    stopAfterLoops: 0,
                    stopAtSlide: -1,
                    shuffle: "off",
                    autoHeight: "off",
                    fullScreenAutoWidth: "off",
                    fullScreenAlignForce: "off",
                    fullScreenOffsetContainer: "",
                    fullScreenOffset: "0",
                    hideThumbsOnMobile: "off",
                    hideSliderAtLimit: 0,
                    hideCaptionAtLimit: 0,
                    hideAllCaptionAtLilmit: 0,
                    debugMode: false,
                    fallbacks: {
                        simplifyAll: "off",
                        nextSlideOnWindowFocus: "off",
                        disableFocusListener: false,
                    }
                });
            });
        </script>
    </div>
</section>





<!-- Section: About -->
<section id="about">
    <div class="container pt-0">
        <div class="row mb-60">
            <div class="col-md-12">
                <div class="divider layer-overlay overlay-deep" data-margin-top="-165px" data-bg-img="">
                    <div class="col-md-12 col-lg-8 p-50 pl-md-0 pr-0 md-text-center">
                        <h2 class="text-black-444">{"meet up in vaf"|translate}</h2>
                        <ul class="list-inline xs-list-inline-none mt-50">
                            {foreach $list_meetup as $key => $item}
                                <li{if $key} class="ml-30"{/if}>
                                    <h4 class="text-gray-light"><i class="fa {$item.icon} text-theme-colored"></i> {$item.varchar_title}</h4>
                                    <h6 class="text-gray">{$item.rec_number} {$item.varchar_description}</h6>
                                </li>
                            {/foreach}
                        </ul>
                    </div>
                    {*
                    <div class="col-md-12 col-lg-4 pr-0 pl-md-0">
                        <div id="" class="countdown-timer timer-box bg-theme-colored" data-endingdate="2018/02/15">sdasasdasd fdfsdf sdf sdf sdf sdf sdf df sdf sd fdffg dfgdfg df gdf gdfg dfg df gdfg df gdf gdfg df gd</div>
                    </div>
                    *}
                </div>
            </div>
        </div>
        {if $list_about}
            {foreach $list_about as $key => $item}
                <div class="row">
                    <div class="col-md-6 mb-sm-30">
                        <h2 class="title font-40 text-gray mt-0 mb-20">{$item.varchar_title}</h2>
                        {$item.text_description}
                        {if $item.varchar_button_title}
                            <a class="btn btn-colored btn-theme-colored btn-flat btn-lg text-uppercase smooth-scroll font-13 mt-30" href="{$item.button_link}">{$item.varchar_button_title}</a>
                        {/if}
                        {* <a class="btn btn-colored btn-dark-light btn-flat btn-lg text-uppercase smooth-scroll font-13 mt-30" href="#registration-form">Register Now</a> *}
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 mb-10">
                                <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=`$item.gallery_id_1`&type=crop590x230"|make_url}" alt="">
                            </div>
                            <div class="col-xs-6 col-md-6 pr-5">
                                <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=`$item.gallery_id_2`&type=crop280x230"|make_url}" alt="">
                            </div>
                            <div class="col-xs-6 col-md-6 pl-5">
                                <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=`$item.gallery_id_3`&type=crop280x230"|make_url}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
</section>



{if $list_conference}
    <!-- Section: Schedule -->
    <section id="schedule">
        <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5">
            <div class="container pt-50 pb-50">
                <div class="section-title">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <h2 class="title text-white mb-0">{"conference"|translate}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="layer-overlay overlay-light" data-bg-img="{"/index.php?module=images&action=show&id=5&type=crop1920x1280"|make_url}">
            <div class="container pt-80 pb-50">
                <div class="section-content">
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-schedule">
                                <thead>
                                <tr>
                                    <th style="width: 20%;">{"time"|translate}</th>
                                    <th style="width: 55%;">{"schedule"|translate}</th>
                                    <th style="width: 25%;">{"language"|translate}</th>
                                </tr>
                                </thead>
                                <tbody>
                                {foreach $list_conference as $item}
                                    <tr>
                                        <td>{$item.rec_time|my_date_format:"time_middle"}</td>
                                        <td>
                                            {if $item.speaker_id}
                                                {if $item.speaker_gallery_id}<img class="img-circle speaker-thumb" src="{"/index.php?module=images&action=show&id=`$item.speaker_gallery_id`&type=crop200x200"|make_url}" alt="">{/if}
                                                <h5 class="title">{$item.conference_title}</h5>
                                                <h6 class="name">{$item.speaker_name}{if $item.speaker_company}, <small>{$item.speaker_company}</small>{/if}</h6>
                                            {else}
                                                <strong>{$item.conference_title}</strong>
                                            {/if}
                                        </td>
                                        <td>{if $item.conference_language}{$item.conference_language}{/if}</td>
                                    </tr>
                                {/foreach}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}



{if $list_breweries}
    <section id="breweries">
        <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="">
            <div class="container pt-50 pb-50">
                <div class="section-title">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <h2 class="title text-white mb-0">{"breweries"|translate}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white-light">
            <div class="container pt-80 pb-80">
                <div class="section-content">
                    <div class="row multi-row-clearfix">
                        {foreach $list_breweries as $item}
                            <div class="col-xs-12 col-sm-6 col-md-3 mb-30">
                                <div class="team-member clearfix">
                                    <div class="team-thumb"><img alt="" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop400x400"|make_url}" class="img-fullwidth"></div>
                                    <div class="overlay">
                                        <div class="content text-center">
                                            <h4 class="author mb-0"><a href="{"/index.php?module=breweries&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{$item.title}</a></h4>
                                            <h6 class="title text-gray font-14 mt-5 mb-15">{$item.country_title}</h6>
                                        </div>
                                    </div>
                                    <ul class="social-icons flat icon-white square mt-10">
                                        <li class=""><a href="{"/index.php?module=breweries&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{"read more"|translate}</a></li>
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}




{if $list_participants}
    <section id="participants">
        <div class="divider parallax layer-overlay overlay-darkblue" data-stellar-background-ratio="0.5" data-bg-img="">
            <div class="container pt-50 pb-50">
                <div class="section-title">
                    <div class="row">
                        <div class="col-md-6 col-md-offset-3 text-center">
                            <h2 class="title text-white mb-0">{"participants other"|translate}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white-light">
            <div class="container pt-80 pb-80">
                <div class="section-content">
                    <div class="row multi-row-clearfix">
                        {foreach $list_participants as $item}
                            <div class="col-xs-12 col-sm-6 col-md-3 mb-30">
                                <div class="team-member clearfix">
                                    <div class="team-thumb"><img alt="" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop400x400"|make_url}" class="img-fullwidth"></div>
                                    <div class="overlay">
                                        <div class="content text-center">
                                            <h4 class="author mb-0"><a href="{"/index.php?module=participants&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{$item.title}</a></h4>
                                            <h6 class="title text-gray font-14 mt-5 mb-15">{$item.description_short}</h6>
                                        </div>
                                    </div>
                                    <ul class="social-icons flat icon-white square mt-10">
                                        <li class=""><a href="{"/index.php?module=participants&action=show&id=`$item.id`&rec_url_name=`$item.title`"|make_url}">{"read more"|translate}</a></li>
                                    </ul>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}



{if $list_meetup}
    <section class="divider parallax layer-overlay overlay-darkblue" data-bg-img="">
        <div class="container">
            <div class="row">
                {foreach $list_meetup as $key => $item}
                    <div class="col-xs-12 col-sm-6 col-md-3 mb-md-50">
                        <div class="funfact pt-15 pr-40 pb-15 pl-20">
                            <i class="{$item.icon} text-black-light mt-5 font-48 pull-left flip" data-text-color="#ccc"></i>
                            <h2 class="animate-number text-red mt-0 font-48 pull-right flip" data-value="{$item.rec_number}" data-animation-duration="2000">0</h2>
                            <div class="clearfix"></div>
                            <h4 class="text-uppercase text-right flip font-14" data-text-color="rgba(255,255,255,0.3)">{$item.varchar_title}</h4>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    </section>
{/if}



{if $list_tickets}
    <section id="tickets" class="divider parallax layer-overlay overlay-deep" data-stellar-background-ratio="0.5" data-bg-img="{"/index.php?module=images&action=show&id=9&type=crop1920x1280"|make_url}">
        <div class="container pb-50">
            <div class="section-title mb-30">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <h2 class="title text-theme-colored">{"tickets"|translate}</h2>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row equal-height-pricing-table">
                    {foreach $list_tickets as $item}
                        <div class="col-xs-12 col-sm-6 col-md-6 mb-30">
                            <div class="pricing-table table-horizontal maxwidth400">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="table-price text-white">{if $item.price}{$item.price}€{/if}</div>
                                        <h6 class="table-title text-theme-colored">{$item.varchar_title}</h6>
                                    </div>
                                    <div class="col-sm-6">
                                        <p>{$item.text_description}</p>
                                        {if $item.link}<a class="btn btn-colored btn-theme-colored btn-sm mt-15 pl-20 pr-20" href="{$item.link}" target="_blank">{"buy ticket"|translate}</a>{/if}
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </section>
{/if}



{if $list_sponsors}
    <section id="sponsors" class="divider parallax layer-overlay overlay-deep" data-stellar-background-ratio="0.5" data-bg-img="">
        <div class="container pt-70">
            <div class="section-title">
                <div class="row">
                    <div class="col-md-6 col-md-offset-3 text-center">
                        <h2 class="title text-theme-colored">{"sponsors"|translate}</h2>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="row sponsors-style2 pt-30 pb-20">
                            {foreach $list_sponsors as $item}
                                <div class="col-xs-6 col-md-3 text-center">
                                    <a href="{$item.link}" target="_blank"><img src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop210x210"|make_url}" alt=""></a>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}




{if $list_gallery}
    <section>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div id="grid" class="portfolio-gallery grid-4 gutter clearfix">
                        {foreach $list_gallery as $item}
                            <div class="portfolio-item photography">
                                <div class="thumb">
                                    <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop380x250"|make_url}" alt="project">
                                    <div class="overlay-shade"></div>
                                    <div class="icons-holder">
                                        <div class="icons-holder-inner">
                                            <div class="social-icons icon-sm icon-dark icon-circled icon-theme-colored">
                                                <a href="#"><i class="fa fa-plus"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <a class="hover-link" data-lightbox="image" href="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=any1200x800"|make_url}">View more</a>
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}



{if $list_place}
    <section id="place" class="divider">
        <div class="container pt-60 pb-60">
            <div class="section-title mb-60">
                <div class="row">
                    <div class="col-md-12">
                        <div class="esc-heading small-border text-center">
                            <h3>{"place"|translate}</h3>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-content">
                <div class="row">
                    {foreach $list_place as $item}
                        <div class="col-sm-12 col-md-4">
                            <div class="contact-info text-center">
                                <i class="fa {$item.icon} font-36 mb-10 text-theme-colored"></i>
                                <h4>{$item.varchar_title}</h4>
                                <h6 class="text-gray">{$item.varchar_description}</h6>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
    </section>
{/if}


{if $list_map}
    {foreach $list_map as $item}
        {if $item.latitude && $item.longitude && $page_google_api_key}
            <section>
                <div class="container-fluid p-0">
                    <div
                            data-address="{$item.varchar_address}"
                            data-popupstring-id="#popupstring1"
                            class="map-canvas autoload-map bg-theme-colored"
                            data-mapstyle="style1"
                            data-height="420"
                            data-latlng="{$item.latitude},{$item.longitude}"
                            data-title="{$item.varchar_address}"
                            data-zoom="15"
                            data-marker="/themes/default/images/map-icon-blue.png">
                    </div>
                    <div class="map-popupstring hidden" id="popupstring1">
                        <div class="text-center">
                            <script src="https://maps.google.com/maps/api/js?key={$page_google_api_key}"></script>
                            <script src="/web/js/google-map-init.js"></script>
                        </div>
                    </div>
                </div>
            </section>
        {/if}
    {/foreach}
{/if}