
{include file="blocks/slider_header.tpl"}


<!-- Section: About -->
<section id="about">
    <div class="container pt-0">
        <div class="row mb-60">
            <div class="col-md-12">
                <div class="divider layer-overlay overlay-deep" data-margin-top="-165px" data-bg-img="">
                    <div class="col-md-12 col-lg-8 p-50 pl-md-0 pr-0 md-text-center">
                        <h2 class="text-black-444">{"about"|translate}</h2>
                    </div>
                </div>
            </div>
        </div>
                <div class="row">
                    <div class="col-md-6 mb-sm-30">
                        <h2 class="title font-40 text-gray mt-0 mb-20">Lietuvos alaus someljė čempionatas</h2>

                        <p>Lietuvos someljė asociacija kartus su Vilniaus alaus festivaliu rengė pirmąjį Lietuvos alaus someljė čempionatą.</p>
                        <p>Varžybos buvo iš dviejų etapų. Pirmame etape dalyviai rašė teorinį testą susijusį su alumi
                            (klausimai apie alaus gamybą, gamintojus, personalijas, istoriją ir kt.), degustavo aklai
                            ir aprašinėjo alų, atliko alaus pateikimo rungtis. Pirmasis etapas vyko už uždarų durų.
                            Po pirmojo etapo buvo atrinkti 4 geriausieji, kurie rungėsi finale. Finalinis etapas buvo viešas.
                            Finale: aklos alaus degustacijos, įvairios serviso rungtys, alaus ir maisto derinimas, teorinės rungtys.
                            Geriausiai pasirodęs dalyvis paskelbtas Lietuvos alaus someljė čempionu.</p>
                        <p>Čempionatas vyko anglų kalba.</p>
                        <p>Dalyvauti galėjo visi Lietuvos piliečiai, norintys išbandyti save ir turintys žinių apie alaus gamybą, istoriją, pateikimą, išmanantys alaus stilius ir gebantys aklai ragauti bei apibūdinti alų.</p>
                        <p>Lietuvos alaus čempionatas vyko 2023 lapkričio 24-25 dienomis VAF alaus festivalio metu, Švitrigailos g. 29, Loftas, Vilnius.</p>
                        <br /><br />
                        <h4>Finalistai</h4>
                        <ul>
                            <li>Mindaugas Burneika</li>
                            <li>Tomas Josas</li>
                            <li>Jonaistė Jusionytė</li>
                            <li>Tomas Savickis</li>
                        </ul>

                        <br />
                        <h4>Čempionas</h4>
                        <ul>
                            <li>Tomas Josas</li>
                        </ul>

                        <br />
                        <h4>Kiti čempionatai</h4>
                        <ul>
                            {foreach $config_valid_ids as $item}
                                {if $item != $config_active_id}
                                    <li><a href="{"/index.php?module=sommelier&action=show&id=`$item`"|make_url}">{$item}</a></li>
                                {/if}
                            {/foreach}
                        </ul>

                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12 mb-10">
                                <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=401&type=crop590x230"|make_url}" alt="">
                            </div>
                            <div class="col-xs-6 col-md-6 pr-5">
                                <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=404&type=crop280x230"|make_url}" alt="">
                            </div>
                            <div class="col-xs-6 col-md-6 pl-5">
                                <img class="img-fullwidth" src="{"/index.php?module=images&action=show&id=399&type=crop280x230"|make_url}" alt="">
                            </div>
                        </div>
                    </div>
                </div>
    </div>
</section>





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
                                    <a href="{$item.link}" target="_blank"><img src="{"/index.php?module=images&action=show&id=`$item.gallery_id`&type=crop210x140"|make_url}" alt=""></a>
                                </div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
{/if}


{if $block_gallery}
    {include file="blocks/photo_gallery.tpl"}
{/if}

