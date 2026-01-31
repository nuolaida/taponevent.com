{* -- Top images -- *}
<section id="home" class="divider">
    <div class="container-fluid p-0">

        <div class="rev_slider_wrapper">
            <div class="rev_slider" data-version="5.0">
                <ul>
                    {foreach $block_slider.photos as $key => $gallery_id}
                        <li data-index="rs-{$key+1}" data-transition="slidingoverlayhorizontal" data-slotamount="default" data-easein="default" data-easeout="default" data-masterspeed="default" data-thumb="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop1920x1281"|make_url}" data-rotate="0" data-saveperformance="off" data-title="" data-description="">
                            <img src="{"/index.php?module=images&action=show&id=`$gallery_id`&type=crop1920x1281"|make_url}"  alt=""  data-bgposition="center center" data-bgfit="cover" data-bgrepeat="no-repeat" class="rev-slidebg" data-bgparallax="6" data-no-retina>
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
                                 style="z-index: 7; white-space: nowrap; font-weight:600;">{$block_slider.texts.0}
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
                                 style="z-index: 5; white-space: nowrap; font-weight:600;">{$block_slider.texts.1}
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
                                 style="z-index: 5; white-space: nowrap; font-weight:600;">{$block_slider.texts.2}
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
